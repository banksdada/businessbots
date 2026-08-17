import logging
from datetime import datetime
from utils.db import DatabaseConnection

logger = logging.getLogger(__name__)


def log_job_run(
    job_name: str,
    status: str,
    rows_processed: int = 0,
    error_message: str | None = None,
    business_id: int | None = None,
) -> None:
    """
    Writes to agent_logs (see architecture.md monitoring section) — this is
    what a future admin "job health" screen reads from, and it's how a human
    finds out a job has been silently failing for three days instead of
    discovering it via a customer complaint.
    """
    try:
        with DatabaseConnection() as db:
            cursor = db.cursor()
            cursor.execute(
                """
                INSERT INTO agent_logs
                    (business_id, job_name, status, rows_processed, error_message, created_at)
                VALUES (%s, %s, %s, %s, %s, %s)
                """,
                (business_id, job_name, status, rows_processed, error_message, datetime.now()),
            )
            db.commit()
    except Exception as e:
        # Logging must never be the reason a job crashes — worst case, we lose
        # one observability row, not the actual work the job did.
        logger.error(f"Failed to write agent_logs entry for {job_name}: {e}")
