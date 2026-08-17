import logging
from datetime import datetime
from pytrends.request import TrendReq
from utils.db import DatabaseConnection
from utils.job_logging import log_job_run

logger = logging.getLogger(__name__)


def detect_trends() -> dict:
    """
    Runs at 5 AM daily. Fetches UK-wide trending searches, stores the top N
    in trend_topics for content_generator.py to pick up at 6 AM.

    Deliberately industry-agnostic here — filtering to what's relevant per
    vertical happens in content_generator.py, where each business's config
    is already loaded. Keeping that logic in one place avoids two jobs
    disagreeing about what counts as "relevant to care" vs "relevant to
    cleaning".
    """
    logger.info("Starting trend detection...")
    start = datetime.now()
    inserted = 0

    try:
        pytrends = TrendReq(hl="en-GB", tz=0)
        pytrends.get_trending_searches(pn="gb")
        trending = pytrends.trending_searches(pn="gb")
        keywords = [str(k).strip() for k in trending[0].tolist()[:15]]

        with DatabaseConnection() as db:
            cursor = db.cursor()

            for keyword in keywords:
                cursor.execute(
                    """
                    SELECT id FROM trend_topics
                    WHERE keyword = %s AND detected_date = %s
                    """,
                    (keyword, datetime.now().date()),
                )

                if cursor.fetchone() is None:
                    cursor.execute(
                        """
                        INSERT INTO trend_topics (keyword, source, detected_date, volume, used_count, created_at, updated_at)
                        VALUES (%s, %s, %s, %s, 0, %s, %s)
                        """,
                        (keyword, "google_trends", datetime.now().date(), 100, datetime.now(), datetime.now()),
                    )
                    inserted += 1

            db.commit()

        elapsed = (datetime.now() - start).total_seconds()
        logger.info(f"Trend detection complete — inserted {inserted} new trends in {elapsed:.1f}s")
        log_job_run("trend_detector", "success", rows_processed=inserted)
        return {"success": True, "inserted": inserted}

    except Exception as e:
        logger.error(f"Trend detection failed: {e}")
        log_job_run("trend_detector", "failed", error_message=str(e))
        # Deliberately swallowed, not re-raised — content_generator.py falls
        # back to yesterday's trends if today's detection produced nothing.
        # See its fetch_trends() for that fallback.
        return {"success": False, "error": str(e)}
