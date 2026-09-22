import logging
import time
from apscheduler.schedulers.background import BackgroundScheduler
from apscheduler.triggers.cron import CronTrigger
from apscheduler.triggers.interval import IntervalTrigger

from config import config
from jobs.trend_detector import detect_trends
from jobs.content_generator import generate_content
from jobs.analytics_collector import collect_analytics
from jobs.job_alert_notifier import notify_job_alerts

logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s - %(name)s - %(levelname)s - %(message)s",
)
logger = logging.getLogger(__name__)

scheduler = BackgroundScheduler(timezone=config.TIMEZONE)

scheduler.add_job(
    detect_trends,
    CronTrigger(hour=5, minute=0),
    id="trend_detector",
    name="Trend Detector (5 AM)",
    replace_existing=True,
)

scheduler.add_job(
    generate_content,
    CronTrigger(hour=6, minute=0),
    id="content_generator",
    name="Content Generator (6 AM)",
    replace_existing=True,
)

scheduler.add_job(
    collect_analytics,
    CronTrigger(hour=23, minute=0),
    id="analytics_collector",
    name="Analytics Collector (11 PM)",
    replace_existing=True,
)

scheduler.add_job(
    notify_job_alerts,
    IntervalTrigger(minutes=30),
    id="job_alert_notifier",
    name="Job Alert Notifier (every 30 min)",
    replace_existing=True,
)


def main():
    logger.info("Starting BusinessBots Python job runner...")
    logger.info(f"Timezone: {config.TIMEZONE}")
    logger.info(
        "Registered jobs: trend_detector (05:00), content_generator (06:00), "
        "analytics_collector (23:00), job_alert_notifier (every 30 min)"
    )

    scheduler.start()

    try:
        while True:
            time.sleep(1)
    except KeyboardInterrupt:
        logger.info("Shutting down scheduler...")
        scheduler.shutdown()


if __name__ == "__main__":
    main()
