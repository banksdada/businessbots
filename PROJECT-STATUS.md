# BusinessBots — Consolidated Status

A single honest pass over everything built so far: what's real and working, what's partially built, and what's genuinely launch-blocking. Written to be re-read cold by whoever (including future-you) picks this up next.

---

## ✅ Solid — built, wired, and internally consistent

### Documentation
Full doc set (`project-overview.md`, `architecture.md`, `build-plan.md`, `code-standards.md`, `ui-rules.md`, `ui-registry.md`, `progress-tracker.md`, `README.md`) — all updated to reflect Laravel Blade + Livewire (not the original Next.js draft).

### Database
14 migrations, 10 Eloquent models. Schema covers: businesses, verticals (8 industries seeded), leads + WhatsApp conversation log, scheduled posts (with media_url), trend topics, channel OAuth settings (encrypted tokens), AI templates, Stripe/Cashier tables, and WhatsApp opt-out/consent tracking.

### Frontend (Blade + Livewire)
- Homepage, pricing, navbar, footer — static Blade, dark theme matching the reference design
- 3-step onboarding wizard (vertical → profile → connect), fully Livewire, no page reloads
- Dashboard with `wire:poll` live stats
- Leads table with escalation styling (complaints/no-reply flagged red)
- Settings page: editable business details + editable billing panel (plan switching, cancel/resume)
- Navbar billing status badge (only shows when something needs attention)

### Billing
Laravel Cashier + Stripe fully wired: checkout, trial handling, self-serve plan switching, billing portal handoff, webhook handling. Config-driven tier→price mapping.

### WhatsApp core loop
Webhook receiver (signature-verified) → queued handler → intent classification → vertical-aware reply generation → send. **Opt-out (STOP) and opt-in (START) both implemented as fixed, non-AI-dependent logic** — compliance-critical paths don't depend on an LLM call succeeding.

### Content generation & posting
- `content_generator.py`: vertical-aware captions (GPT-4o-mini) + DALL-E images, downloaded to a shared volume (not the expiring OpenAI URL)
- `PostSchedulerJob` → `PostToChannelJob` → per-platform posting services (Instagram, LinkedIn, GBP) — one queue job per post, so one platform's outage can't block another's
- Daily image cleanup with two retention windows (90d posted / 7d never-posted)

### Multi-tenancy & admin
Filament panel with real tenancy (`Business` as tenant, `User implements HasTenants`) — `LeadResource` auto-scopes per business, `BusinessResource` special-cased since it *is* the tenant model.

### AI provider abstraction
Chat/classification calls go through a config-driven `AiClient` (PHP) / `AI_BASE_URL` (Python) — switching LLM providers is a `.env` change, not a code change. (Image generation is *not* covered by this — DALL-E-specific, documented as such.)

### Compliance
Terms, Privacy, Cookie Policy pages — config-driven (company name/domain/emails all from `.env`, not hardcoded). Sub-processor list matches actual integrations. **All marked as unreviewed drafts** — visible warning banners on each page.

### Channel connections
OAuth flow for WhatsApp/Instagram/LinkedIn/GBP, with CSRF-style state verification. GBP does the real two-step account→location lookup (not a generic user ID). Token refresh runs twice daily, disconnects gracefully on failure rather than silently dying.

---

## ⚠️ Partial — works, but with known, documented limits

| Item | What works | What doesn't |
|---|---|---|
| GBP connections | Real API calls, real posting | Only the *first* location on the account — no picker for multi-location businesses |
| LinkedIn/GBP analytics | Posting works | `analytics_collector.py`'s LinkedIn/GBP metric fetchers are still stubs returning zeros — **this is now a real gap**, not a placeholder, since both platforms actually post content that nothing measures |
| WhatsApp channel connection | OAuth-based token connection works | Assumes an *existing* WABA (WhatsApp Business Account) — Meta's real Embedded Signup flow (provisioning a brand-new WABA) isn't built |
| Multi-tenant scoping | Filament admin fully scoped | Customer-facing Livewire (`LeadTable`, `Overview`) does its own manual `business_id` filtering via `activeBusiness()` — works, but is a second scoping mechanism running in parallel to Filament's, not unified |

---

## ❌ Not built — genuinely missing

- **Auth pages** — `routes/auth.php`, login/register views. Referenced everywhere, assumed to exist, never generated in this repo.
- **`bootstrap/app.php` / `bootstrap/providers.php`** — middleware aliases, CSRF exemptions, and the Filament panel provider registration are all *documented* in `SETUP-NOTES.md` but the actual files don't exist yet (Laravel scaffolds these on `laravel new`, which hasn't been run against this tree).
- **Tests** — zero. No unit, feature, or integration tests anywhere.
- **CI/CD pipeline** — no GitHub Actions / equivalent.
- **`docker-compose.yml`** — described piecemeal across `SETUP-NOTES.md` and earlier chat (shared volume config, service list) but never assembled into one actual file.
- **Weekly learning job** (`performance_analyzer.py`, the thing that would weight future content toward what performed well) — described in `architecture.md`, never built.
- **Cookie consent banner** — the policy page exists; nothing on-site actually asks for or records consent.
- **DPA as a real document** — Privacy Policy says "available on request"; no actual DPA text exists yet.
- **Notification system** — `RefreshChannelTokens` has a `TODO: notify affected business owners` that's still just a log line, not an email/in-app notification.
- **Mail sending** — `.env` has SMTP vars; nothing in the app actually sends a transactional email (password reset relies on Laravel defaults not yet scaffolded).

---

## If launching to real paying customers tomorrow, in priority order

1. **Auth scaffolding + `bootstrap/app.php`** — the app literally doesn't boot without these
2. **`docker-compose.yml`** assembled as one real file — needed to deploy at all
3. **Cookie consent banner** — legal exposure if skipped, not just a nice-to-have
4. **LinkedIn/GBP analytics** — currently posting content nobody can measure the performance of
5. Everything else in "Not built" can reasonably wait for post-launch iteration

---

## What this status doc is *not*

This isn't a judgment that the project is behind — for the scope covered (multi-vertical SaaS, WhatsApp automation, Stripe billing, multi-platform posting, GDPR-aware legal pages, admin tenancy) this is a substantial working skeleton. It's a map of exactly where the edges are, so nothing gets assumed to work that hasn't actually been tested.

**Last updated:** during this build session, reflecting everything through the image-cleanup command.
