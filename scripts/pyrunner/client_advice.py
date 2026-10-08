"""
Client advice worker for PyRunner.

Paste this into a PyRunner script called "client-advice". Each run:
  1. claims the next pending client_advice job from BusinessBots,
  2. asks the AI model for advice,
  3. sends the draft back (or reports the error),
and repeats until there are no jobs left.

PyRunner setup
--------------
Environment packages: requests
Secrets (PyRunner -> Secrets, all injected as environment variables):
  BUSINESSBOTS_URL       e.g. https://automation.baseuse.xyz
  PYRUNNER_WORKER_TOKEN  same value as PYRUNNER_WORKER_TOKEN in BusinessBots .env
  AI_BASE_URL            any OpenAI-compatible API, e.g. https://api.openai.com/v1
  AI_API_KEY
  AI_MODEL               e.g. gpt-4o-mini
Optional:
  AI_TIMEOUT_SECONDS     default 120
  MAX_JOBS_PER_RUN       default 10

Triggers: turn on the script's webhook (BusinessBots calls it after each
submission) and add a 5-minute schedule as a backup.
"""

import os
import sys

import requests

BASE_URL = os.environ["BUSINESSBOTS_URL"].rstrip("/")
WORKER_TOKEN = os.environ["PYRUNNER_WORKER_TOKEN"]
AI_BASE_URL = os.environ["AI_BASE_URL"].rstrip("/")
AI_API_KEY = os.environ["AI_API_KEY"]
AI_MODEL = os.environ["AI_MODEL"]
AI_TIMEOUT = int(os.environ.get("AI_TIMEOUT_SECONDS", "120"))
MAX_JOBS = int(os.environ.get("MAX_JOBS_PER_RUN", "10"))

JOB_TYPE = "client_advice"

SYSTEM_PROMPT = """You are an experienced operations and digital adviser for small UK organisations:
care providers, churches, charities and small businesses. You write practical,
plain-English advice that a busy manager with no technical background can act on.

Write the report in Markdown with these sections:
## Summary
Two or three sentences: the core problem and the recommended direction.
## What's causing this
The likely root causes, based only on what the client said.
## Options
Two to four options, from simplest/cheapest to most involved. For each: what it is,
roughly what it takes (time, cost band, skills), and the trade-offs.
Name well-known tools only where genuinely useful, and never invent prices.
## Recommended first steps
A numbered list of 3-5 concrete steps they can start this week.
## Questions to confirm
Anything you had to assume, as short questions.

Rules: be specific to their sector and size; say clearly when something depends on
regulation (e.g. CQC, GDPR, Charity Commission) and suggest checking with the right
body; do not make up facts, statistics or regulations; keep it under 900 words."""


def api(path, payload):
    return requests.post(
        f"{BASE_URL}/api/pyrunner/jobs{path}",
        json=payload,
        headers={"Authorization": f"Bearer {WORKER_TOKEN}", "Accept": "application/json"},
        timeout=30,
    )


def build_prompt(payload):
    org = payload.get("organisation") or {}
    problem = payload.get("problem") or {}

    def line(label, value):
        return f"{label}: {value}" if value not in (None, "") else f"{label}: not given"

    return "\n".join([
        "ORGANISATION",
        line("Name", org.get("name")),
        line("Type", org.get("type")),
        line("Location", org.get("location")),
        line("About", org.get("description")),
        line("Staff", org.get("staff_count")),
        "",
        "PROBLEM",
        line("Title", problem.get("title")),
        line("Description", problem.get("description")),
        line("Already tried", problem.get("already_tried")),
        line("Tools used now", problem.get("current_tools")),
        line("Urgency", problem.get("urgency")),
        line("They want", problem.get("help_wanted")),
    ])


def ask_ai(prompt):
    response = requests.post(
        f"{AI_BASE_URL}/chat/completions",
        headers={"Authorization": f"Bearer {AI_API_KEY}"},
        json={
            "model": AI_MODEL,
            "messages": [
                {"role": "system", "content": SYSTEM_PROMPT},
                {"role": "user", "content": prompt},
            ],
            "temperature": 0.4,
        },
        timeout=AI_TIMEOUT,
    )
    response.raise_for_status()
    content = response.json()["choices"][0]["message"]["content"]
    if not content or not content.strip():
        raise RuntimeError("The AI returned an empty answer.")
    return content.strip()


def process_one():
    """Claim and handle one job. Returns False when the queue is empty."""
    claim = api("/claim", {"type": JOB_TYPE})
    if claim.status_code == 204:
        return False
    claim.raise_for_status()

    job = claim.json()
    uuid = job["uuid"]
    print(f"Claimed job {uuid}")

    try:
        draft = ask_ai(build_prompt(job.get("payload") or {}))
    except Exception as exc:  # report every failure so the owner is told
        message = f"{type(exc).__name__}: {exc}"[:2000]
        print(f"Job {uuid} failed: {message}", file=sys.stderr)
        api(f"/{uuid}/fail", {"error": message}).raise_for_status()
        return True

    api(f"/{uuid}/complete", {"result": {"draft": draft, "model": AI_MODEL}}).raise_for_status()
    print(f"Job {uuid} completed ({len(draft)} characters)")
    return True


def main():
    handled = 0
    while handled < MAX_JOBS and process_one():
        handled += 1
    print(f"Done. Handled {handled} job(s).")


if __name__ == "__main__":
    main()
