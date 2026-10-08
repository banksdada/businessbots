# Client advice portal

Clients describe a business problem, a PyRunner script drafts advice with AI,
and an admin reviews it in `/ops` before the client sees it.

```
Client form ──► ProblemRequest + AutomationJob (pending) ──► PyRunner webhook
                                                              │
PyRunner script: claim ─► ask AI ─► complete (draft) or fail ◄┘
                                     │
          pending_review + owner email ◄┘        failed + owner email
                     │
     /ops: edit draft ─► Approve and send ─► client email + report on dashboard
```

A scheduled command (`portal:fail-stuck-jobs`, every 10 minutes) fails any job
with no result after `PORTAL_STUCK_AFTER_MINUTES`, so nothing waits forever.

## Test it locally (VS Code)

```bash
git fetch origin && git checkout feature/client-advice-portal
composer install && npm install && npm run build
php artisan filament:assets
php artisan migrate
php artisan portal:make-admin you@example.com
php vendor/bin/phpunit          # 42 tests
php artisan serve
```

With `MAIL_MAILER=log`, magic login links appear in `storage/logs/laravel.log`.

To run the AI step without PyRunner, run the script from your terminal with the
same environment variables PyRunner would give it:

```bash
BUSINESSBOTS_URL=http://127.0.0.1:8000 PYRUNNER_WORKER_TOKEN=<from .env> \
AI_BASE_URL=https://api.openai.com/v1 AI_API_KEY=<key> AI_MODEL=gpt-4o-mini \
python3 scripts/pyrunner/client_advice.py
```

## Deploy on Coolify

1. Set the new `.env` values listed under "Client advice portal" in `.env.example`.
2. Redeploy, then run `php artisan migrate --force` and
   `php artisan portal:make-admin <your email>` in the container.
3. In PyRunner: create a script `client-advice` from
   `scripts/pyrunner/client_advice.py`, add `requests` to its environment, add
   the secrets listed at the top of the script, turn on its webhook, and add a
   5-minute schedule.
4. Put the script's webhook URL in `PYRUNNER_ADVICE_WEBHOOK_URL`.

`AI_BASE_URL` must be an OpenAI-compatible `/chat/completions` API. Check that
the provider you choose fits UK GDPR for your clients' data.
