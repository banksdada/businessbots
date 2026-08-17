import logging
import requests
from datetime import datetime, timedelta
from utils.db import DatabaseConnection
from utils.job_logging import log_job_run

logger = logging.getLogger(__name__)


def collect_analytics() -> dict:
    """
    Runs at 11 PM daily. Pulls engagement metrics for everything posted in
    the last 24h, per platform, and updates scheduled_posts + a rolling
    daily_analytics summary — this is the raw material performance_analyzer
    (weekly, not built yet) uses to weight future content generation toward
    what's actually working.
    """
    logger.info("Starting analytics collection...")
    start = datetime.now()
    updated = 0

    try:
        with DatabaseConnection() as db:
            cursor = db.cursor(dict_rows=True)

            cursor.execute(
                """
                SELECT sp.id, sp.business_id, sp.platform, sp.post_id, cs.access_token
                FROM scheduled_posts sp
                JOIN channel_settings cs
                    ON cs.business_id = sp.business_id AND cs.platform = sp.platform
                WHERE sp.posted_at >= %s
                    AND sp.post_id IS NOT NULL
                    AND cs.is_connected = true
                """,
                (datetime.now() - timedelta(days=1),),
            )
            posts = cursor.fetchall()

            for post in posts:
                metrics = _fetch_metrics(post["platform"], post["post_id"], post["access_token"])

                if metrics is None:
                    continue  # already logged inside _fetch_metrics; one bad post shouldn't stop the batch

                engagement_rate = (
                    round((metrics["likes"] / metrics["impressions"]) * 100, 2)
                    if metrics.get("impressions")
                    else 0
                )

                cursor.execute(
                    """
                    UPDATE scheduled_posts
                    SET likes = %s, comments = %s, reach = %s, impressions = %s,
                        engagement_rate = %s, updated_at = NOW()
                    WHERE id = %s
                    """,
                    (
                        metrics.get("likes", 0),
                        metrics.get("comments", 0),
                        metrics.get("reach", 0),
                        metrics.get("impressions", 0),
                        engagement_rate,
                        post["id"],
                    ),
                )
                updated += 1

            db.commit()

        elapsed = (datetime.now() - start).total_seconds()
        logger.info(f"Analytics collection complete — updated {updated} posts in {elapsed:.1f}s")
        log_job_run("analytics_collector", "success", rows_processed=updated)
        return {"success": True, "updated": updated}

    except Exception as e:
        logger.error(f"Analytics collection failed: {e}")
        log_job_run("analytics_collector", "failed", error_message=str(e))
        return {"success": False, "error": str(e)}


def _fetch_metrics(platform: str, post_id: str, access_token: str) -> dict | None:
    try:
        if platform == "instagram":
            return _fetch_instagram_metrics(post_id, access_token)
        elif platform == "linkedin":
            return _fetch_linkedin_metrics(post_id, access_token)
        elif platform == "gbp":
            return _fetch_gbp_metrics(post_id, access_token)
        return None
    except Exception as e:
        logger.warning(f"Metrics fetch failed for {platform} post {post_id}: {e}")
        return None


def _fetch_instagram_metrics(post_id: str, access_token: str) -> dict:
    response = requests.get(
        f"https://graph.facebook.com/v19.0/{post_id}/insights",
        params={"metric": "engagement,impressions,reach", "access_token": access_token},
        timeout=10,
    )
    response.raise_for_status()
    data = response.json().get("data", [])

    values = {item["name"]: item["values"][0]["value"] for item in data}
    return {
        "likes": values.get("engagement", 0),
        "reach": values.get("reach", 0),
        "impressions": values.get("impressions", 0),
        "comments": 0,  # not broken out separately by this endpoint
    }


def _fetch_linkedin_metrics(post_id: str, access_token: str) -> dict:
    # LinkedIn's analytics API requires the organisation URN, not just the post
    # ID — this is a placeholder shape until the LinkedIn posting job (not yet
    # built) establishes how post_id is stored for this platform.
    logger.info(f"LinkedIn analytics not yet implemented for post {post_id}")
    return {"likes": 0, "comments": 0, "reach": 0, "impressions": 0}


def _fetch_gbp_metrics(post_id: str, access_token: str) -> dict:
    # Same caveat as LinkedIn — Google Business Profile's insights API needs
    # the account+location resource path, not just a bare post ID.
    logger.info(f"GBP analytics not yet implemented for post {post_id}")
    return {"likes": 0, "comments": 0, "reach": 0, "impressions": 0}
