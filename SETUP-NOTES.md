# Python jobs (PyRunner) — what's real vs stubbed

`python/scheduler.py` + `jobs/{trend_detector,content_generator,analytics_collector}.py`
are now real, not just code examples in the docs.

**Fully working:** trend detection (Google Trends via pytrends), content
generation (vertical-aware via the same `resolvedConfig()` merge shape as
`BusinessVertical` on the PHP side — the two are duplicated by necessity
since Python/PHP don't share code; if the merge logic changes, change it in
both places), and Instagram analytics collection.

**Stubbed, not built:** LinkedIn and GBP analytics collection return zeros —
because **no job posts to LinkedIn or GBP yet either**. Only Instagram
posting exists (referenced in `architecture.md`'s data flow but the actual
`PostSchedulerJob`/`PostInstagramService` Laravel classes it describes
haven't been built in this repo yet — that's the next real gap after this).

## Running PyRunner against this repo

```bash
cd python
pip install -r requirements.txt --break-system-packages
python scheduler.py
```

Needs the same `DATABASE_URL` / `REDIS_URL` / `OPENAI_API_KEY` already in
`.env.example` — this reads the identical `.env` Laravel uses, not a
separate config.

# Filament multi-tenancy — scoping admins to their own business

**Implemented** via Filament's built-in tenancy, tied to the `Business` model:

- `AdminPanelProvider` calls `->tenant(Business::class, ownershipRelationship: 'business')`
  — every resource whose model has a `business()` relationship (like `LeadResource`)
  is automatically scoped to the tenant currently selected in the switcher.
  No manual `->where('business_id', ...)` needed in resource queries.
- `User implements HasTenants` — `getTenants()` returns only onboarded
  (`is_active`) businesses the user owns; `canAccessTenant()` is the actual
  security check preventing a user from switching to a business they don't own.
- `Business implements HasName` — supplies the label shown in the tenant switcher.
- `BusinessResource` is the one exception: it can't be scoped by `business_id`
  (it IS the tenant), so it's manually restricted to `Filament::getTenant()`
  instead, and its `EditBusiness` page resolves that tenant directly rather
  than expecting a `{record}` route parameter.

**Before going live, verify:**
- [ ] Register `AdminPanelProvider` in `bootstrap/providers.php`
- [ ] Confirm `php artisan filament:upgrade` doesn't overwrite `->tenant()` config
- [ ] Manually test: log in as a user with 2 businesses, switch tenants, confirm
      `LeadResource` shows different leads per business — this is the actual
      proof the scoping works, not just that the code compiles
- [ ] If you ever add internal BusinessBots staff accounts that need to see
      *all* businesses, that's a separate `super_admin` flag + a different
      panel or a bypass in `canAccessTenant()` — don't reuse this panel as-is
      for that use case

# Scheduled commands — routes/console.php

```php
use App\Console\Commands\RefreshChannelTokens;
use App\Console\Commands\CleanupGeneratedImages;
use App\Jobs\PostSchedulerJob;

Schedule::command(RefreshChannelTokens::class)->twiceDaily();
Schedule::job(new PostSchedulerJob)->hourly();
Schedule::command(CleanupGeneratedImages::class)->daily();
```

`PostSchedulerJob` runs alongside the Python jobs already scheduled via
PyRunner — it's the Laravel-side counterpart that actually publishes what
`content_generator.py` writes into `scheduled_posts`.

## Posting pipeline — what works, what doesn't yet

**Instagram**: real two-step (container → publish) implementation against
the Graph API. `content_generator.py` now generates a DALL-E 3 image per
post and downloads it to a shared volume (see below) rather than storing
OpenAI's own URL, which expires in ~2 hours — too short given posts publish
up to an hour later via the hourly `PostSchedulerJob`. Posts where image
generation fails are skipped, not created broken.

### Shared image storage volume (required for Instagram posting to work)

PyRunner (Python) writes generated images to disk; Laravel needs to serve
them at a public URL. Both containers must mount the **same** volume:

```yaml
# docker-compose.yml
services:
  app:
    volumes:
      - shared_storage:/var/www/html/storage/app/public/generated-posts
  pyrunner:
    volumes:
      - shared_storage:/shared-storage/generated-posts
    environment:
      - IMAGE_STORAGE_PATH=/shared-storage/generated-posts
      - APP_URL=${APP_URL}

volumes:
  shared_storage:
```

Then run `php artisan storage:link` once so Laravel's `public/storage` symlink
resolves to `storage/app/public`, making
`https://automation.baseuse.xyz/storage/generated-posts/{file}.png` a real,
publicly reachable URL — which is what gets stored in `scheduled_posts.media_url`
and what Instagram's API fetches at publish time.

`CleanupGeneratedImages` (below) deletes from `Storage::disk('public')`,
which by Laravel's default `filesystems.php` config means
`storage/app/public` — the same location the shared volume above mounts
into. If you ever change the `public` disk's root path, the cleanup command
and the image-serving URL both need to stay pointed at that same shared
volume, or cleanup will silently delete nothing (path never matches) while
disk usage keeps growing.

Cleanup is handled by `php artisan posts:cleanup-images`, scheduled daily
(see above) — 90-day retention for posted images, 7-day for images generated
but never published. Deletes the file from the shared volume and clears
`media_url` on the row so the caption/hashtags/analytics data stays intact
even after the image itself is gone.

**LinkedIn**: implemented against the real UGC Posts API shape. Should work
once a business connects LinkedIn via the OAuth flow — untested against a
live account.

**Google Business Profile**: implemented against the real Local Posts API
shape. Identity resolution now does the real two-step lookup (list accounts
→ list locations) instead of a generic user ID — see
`ChannelOAuthController::resolveGbpLocation()`. If the connected Google
account has no Business Profile account/location, the connect flow now
fails visibly at OAuth callback time with a clear message, rather than
saving a broken connection that fails silently three days later in the
queue.

**Known limitation, not yet fixed**: only the *first* location on the
account is used. A business managing multiple locations (e.g. a cleaning
franchise with 3 branches) has no UI to pick which one connects — it's
whichever Google's API returns first. Fine for the single-location MVP
target; a real gap for multi-location businesses.

# AI provider — decoupled from OpenAI specifically

`config/ai.php` (PHP) and `AI_BASE_URL`/`AI_API_KEY` in `.env` (both PHP and
Python) mean the WhatsApp intent classifier, reply generator, and post
caption generator all call whatever endpoint you configure — OpenAI itself
by default, but any OpenAI-compatible `/chat/completions` API works by
changing `.env` only. `App\Services\AI\AiClient` is the one place this
lives on the PHP side; nothing else should call an LLM API directly.

**Not covered by this abstraction:** `_generate_image()` in
`content_generator.py` still calls OpenAI's DALL-E API directly. Image
generation isn't standardized across providers the way chat completions
are (different request/response shapes, no common "OpenAI-compatible"
convention) — swapping image providers would mean writing a new function,
not just changing a URL. Left as-is rather than half-abstracting it.

This is unrelated to which tool you use to *write* code (OpenCode, Claude
Code, etc.) — that's a development-time choice about this repo's files;
this section is about what the deployed app calls at runtime.

# Middleware & CSRF setup — bootstrap/app.php

This project doesn't include a generated `bootstrap/app.php` (Laravel scaffolds
this on `laravel new`). Once you run that, add the following to the
`->withMiddleware()` call:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'business.onboarded' => \App\Http\Middleware\EnsureBusinessOnboarded::class,
        'subscribed' => \App\Http\Middleware\EnsureSubscribed::class,
        'meta.signature' => \App\Http\Middleware\VerifyMetaWebhookSignature::class,
    ]);

    // Both webhooks are called by external services (Stripe, Meta) with no
    // session/CSRF token — exempt them, but note both are still protected:
    // Stripe's via Cashier's own signature check, Meta's via meta.signature above.
    $middleware->validateCsrfTokens(except: [
        'stripe/webhook',
        'webhooks/whatsapp',
    ]);
})
```

## config/services.php

Merge the contents of `config/services.meta.php` (in this repo) into the
`return [...]` array in Laravel's generated `config/services.php` — don't
leave it as a separate file; Laravel only autoloads `config/services.php`
itself.

## Queue worker

`WhatsAppHandlerJob` is dispatched onto the default queue (Redis, per
`.env.example`). In production this needs a running worker:

```bash
php artisan queue:work --tries=3 --backoff=10
```

On Coolify, run this as a second process alongside `php-fpm`/`octane` in the
same container or as a sibling service in `docker-compose.yml` — see
`coolify-pyrunner-integration.md` for the pattern already established for
the Python jobs.
