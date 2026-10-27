# BusinessBots — Consolidated Status

A single honest pass over everything built so far: what's real and working, what's partially built, and what's genuinely launch-blocking. Written to be re-read cold by whoever (including future-you) picks this up next.

---

## 2026-10-27 — SEO foundation, legal emails, Postal-only mail

All changes below are committed; see git history for the exact commits.

- **SEO foundation:** `robots.txt` points at `https://nowsorted.co.uk/sitemap.xml`; `SeoController` generates the sitemap with the three microsites and only includes `/live-proof` when `features.social` is on; `SeoTest` covers the sitemap, canonical, OG/Twitter meta, robots, and OG image.
- **Meta/OG:** `seo-meta` builds canonical from `legal.domain` + path and drops query strings; added `og:image:width/height/alt`, `twitter:image:alt`; default `public/images/og-default.png` is 1200x630. Static microsites (`public/ai-audit/`, `public/career/`, `public/consult/`, `public/ai-audit.html`) now have description, canonical, OG and Twitter tags, and their broken `/projects/microsites/...` nav links are fixed.
- **Structured data:** Organization schema uses a neutral description; Service and FAQPage are gated to `features.social && home`.
- **Legal emails:** legal pages render `legal@nowsorted.co.uk` and `privacy@nowsorted.co.uk` from `config/legal.php`; `LegalEmailTest` asserts the old `automation.baseuse.xyz` / `businessbots.com` addresses are gone.
- **Config:** `legal.domain` now uses `LEGAL_DOMAIN` (default `https://nowsorted.co.uk`); `.env.example` sets `APP_URL=https://nowsorted.co.uk`; `phpunit.xml` pins `APP_URL` and clears `DATABASE_URL` so tests no longer depend on the local `.env`.
- **Mail:** removed the out-of-scope Resend transport, mailer, service config, env key and patch files. Mail is Postal/SMTP only; `.env.example` documents the Postal production values.
- **Test status:** Full suite passes: 79 tests, 287 assertions, 0 failures. All 79 are flagged deprecated by a PHP 8.5 warning from Laravel's vendor database config (`PDO::MYSQL_ATTR_SSL_CA`), not by application code.

## 2026-10-27 — Legacy WhatsApp automation removed

- Deleted the WhatsApp webhook controller, queued handler, Meta signature middleware, conversation model, WhatsApp service classes and the WhatsApp conversation migration.
- Removed WhatsApp from channel OAuth, token refresh, onboarding, service/config/env keys, marketing/legal copy, dashboard/leads empty states and the Filament lead label.
- Preserved Instagram/LinkedIn/GBP posting and the generic lead/consent records. The `whatsapp_conversations` table may still exist in already-migrated databases; no application code reads or writes it.
- Added `WhatsAppRemovalTest` to prove the webhook is gone, WhatsApp is not an available channel, onboarding can finish without a channel, and public copy no longer advertises it.

## 2026-10-27 — Inert lead AI-reply fields removed

- Dropped `ai_reply_sent`, `ai_reply_text` and `reply_time_seconds` from `leads` (migration `2026_10_27_000001`).
- Replaced reply-time/reply-rate metrics with lead/status metrics on the dashboard, homepage and live-proof page.
- `needsHumanAttention()` now follows `status !== 'closed'`; the lead table and Filament filter use the same rule.

---

## ✅ Solid — built, wired, and internally consistent

### Documentation
Full doc set (`project-overview.md`, `architecture.md`, `build-plan.md`, `code-standards.md`, `ui-rules.md`, `ui-registry.md`, `progress-tracker.md`, `README.md`) — all updated to reflect Laravel Blade + Livewire (not the original Next.js draft).

### Database
21 migrations, 12 Eloquent models. Schema covers: businesses, verticals (8 industries seeded), leads + consent tracking, scheduled posts (with media_url), trend topics, channel OAuth settings (encrypted tokens), AI templates, Stripe/Cashier tables, and problem requests.

### Frontend (Blade + Livewire)
- Homepage, pricing, navbar, footer — static Blade, dark theme matching the reference design
- 3-step onboarding wizard (vertical → profile → connect), fully Livewire, no page reloads
- Dashboard with `wire:poll` live stats
- Leads table with escalation styling (open leads flagged red)
- Settings page: editable business details + editable billing panel (plan switching, cancel/resume)
- Navbar billing status badge (only shows when something needs attention)

### Billing
Laravel Cashier + Stripe fully wired: checkout, trial handling, self-serve plan switching, billing portal handoff, webhook handling. Config-driven tier→price mapping.

### Content generation & posting
- `content_generator.py`: vertical-aware captions (GPT-4o-mini) + DALL-E images, downloaded to a shared volume (not the expiring OpenAI URL)
- `PostSchedulerJob` → `PostToChannelJob` → per-platform posting services (Instagram, LinkedIn, GBP) — one queue job per post, so one platform's outage can't block another's
- Daily image cleanup with two retention windows (90d posted / 7d never-posted)

### Multi-tenancy & admin
Filament panel with real tenancy (`Business` as tenant, `User implements HasTenants`) — `LeadResource` auto-scopes per business, `BusinessResource` special-cased since it *is* the tenant model.

### AI provider abstraction
A config-driven `AiClient` (PHP) and `AI_BASE_URL` (Python) remain for future AI-assisted workflows; the WhatsApp classifier/reply path was removed. Switching LLM providers is a `.env` change, not a code change. (Image generation is *not* covered by this — DALL-E-specific, documented as such.)

### Compliance
Terms, Privacy, Cookie Policy pages — config-driven (company name/domain/emails all from `.env`, not hardcoded). Sub-processor list matches actual integrations. **All marked as unreviewed drafts** — visible warning banners on each page.

### Channel connections
OAuth flow for Instagram/LinkedIn/GBP, with CSRF-style state verification. GBP does the real two-step account→location lookup (not a generic user ID). Token refresh runs twice daily, disconnects gracefully on failure rather than silently dying.

---

## ⚠️ Partial — works, but with known, documented limits

| Item | What works | What doesn't |
|---|---|---|
| GBP connections | Real API calls, real posting | Only the *first* location on the account — no picker for multi-location businesses |
| LinkedIn/GBP analytics | Posting works | `analytics_collector.py`'s LinkedIn/GBP metric fetchers are still stubs returning zeros — **this is now a real gap**, not a placeholder, since both platforms actually post content that nothing measures |
| Multi-tenant scoping | Filament admin fully scoped | Customer-facing Livewire (`LeadTable`, `Overview`) does its own manual `business_id` filtering via `activeBusiness()` — works, but is a second scoping mechanism running in parallel to Filament's, not unified |

---

## ❌ Not built — genuinely missing

- **CI/CD pipeline** — no GitHub Actions / equivalent.
- **Weekly learning job** (`performance_analyzer.py`, the thing that would weight future content toward what performed well) — described in `architecture.md`, never built.
- **Cookie consent banner** — the policy page exists; nothing on-site actually asks for or records consent.
- **DPA as a real document** — Privacy Policy says "available on request"; no actual DPA text exists yet.
- **Notification system** — `RefreshChannelTokens` has a `TODO: notify affected business owners` that's still just a log line, not an email/in-app notification.

---

## If launching to real paying customers tomorrow, in priority order

1. **Production auth/mail env** — magic-link sign-in depends on Postal/Coolify env being set
2. **Cookie consent banner** — legal exposure if skipped, not just a nice-to-have
3. **LinkedIn/GBP analytics** — currently posting content nobody can measure the performance of
4. Everything else in "Not built" can reasonably wait for post-launch iteration

---

## What this status doc is *not*

This isn't a judgment that the project is behind — for the scope covered (multi-vertical SaaS, Stripe billing, multi-platform posting, GDPR-aware legal pages, admin tenancy) this is a substantial working skeleton. It's a map of exactly where the edges are, so nothing gets assumed to work that hasn't actually been tested.

**Last updated:** 2026-10-27 — SEO foundation, legal-email rebrand, Postal-only mail, full-suite verification, legacy WhatsApp automation removal, dead AI model-config cleanup, and inert lead AI-reply field removal.
