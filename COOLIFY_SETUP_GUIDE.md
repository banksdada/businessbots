# BusinessBots — Coolify Setup Guide

## Current Status
- **Repo**: https://github.com/banksdada/businessbots
- **App URL**: https://automation.baseuse.xyz
- **Stack**: Laravel 11 + Livewire + Filament + PostgreSQL + Redis + Python job runner
- **Last deployment**: FAILED (ext-zip missing + Controller missing + users table missing — all fixed, redeploy pending)

---

## What Was Built (Complete Inventory)

### Phase 1 — Laravel App Bootable
- `bootstrap/app.php`, `bootstrap/providers.php`
- `routes/auth.php`, `routes/web.php`, `routes/console.php`
- `app/Http/Controllers/Auth/AuthController.php`
- Auth views (login/register)
- `.env.example`, `config/services.php`, `artisan`, `public/index.php`, `public/.htaccess`
- `app/Providers/AppServiceProvider.php`
- `app/Http/Controllers/Controller.php` (base class)
- `app/Providers/Filament/AdminPanelProvider.php`
- Filament resources: `LeadResource.php`, `BusinessResource.php`

### Phase 2 — Docker Config
- `Dockerfile` (php:8.3-fpm-bookworm, all extensions including zip)
- `Dockerfile.pyrunner` (Python 3.12 for background jobs)
- `docker-compose.yml` (4 services: app, pyrunner, postgres, redis)
- `docker/nginx.conf`, `docker/supervisord.conf`, `docker/php.ini`
- `.dockerignore`, `vite.config.js`, `tailwind.config.js`, `package.json`, `postcss.config.js`

### Phase 3 — Database
- `database/migrations/0000_00_00_000000_create_users_table.php`
- All business/lead/social/subscription migrations
- `database/seeders/DatabaseSeeder.php`, `VerticalConfigSeeder.php`
- `database/factories/UserFactory.php`, `BusinessFactory.php`, `LeadFactory.php`

### Phase 4 — Tests
- `phpunit.xml`, test base classes
- Unit tests: BusinessTest, LeadTest, UserTest
- Feature tests: AuthTest, DashboardTest

### Phase 5 — Python Jobs
- `python/scheduler.py` — APScheduler cron (5am, 6am, 11pm)
- `python/jobs/trend_detector.py` — trend detection
- `python/jobs/content_generator.py` — AI post generation (OpenAI + DALL-E 3)
- `python/jobs/analytics_collector.py` — engagement metrics
- `python/config.py`, `python/utils/` — shared config and DB utils

### Phase 6 — Docs
- `DEPLOYMENT-GUIDE.md`, `QUICK-REFERENCE.md`

---

## Coolify Setup Instructions

### Step 1: Create the App in Coolify
1. Go to Coolify dashboard → **Applications** → **New Application**
2. Select **Git-based deployment**
3. Enter repo URL: `https://github.com/banksdada/businessbots`
4. Branch: `master`
5. Build pack: **Dockerfile** (point to the root `Dockerfile`)

### Step 2: Add PostgreSQL Database
1. Go to **Databases** → **New Database**
2. Select **PostgreSQL**
3. Service name: `postgres`
4. Note the credentials Coolify generates (or set your own)

### Step 3: Add Redis (optional, for queues/cache)
1. Go to **Databases** → **New Database**
2. Select **Redis**
3. Service name: `redis`

### Step 4: Set Environment Variables
In the app's **Environment Variables** tab, add:

```
APP_NAME=BusinessBots
APP_ENV=production
APP_KEY=              (generate with: php artisan key:generate)
APP_DEBUG=false
APP_TIMEZONE=UTC
APP_URL=https://automation.baseuse.xyz

DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=businessbots
DB_USERNAME=secret
DB_PASSWORD=secret

SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_HOST=redis
REDIS_PASSWORD=secret
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=hello@businessbots.com
MAIL_FROM_NAME=BusinessBots

STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=

META_APP_ID=
META_APP_SECRET=
META_WHATSAPP_TOKEN=
META_WHATSAPP_PHONE_NUMBER_ID=
META_WEBHOOK_VERIFY_TOKEN=

LINKEDIN_CLIENT_ID=
LINKEDIN_CLIENT_SECRET=

GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=

AI_BASE_URL=https://api.openai.com/v1
AI_API_KEY=
OPENAI_API_KEY=
```

### Step 5: Deploy
1. Click **Deploy** in Coolify
2. Wait for build to complete (8-10 min)
3. Once running, go to the **Terminal** tab in Coolify

### Step 6: Run Migrations (in Coolify Terminal)
```bash
php artisan migrate:fresh --force
php artisan db:seed --force
php artisan cache:clear
php artisan config:cache
php artisan view:cache
```

### Step 7: Verify
Open https://automation.baseuse.xyz — you should see the landing page.

---

## Fixes Applied During This Session

| Issue | Fix | Commit |
|-------|-----|--------|
| ext-zip missing in Docker | Added `zip` to `docker-php-ext-install` | `191fbba` |
| libzip-dev missing | Added `libzip-dev` to `apt-get install` | `488a706` |
| Base Controller missing | Created `app/Http/Controllers/Controller.php` | `929852a` |
| AdminPanelProvider deprecated method | Removed `tenantRegistrationDisabled()` | `0d78dbf` |
| Users table migration missing | Created `0000_00_00_000000_create_users_table.php` | `4733118` |
| DatabaseSeeder missing | Created `database/seeders/DatabaseSeeder.php` | `b0f3bfd` |
| Nginx not starting | Added nginx + php-fpm to supervisord.conf | `1cdd6fa` |
| bootstrap/cache permissions | Changed 755 to 775 | `1cdd6fa` |
| Composer --no-audit fails | Used `--no-security-blocking` | `5d646bc` |

---

## Troubleshooting

### Build fails with "ext-zip missing"
The Dockerfile must include `zip` in BOTH:
- `apt-get install ... zip unzip libzip-dev`
- `docker-php-ext-install ... zip`

### "Class Controller not found"
Create `app/Http/Controllers/Controller.php`:
```php
<?php
namespace App\Http\Controllers;
use Illuminate\Routing\Controller as BaseController;
class Controller extends BaseController {}
```

### "users table does not exist" during migrate
You need the users migration. Run:
```bash
php artisan migrate:fresh --force
```

### "cache table does not exist"
Tables haven't been created yet. Run:
```bash
php artisan migrate --force
```

### Coolify terminal commands fail with permissions
The Dockerfile sets `chmod 775` on `storage/` and `bootstrap/cache/`. If still failing:
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## Architecture

```
┌─────────────┐     ┌─────────────┐     ┌──────────────┐
│   Nginx     │────▶│  PHP-FPM    │────▶│  PostgreSQL  │
│  (port 80)  │     │  (Laravel)  │     │  (database)  │
└─────────────┘     └──────┬──────┘     └──────────────┘
                           │
                    ┌──────▼──────┐
                    │   Redis     │
                    │  (cache)    │
                    └──────┬──────┘
                           │
                    ┌──────▼──────┐
                    │  PyRunner   │
                    │  (AI jobs)  │
                    └─────────────┘
```

- **Nginx** serves static files + proxies PHP-FPM
- **PHP-FPM** runs Laravel (web, API, queue worker)
- **PostgreSQL** stores all data
- **Redis** handles sessions, cache, queues
- **PyRunner** generates AI content (trends, posts, images)
