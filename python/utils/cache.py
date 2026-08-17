import redis
from config import config


def get_redis() -> redis.Redis:
    return redis.Redis.from_url(config.REDIS_URL, decode_responses=True)
