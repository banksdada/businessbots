import logging
import os
import uuid
import requests
from datetime import datetime, timedelta
from openai import OpenAI
from config import config
from utils.db import DatabaseConnection
from utils.job_logging import log_job_run

logger = logging.getLogger(__name__)
client = OpenAI(api_key=config.OPENAI_API_KEY, base_url=config.AI_BASE_URL)


def generate_content() -> dict:
    """
    Runs at 6 AM daily. For every active, onboarded business: fetch today's
    trends (or yesterday's, if today's detection failed), resolve the
    business's vertical config (same care/cleaning/real_estate/... shape as
    App\\Models\\BusinessVertical::resolvedConfig() on the PHP side), and
    generate 3 vertical-appropriate posts — caption, hashtags, AND a
    downloaded-and-persisted image (Instagram's API requires one; a
    caption-only post fails at publish time). Posts without a usable image
    are skipped rather than created broken.

    This is the piece that makes ONE AI system work across 8 different
    industries — the prompt changes per business, the pipeline doesn't.
    """
    logger.info("Starting content generation...")
    start = datetime.now()
    posts_created = 0
    businesses_processed = 0

    try:
        with DatabaseConnection() as db:
            cursor = db.cursor(dict_rows=True)

            businesses = _fetch_active_businesses(cursor)
            trends = _fetch_trends(cursor)

            if not trends:
                logger.warning("No trends available (today or yesterday) — skipping content generation")
                log_job_run("content_generator", "skipped", error_message="no trends available")
                return {"success": False, "reason": "no_trends"}

            for business in businesses:
                vertical_config = _resolve_vertical_config(cursor, business["id"])

                for trend in trends[:3]:
                    try:
                        caption, hashtags = _generate_post(business, vertical_config, trend)
                        media_url = _generate_image(business, vertical_config, trend)

                        if not media_url:
                            # Instagram's API rejects feed posts with no image —
                            # skip rather than create a post that will fail at
                            # publish time (see SETUP-NOTES.md). LinkedIn/GBP
                            # don't require media, but for MVP simplicity every
                            # post generated here targets Instagram, so a failed
                            # image means a skipped post rather than a broken one.
                            logger.warning(f"Skipping post for business {business['id']} — image generation failed")
                            continue

                        _store_post(cursor, business["id"], caption, hashtags, media_url)
                        posts_created += 1
                    except Exception as e:
                        # One failed post for one business must never stop the
                        # loop for the other 99 businesses — log and continue.
                        logger.error(f"Post generation failed for business {business['id']}: {e}")

                businesses_processed += 1

            db.commit()

        elapsed = (datetime.now() - start).total_seconds()
        logger.info(
            f"Content generation complete — {posts_created} posts across "
            f"{businesses_processed} businesses in {elapsed:.1f}s"
        )
        log_job_run("content_generator", "success", rows_processed=posts_created)
        return {"success": True, "posts_created": posts_created, "businesses": businesses_processed}

    except Exception as e:
        logger.error(f"Content generation failed: {e}")
        log_job_run("content_generator", "failed", error_message=str(e))
        return {"success": False, "error": str(e)}


def _fetch_active_businesses(cursor) -> list[dict]:
    cursor.execute(
        """
        SELECT id, name, location FROM businesses
        WHERE is_active = true
        """
    )
    return cursor.fetchall()


def _fetch_trends(cursor) -> list[str]:
    """Today's trends, falling back to yesterday's if detection failed overnight."""
    cursor.execute(
        "SELECT keyword FROM trend_topics WHERE detected_date = %s ORDER BY volume DESC",
        (datetime.now().date(),),
    )
    rows = cursor.fetchall()

    if not rows:
        yesterday = (datetime.now() - timedelta(days=1)).date()
        cursor.execute(
            "SELECT keyword FROM trend_topics WHERE detected_date = %s ORDER BY volume DESC",
            (yesterday,),
        )
        rows = cursor.fetchall()

    return [row["keyword"] for row in rows]


def _resolve_vertical_config(cursor, business_id: int) -> dict:
    """
    Mirrors BusinessVertical::resolvedConfig() on the PHP side: the
    business's own industry_config overrides merged on top of the shared
    vertical_configs defaults. Two implementations of the same merge because
    Python and PHP don't share code — if this logic ever changes, it must
    change in BOTH places. See code-standards.md for that flagged duplication.
    """
    cursor.execute(
        """
        SELECT bv.vertical_type, bv.industry_config, vc.label, vc.default_tone,
               vc.default_topics, vc.default_hashtags, vc.lead_questions
        FROM business_verticals bv
        LEFT JOIN vertical_configs vc ON vc.vertical_type = bv.vertical_type
        WHERE bv.business_id = %s
        """,
        (business_id,),
    )
    row = cursor.fetchone()

    if not row:
        return {"label": "small business", "default_tone": "friendly, professional"}

    overrides = row.get("industry_config") or {}
    merged = {
        "label": row["label"],
        "default_tone": row["default_tone"],
        "default_topics": row["default_topics"] or [],
        "default_hashtags": row["default_hashtags"] or [],
    }
    merged.update({k: v for k, v in overrides.items() if v})
    return merged


def _generate_post(business: dict, vertical_config: dict, trend: str) -> tuple[str, str]:
    system_prompt = (
        f"You are a social media expert for {vertical_config.get('label', 'a small business')}. "
        f"Write Instagram captions (150-200 chars) that are {vertical_config.get('default_tone', 'professional')}. "
        f"Include a clear call to action and 10-15 relevant hashtags at the end, "
        f"separated from the caption by a line break."
    )
    user_prompt = (
        f'Generate an Instagram post about "{trend}" for {business["name"]}, '
        f'a {vertical_config.get("label", "business")} in {business["location"]}. '
        f"Make it relevant to their audience — don't force a connection to the trend if it's a stretch, "
        f"pick a genuinely relevant angle instead."
    )

    response = client.chat.completions.create(
        model=config.AI_MODEL_CONTENT,
        messages=[
            {"role": "system", "content": system_prompt},
            {"role": "user", "content": user_prompt},
        ],
        temperature=0.7,
        max_tokens=300,
    )

    content = response.choices[0].message.content.strip()
    parts = content.rsplit("#", 1)
    caption = parts[0].strip()
    hashtags = ("#" + parts[1].strip()) if len(parts) > 1 else ""

    return caption, hashtags


def _generate_image(business: dict, vertical_config: dict, trend: str) -> str | None:
    """
    Generates an image via DALL-E 3 and immediately downloads it to a shared
    volume served by Laravel's public/storage — NOT the raw OpenAI URL, which
    expires in ~2 hours. Posts publish up to an hour after generation via the
    hourly PostSchedulerJob, so the temporary URL isn't safe to store directly.

    Returns None on any failure — content_generator.py's caller skips the
    post entirely rather than create one that will fail at Instagram's
    publish step for lack of an image_url.
    """
    try:
        prompt = (
            f"A professional, warm photograph representing {vertical_config.get('label', 'a small business')} "
            f"related to \"{trend}\". No text or logos in the image. Natural lighting, realistic, "
            f"suitable for an Instagram business post."
        )

        response = client.images.generate(
            model="dall-e-3",
            prompt=prompt,
            size="1024x1024",
            quality="standard",
            n=1,
        )

        temp_url = response.data[0].url
        image_bytes = requests.get(temp_url, timeout=15).content

        os.makedirs(config.IMAGE_STORAGE_PATH, exist_ok=True)
        filename = f"{uuid.uuid4()}.png"
        filepath = os.path.join(config.IMAGE_STORAGE_PATH, filename)

        with open(filepath, "wb") as f:
            f.write(image_bytes)

        return f"{config.APP_URL}/storage/generated-posts/{filename}"

    except Exception as e:
        logger.error(f"Image generation failed for business {business['id']}: {e}")
        return None


def _store_post(cursor, business_id: int, caption: str, hashtags: str, media_url: str) -> None:
    cursor.execute(
        """
        INSERT INTO scheduled_posts
            (business_id, platform, caption, hashtags, media_url, scheduled_time, created_at, updated_at)
        VALUES (%s, %s, %s, %s, %s, NOW() + INTERVAL '2 hours', NOW(), NOW())
        """,
        (business_id, "instagram", caption, hashtags, media_url),
    )
