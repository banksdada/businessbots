import logging
import psycopg2
from psycopg2.extras import RealDictCursor
from config import config

logger = logging.getLogger(__name__)


class DatabaseConnection:
    """
    Thin wrapper, not an ORM — these jobs run a handful of well-defined
    queries against tables Laravel's migrations own. Keeping this dumb on
    purpose: schema changes happen in Laravel migrations, never here.
    """

    def __init__(self):
        self.connection = psycopg2.connect(config.DATABASE_URL)

    def cursor(self, dict_rows: bool = False):
        return self.connection.cursor(
            cursor_factory=RealDictCursor if dict_rows else None
        )

    def commit(self):
        self.connection.commit()

    def rollback(self):
        self.connection.rollback()

    def close(self):
        self.connection.close()

    def __enter__(self):
        return self

    def __exit__(self, exc_type, exc_val, exc_tb):
        if exc_type is not None:
            self.rollback()
            logger.error(f"DB transaction rolled back due to: {exc_val}")
        self.close()
