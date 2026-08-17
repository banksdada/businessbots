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


config = Config()
