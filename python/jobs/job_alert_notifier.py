import logging
from datetime import datetime

import requests

from config import config
from utils.db import DatabaseConnection
from utils.job_logging import log_job_run
from utils.mailer import send_email

logger = logging.getLogger(__name__)

_TIMEOUT = 15


def notify_job_alerts() -> dict:
    """
    Runs every 30 minutes. Polls Flux's own /api/jobs (a separate,
    self-hosted app — see fluxjobs.baseuse.xyz) for postings scored at or
    above JOB_ALERT_SCORE_THRESHOLD — and, when JOB_ALERT_ROLE_FILTER is
    set, matching that specific role only (defaults to "business_analyst")
    — and emails any that haven't already been notified about.

    Flux itself has no outbound email capability (it's a local-first,
    dependency-light dashboard by design) — this job is what turns its
    scoring into an actual notification, using the same PyRunner
    infrastructure and SMTP config already running the other jobs here.

    "Already notified" is tracked in job_alert_notifications (this DB, not
    Flux's), keyed on Flux's own job id, so a job that's re-fetched on a
    later scan (Flux replaces stale copies of the same posting on every
    scan — see its scanner.py) is never re-emailed just because it showed
    up in the response again.
    """
    logger.info("Checking Flux for new job matches...")
    start = datetime.now()

    try:
        jobs = _fetch_matching_jobs()
    except Exception as e:
        logger.error(f"Could not reach Flux at {config.FLUX_API_URL}: {e}")
        log_job_run("job_alert_notifier", "failed", error_message=str(e))
        return {"success": False, "error": str(e)}

    try:
        with DatabaseConnection() as db:
            cursor = db.cursor(dict_rows=True)
            new_jobs = _filter_unnotified(cursor, jobs)

            if not new_jobs:
                logger.info("No new job matches above threshold.")
                log_job_run("job_alert_notifier", "success", rows_processed=0)
                return {"success": True, "notified": 0}

            _send_alert_email(new_jobs)
            _record_notified(cursor, new_jobs)
            db.commit()

        elapsed = (datetime.now() - start).total_seconds()
        logger.info(f"Job alert notifier complete — emailed {len(new_jobs)} new match(es) in {elapsed:.1f}s")
        log_job_run("job_alert_notifier", "success", rows_processed=len(new_jobs))
        return {"success": True, "notified": len(new_jobs)}

    except Exception as e:
        logger.error(f"Job alert notifier failed: {e}")
        log_job_run("job_alert_notifier", "failed", error_message=str(e))
        return {"success": False, "error": str(e)}


def _fetch_matching_jobs() -> list:
    response = requests.get(f"{config.FLUX_API_URL}/api/jobs", timeout=_TIMEOUT)
    response.raise_for_status()
    payload = response.json()

    return [
        job
        for job in payload.get("jobs", [])
        if job.get("match", {}).get("score", 0) >= config.JOB_ALERT_SCORE_THRESHOLD
        and (config.JOB_ALERT_ROLE_FILTER is None or job.get("role") == config.JOB_ALERT_ROLE_FILTER)
    ]


def _filter_unnotified(cursor, jobs: list) -> list:
    if not jobs:
        return []

    ids = [job["id"] for job in jobs]
    cursor.execute(
        "SELECT flux_job_id FROM job_alert_notifications WHERE flux_job_id = ANY(%s)",
        (ids,),
    )
    already_notified = {row["flux_job_id"] for row in cursor.fetchall()}
    return [job for job in jobs if job["id"] not in already_notified]


def _record_notified(cursor, jobs: list) -> None:
    now = datetime.now()
    for job in jobs:
        cursor.execute(
            """
            INSERT INTO job_alert_notifications
                (flux_job_id, title, company, score, notified_at, created_at, updated_at)
            VALUES (%s, %s, %s, %s, %s, %s, %s)
            ON CONFLICT (flux_job_id) DO NOTHING
            """,
            (
                job["id"],
                job.get("title", "Untitled role"),
                job.get("company", "Unknown"),
                job.get("match", {}).get("score", 0),
                now,
                now,
                now,
            ),
        )


def _send_alert_email(jobs: list) -> None:
    jobs_sorted = sorted(jobs, key=lambda j: j.get("match", {}).get("score", 0), reverse=True)
    role_label = f" {config.JOB_ALERT_ROLE_FILTER.replace('_', ' ')}" if config.JOB_ALERT_ROLE_FILTER else ""
    subject = f"Flux:{role_label} {len(jobs)} new job match{'es' if len(jobs) != 1 else ''}"

    rows_html = "".join(
        f"""
        <tr>
          <td style="padding:8px 12px;border-bottom:1px solid #e2e8f0;">
            <a href="{job.get('url', '#')}" style="color:#1d4ed8;text-decoration:none;font-weight:600;">
              {job.get('title', 'Untitled role')}
            </a><br>
            <span style="color:#64748b;font-size:13px;">{job.get('company', 'Unknown')} &middot; {job.get('location', '')}</span>
          </td>
          <td style="padding:8px 12px;border-bottom:1px solid #e2e8f0;text-align:right;font-weight:700;">
            {job.get('match', {}).get('score', 0)}% &middot; {job.get('match', {}).get('band', '')}
          </td>
        </tr>
        """
        for job in jobs_sorted
    )

    html_body = f"""
    <html><body style="font-family:sans-serif;color:#0f172a;">
      <h2>{len(jobs)} new match{'es' if len(jobs) != 1 else ''} on Flux</h2>
      <table style="width:100%;border-collapse:collapse;">{rows_html}</table>
      <p style="margin-top:16px;">
        <a href="{config.FLUX_API_URL}">Open Flux</a>
      </p>
    </body></html>
    """

    text_lines = [f"{len(jobs)} new job match(es) on Flux:", ""]
    for job in jobs_sorted:
        match = job.get("match", {})
        text_lines.append(
            f"- {job.get('title', 'Untitled role')} at {job.get('company', 'Unknown')} "
            f"({match.get('score', 0)}% {match.get('band', '')}) — {job.get('url', '')}"
        )
    text_lines.append("")
    text_lines.append(config.FLUX_API_URL)

    send_email(
        to_address=config.JOB_ALERT_EMAIL_TO,
        subject=subject,
        html_body=html_body,
        text_body="\n".join(text_lines),
    )
