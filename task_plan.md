# Task Plan — Suicide Prevention Platform

## Status: Phase 1 — Entities + Migrations

---

## Phase 0 — Project scaffold ✅ COMPLETE
- [x] Symfony 8.0.7 scaffolded via `composer create-project symfony/skeleton:^8.0`
- [x] All composer dependencies installed (prod + dev)
- [x] Docker stack: FrankenPHP (PHP 8.4) + PostgreSQL 16 + Mailpit
- [x] Dockerfile, compose.yaml, compose.override.yaml, Makefile in place
- [ ] Configure `config/packages/doctrine.yaml` — PostgreSQL, server_version 16 (Flex recipe created baseline)
- [ ] Configure `config/packages/translation.yaml` — default `de`, fallback `en`, 8 locales
- [ ] Configure `config/packages/monolog.yaml` — error-only, no POST bodies
- [ ] Configure `config/packages/rate_limiter.yaml` — `reasons_api`: 10/hour sliding window
- [ ] Set up `importmap.php` with htmx@2.0.3, alpinejs@3.14.1
- [ ] Set up Tailwind 4 via PostCSS in asset pipeline
- [ ] Create `assets/app.js` entry point
- [ ] Create `assets/styles/app.css` with Tailwind directives

## Phase 1 — Database entities + migrations
- [ ] Create `src/Entity/Country.php`
- [ ] Create `src/Entity/CrisisResource.php`
- [ ] Create `src/Entity/FollowupQueue.php`
- [ ] Create `CountryRepository.php` and `CrisisResourceRepository.php`
- [ ] Run `make:migration`, verify SQL, apply
- [ ] Create `CountryFixtures.php` — 20 countries
- [ ] Create `CrisisResourceFixtures.php` — all from seed-data.md
- [ ] Run fixtures and verify

## Phase 2 — Core services
- [x] `GeolocationService.php` — DB-IP Lite MMDB, fallback 'de'
- [x] Download DB-IP country lite via `make geoip` (CC BY 4.0, no account)
- [x] Attribution in datenschutz.html.twig
- [ ] `CrisisResourceService.php`
- [ ] `SafetyOutputFilter.php` — blocked patterns + fallback letter
- [ ] Fallback letter txt files (8 locales)
- [ ] `ClaudeService.php` — claude-sonnet-4-20250514, stateless
- [ ] `FollowupService.php` — schedule/processQueue/cancelByToken, AES-256-CBC
- [ ] `RequestBodyStripListener.php`
- [ ] Unit tests: SafetyOutputFilter (adversarial), FollowupService (encrypt round-trip)

## Phase 3 — Controllers + routing
- [ ] `HomeController.php` — `/`, `/{_locale}`
- [ ] `TalkController.php` — `/talk`, `/talk/transparency/{country}`
- [ ] `PlanController.php` — `/plan`, `/plan/export`
- [ ] `AiController.php` — `/api/reasons` (POST, rate-limited)
- [ ] `ResourceController.php` — `/resources`, `/resources/{countryCode}`
- [ ] `FollowupController.php` — `/followup/optin`, `/followup/stop/{token}`
- [ ] `LegalController.php` — `/impressum`, `/datenschutz`
- [ ] `config/routes.yaml` — locale prefix on all main routes
- [ ] CSRF header injection for HTMX in `assets/app.js`

## Phase 4 — Templates
- [ ] `base.html.twig`
- [ ] `home/index.html.twig`
- [ ] `talk/index.html.twig` + `_transparency.html.twig` (HTMX partial)
- [ ] `plan/index.html.twig` + `_letter.html.twig` + `pdf.html.twig`
- [ ] `resources/index.html.twig` + `_country.html.twig`
- [ ] `followup/_confirmed.html.twig` + `stopped.html.twig`
- [ ] `legal/impressum.html.twig` + `datenschutz.html.twig`
- [ ] `error/error.html.twig` + `error404.html.twig`

## Phase 5 — Translations
- [ ] `messages.de.yaml` (primary, complete)
- [ ] `messages.en.yaml`, `ru`, `ko`, `ja`, `lt`, `uk`, `es`
- [ ] Fallback letter txt files for each locale
- [ ] Test locale switching via URL prefix

## Phase 6 — PDF export
- [ ] `PlanController::export()` — receives JSON blob, renders PDF
- [ ] `templates/plan/pdf.html.twig` — print-safe, no dark mode
- [ ] Test PDF across all 8 locales

## Phase 7 — Followup queue
- [ ] `ProcessFollowupsCommand.php`
- [ ] Email templates: `email/checkin.{locale}.html.twig`
- [ ] Configure Symfony Mailer
- [ ] Test full lifecycle: opt-in → queue → send → wipe → delete row
- [ ] Set up cron: `*/15 * * * *`

## Phase 8 — Security hardening
- [ ] NelmioSecurityBundle CSP headers
- [ ] `X-Frame-Options`, `X-Content-Type-Options`, `Referrer-Policy` headers
- [ ] `php bin/console security:check`
- [ ] Test rate limiter (429 after 10 requests)
- [ ] SafetyOutputFilter adversarial test (20 inputs)
- [ ] Verify no user input in logs
- [ ] OWASP ZAP baseline scan

## Phase 9 — Accessibility + mobile
- [ ] axe-core audit on all pages
- [ ] Keyboard navigation on plan builder
- [ ] Screen reader test (NVDA + Firefox)
- [ ] Alt text on all images
- [ ] Color contrast ≥ 4.5:1
- [ ] 320px width test
- [ ] Touch targets ≥ 44px

## Phase 10 — Deployment
- [ ] Provision Hetzner CX21 (Frankfurt)
- [ ] Set up nginx + FrankenPHP or PHP-FPM 8.3
- [ ] Install PostgreSQL 16
- [ ] SSL via certbot
- [ ] Production env vars
- [ ] Run migrations + fixtures on prod
- [ ] Plausible Analytics (self-hosted)
- [ ] Hetzner automated backups
- [ ] UFW: allow 80, 443, 22 only
- [ ] unattended-upgrades
- [ ] Smoke test all routes

## Phase 11 — Launch prep
- [ ] Fill real Impressum data
- [ ] Legal review of Datenschutzerklärung (budget €300)
- [ ] Apply to Google for Nonprofits
- [ ] Submit to IASP crisis directory
- [ ] Submit to findahelpline.com
- [ ] Google Search Console + sitemap

## Phase 13 — Warm, inclusive design + DaisyUI + adaptive colour scheme ✅ COMPLETE
- [x] Install DaisyUI 5 via Bun (package.json + `make bun-install` after `make build`)
- [x] Define custom DaisyUI theme pair (`warm-light` / `warm-dark`) — amber/brown/cream palette
- [x] Light mode: soft cream background, dark warm text
- [x] Dark mode: warm dark brown base (not cold stone-950), cream text
- [x] Auto-switch: time-of-day + prefers-color-scheme; manual toggle in footer; localStorage persisted
- [x] Migrated all templates to DaisyUI semantic classes (btn, badge, input, select, link)
- [x] Updated `app.source.css` with `@plugin "daisyui"` + custom theme definitions
- [x] Updated `base.html.twig` — data-theme + themeManager() Alpine component
- [x] All stone- hardcoded colours replaced with base-content/primary/error semantics
- [x] Crisis button: `btn btn-error rounded-full` — DaisyUI-aware, theme-adaptive
- [x] Recompiled with `make tw` — 60KB output, 25/25 tests green, all routes 200
- [ ] Retake screenshots (light + dark) and update README

## Phase 12 — Open-source GitHub release
- [x] Update `LICENSE` copyright holder (Kevin Mauel, 2026)
- [x] `README.md` — mission, quickstart, make commands, contributing
- [x] `CONTRIBUTING.md` — translations, crisis resources, code style
- [x] `CODE_OF_CONDUCT.md` — Contributor Covenant 2.1 + safe messaging note
- [x] `SECURITY.md` — responsible disclosure
- [x] `.env.example` — all vars documented
- [x] Update `.gitignore` — `/var/data/`, compiled `assets/styles/app.css`
- [x] `.github/workflows/ci.yaml` — PHPUnit + smoke test, Mercure step removed
- [x] `.github/ISSUE_TEMPLATE/` — bug report + crisis resource update templates
- [x] `.github/pull_request_template.md`
- [ ] Create public GitHub repo, initial commit, push
- [ ] Set topics, description, social preview

---

## Key decisions (locked)
- Stack: Symfony 8 / PHP 8.3 / HTMX 2 / Alpine.js / Tailwind 4
- DB: PostgreSQL via Doctrine ORM + migrations
- Privacy: ZERO user data stored (safety plan = localStorage only)
- AI: Claude API stateless, all output through SafetyOutputFilter
- Analytics: Plausible only (no Google)
- Law: German (DSGVO + Impressum required on every page)
- Deployment: Hetzner Frankfurt, operated as German e.V. nonprofit
