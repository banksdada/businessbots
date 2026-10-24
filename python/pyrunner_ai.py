import os
from dataclasses import dataclass, field
from typing import Generator
from openai import OpenAI
from config import config


_client: OpenAI | None = None


def _get_client() -> OpenAI:
    global _client
    if _client is None:
        _client = OpenAI(api_key=config.OPENAI_API_KEY, base_url=config.AI_BASE_URL)
    return _client


@dataclass
class MimoResult:
    text: str
    tools_used: list[str] = field(default_factory=list)
    cost_usd: float = 0.0
    turns: int = 1


def ask_mimo(
    prompt: str,
    *,
    tools: list[str] | None = None,
    model: str | None = None,
    system_prompt: str | None = None,
    raw: bool = False,
    lean: bool = False,
) -> str | MimoResult:
    client = _get_client()
    selected_model = model or os.getenv("MIMO_MODEL", "mimo-v2.5")

    messages = []
    if system_prompt:
        messages.append({"role": "system", "content": system_prompt})
    messages.append({"role": "user", "content": prompt})

    response = client.chat.completions.create(
        model=selected_model,
        messages=messages,
        temperature=0.7,
        max_tokens=1024,
    )

    text = response.choices[0].message.content.strip()
    cost_usd = getattr(response, "usage", None)
    total_tokens = (cost_usd.prompt_tokens + cost_usd.completion_tokens) if cost_usd else 0

    if raw:
        return MimoResult(
            text=text,
            tools_used=tools or [],
            cost_usd=round(total_tokens * 0.000001, 6),
        )

    return text


def stream_mimo(
    prompt: str,
    *,
    model: str | None = None,
    system_prompt: str | None = None,
) -> Generator[str, None, None]:
    client = _get_client()
    selected_model = model or os.getenv("MIMO_MODEL", "mimo-v2.5")

    messages = []
    if system_prompt:
        messages.append({"role": "system", "content": system_prompt})
    messages.append({"role": "user", "content": prompt})

    stream = client.chat.completions.create(
        model=selected_model,
        messages=messages,
        temperature=0.7,
        max_tokens=1024,
        stream=True,
    )

    for chunk in stream:
        delta = chunk.choices[0].delta
        if delta.content:
            yield delta.content
