import os
from dotenv import load_dotenv

load_dotenv()


class Config:
    """
    Reads the SAME .env file Laravel uses (see .env.example DATABASE_URL /
    REDIS_URL entries) — Python and Laravel are two processes against one
    database, not two separate configs that need to stay in sync by hand.
    """

    DATABASE_URL = os.getenv(
        "DATABASE_URL",
        "postgresql://businessbots:password@postgres:5432/businessbots",
    )
    REDIS_URL = os.getenv("REDIS_URL", "redis://redis:6379")

    OPENAI_API_KEY = os.getenv("AI_API_KEY", os.getenv("OPENAI_API_KEY"))  # AI_API_KEY takes precedence if set

    # Any OpenAI-compatible endpoint — OpenAI's official Python SDK supports
    # base_url natively, so switching providers is a .env change, matching the
    # same pattern used on the PHP side (config/ai.php + AiClient).
    AI_BASE_URL = os.getenv("AI_BASE_URL", "https://api.openai.com/v1")
    AI_MODEL_CONTENT = os.getenv("AI_MODEL_CONTENT", "gpt-4o-mini")

    # Used to build a public, persistent URL for generated images — see
    # jobs/content_generator.py _generate_image(). OpenAI's own image URLs
    # expire in ~2 hours, which isn't safe given posts publish up to an hour
    # later via the hourly PostSchedulerJob (Laravel side).
    APP_URL = os.getenv("APP_URL", "https://automation.baseuse.xyz")

    # Shared Docker volume with the Laravel container's public/storage —
    # see SETUP-NOTES.md "Shared image storage volume" for the docker-compose
    # wiring this depends on.
    IMAGE_STORAGE_PATH = os.getenv("IMAGE_STORAGE_PATH", "/shared-storage/generated-posts")

    TIMEZONE = "Europe/London"
    DEBUG = os.getenv("APP_DEBUG", "false").lower() == "true"

    # --- Job alert notifier (Flux) ---
    # Reads the SAME MAIL_* vars Laravel's mailer already uses (see
    # .env.example) — one set of SMTP credentials, not a second config to
    # keep in sync. Point FLUX_API_URL at wherever Flux is actually deployed;
    # defaults to the live instance so this works out of the box.
    FLUX_API_URL = os.getenv("FLUX_API_URL", "https://fluxjobs.baseuse.xyz")
    JOB_ALERT_EMAIL_TO = os.getenv("JOB_ALERT_EMAIL_TO", "bankoledada@gmail.com")
    JOB_ALERT_SCORE_THRESHOLD = int(os.getenv("JOB_ALERT_SCORE_THRESHOLD", "65"))

    # Restrict alerts to one of Flux's role keys (business_analyst, project,
    # programme, delivery, agile_delivery, scrum_master) — see skills_data.py
    # ROLES in the Flux repo for the exact keys. Leave blank to get all roles.
    JOB_ALERT_ROLE_FILTER = os.getenv("JOB_ALERT_ROLE_FILTER", "business_analyst").strip() or None

    MAIL_HOST = os.getenv("MAIL_HOST", "mailpit")
    MAIL_PORT = int(os.getenv("MAIL_PORT", "1025"))
    MAIL_USERNAME = os.getenv("MAIL_USERNAME") or None
    MAIL_PASSWORD = os.getenv("MAIL_PASSWORD") or None
    MAIL_ENCRYPTION = (os.getenv("MAIL_ENCRYPTION") or "").lower() or None  # "tls", "ssl", or None
    MAIL_FROM_ADDRESS = os.getenv("MAIL_FROM_ADDRESS", "hello@businessbots.com")
    MAIL_FROM_NAME = os.getenv("MAIL_FROM_NAME", "BusinessBots")


config = Config()
