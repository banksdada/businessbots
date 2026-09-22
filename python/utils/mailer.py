import logging
import smtplib
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText

from config import config

logger = logging.getLogger(__name__)


def send_email(to_address: str, subject: str, html_body: str, text_body: str | None = None) -> None:
    """
    Sends one email via the same MAIL_* SMTP settings Laravel's mailer reads
    from .env — no separate credentials to manage for Python jobs.

    Raises on failure rather than swallowing the error: callers (like
    job_alert_notifier) decide whether a failed send should count as a
    failed job run.
    """
    message = MIMEMultipart("alternative")
    message["Subject"] = subject
    message["From"] = f"{config.MAIL_FROM_NAME} <{config.MAIL_FROM_ADDRESS}>"
    message["To"] = to_address

    message.attach(MIMEText(text_body or _strip_html(html_body), "plain"))
    message.attach(MIMEText(html_body, "html"))

    if config.MAIL_ENCRYPTION == "ssl":
        smtp_cls = smtplib.SMTP_SSL
    else:
        smtp_cls = smtplib.SMTP

    with smtp_cls(config.MAIL_HOST, config.MAIL_PORT, timeout=15) as smtp:
        if config.MAIL_ENCRYPTION == "tls":
            smtp.starttls()
        if config.MAIL_USERNAME and config.MAIL_PASSWORD:
            smtp.login(config.MAIL_USERNAME, config.MAIL_PASSWORD)
        smtp.sendmail(config.MAIL_FROM_ADDRESS, [to_address], message.as_string())

    logger.info(f"Sent email to {to_address}: {subject}")


def _strip_html(html: str) -> str:
    import re

    return re.sub(r"<[^>]+>", "", html).strip()
