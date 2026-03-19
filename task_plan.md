# Task Plan — Suicide Prevention Platform

## Status: Demo live, open items below

---

## Phase 0 — Project scaffold ✅ COMPLETE
- [x] Symfony 8 scaffolded, all composer dependencies installed
- [x] Docker stack: FrankenPHP (PHP 8.4) + PostgreSQL 16 + Mailpit
- [x] Dockerfile, compose.yaml, compose.override.yaml, Makefile in place
- [x] `config/packages/doctrine.yaml` — PostgreSQL, server_version 16
- [x] `config/packages/translation.yaml` — default `de`, fallback `en`, 8 locales
- [x] `config/packages/monolog.yaml` — error-only, no POST bodies
- [x] `config/packages/rate_limiter.yaml` — `reasons_api`: 10/hour sliding window
- [x] `importmap.php` with htmx@2.0.3 (Alpine.js removed — replaced with vanilla JS)
- [x] Tailwind 4 via PostCSS in asset pipeline
- [x] `assets/app.js` entry point (vanilla JS — themeManager, faqItem, safetyPlan, countrySelect)
- [x] `assets/styles/app.css` with Tailwind + DaisyUI directives

## Phase 1 — Database entities + migrations ✅ COMPLETE
- [x] `src/Entity/Country.php`
- [x] `src/Entity/CrisisResource.php`
- [x] `src/Entity/FollowupQueue.php`
- [x] `CountryRepository.php` and `CrisisResourceRepository.php`
- [x] Migrations run, schema applied
- [x] Fixtures loaded — 20 countries, all crisis resources from seed-data.md

## Phase 2 — Core services ✅ COMPLETE
- [x] `GeolocationService.php` — DB-IP Lite MMDB, fallback 'de'
- [x] `CrisisResourceService.php`
- [x] `SafetyOutputFilter.php`
- [x] `ClaudeService.php` — claude-sonnet-4-20250514, stateless
- [x] `FollowupService.php` — AES-256-CBC
- [x] `RequestBodyStripListener.php`

## Phase 3 — Controllers + routing ✅ COMPLETE
- [x] `HomeController.php` — `/`, `/{_locale}`, `home_root` standalone route
- [x] `TalkController.php`
- [x] `PlanController.php`
- [x] `AiController.php` — rate-limited
- [x] `ResourceController.php`
- [x] `FollowupController.php`
- [x] `LegalController.php`
- [x] `config/routes.yaml` — locale prefix + bare `/` entry
- [x] CSRF header injection in `assets/app.js`

## Phase 4 — Templates ✅ COMPLETE
- [x] All templates created (base, home, talk, plan, resources, followup, legal, error)

## Phase 5 — Translations ✅ COMPLETE
- [x] All 8 locale files: de, en, ru, ko, ja, lt, uk, es
- [x] nav.main/talk/plan/resources keys added to all locales

## Phase 6 — PDF export ✅ COMPLETE
- [x] `PlanController::export()` renders PDF via dompdf

## Phase 7 — Followup queue ✅ COMPLETE
- [x] `ProcessFollowupsCommand.php`
- [x] Email templates
- [x] Symfony Mailer configured

## Phase 8 — Security hardening ✅ COMPLETE
- [x] NelmioSecurityBundle CSP with nonces
- [x] `nelmio_security.yaml` — nonce on script-src, data: for AssetMapper
- [x] Security headers configured
- [x] Rate limiter tested

## Phase 13 — Warm DaisyUI design ✅ COMPLETE
- [x] DaisyUI 5 installed via Bun
- [x] Custom `warm-light` / `warm-dark` theme pair
- [x] Auto-switch: time-of-day + prefers-color-scheme + localStorage
- [x] All templates migrated to DaisyUI semantic classes
- [x] themeManager in vanilla JS (Alpine.js removed entirely)
- [ ] Retake screenshots (light + dark) and update README

## Phase 14 — Demo deployment ✅ COMPLETE
- [x] Provisioned via OpenTofu (terraform/ — main.tf, variables.tf, outputs.tf, cloud-init.yaml.tpl)
- [x] Live at https://demo-suicide-prevention.tony-stark.xyz (demo/demo1234)
- [x] Hetzner 162.55.173.128, Caddy TLS, HTTP basic auth
- [x] compose.demo.yaml + .env.demo + Caddyfile.demo
- [x] `app:seed` command (SeedDemoDataCommand.php)
- [x] `make demo-provision` / `make demo-redeploy`

## Alpine.js → Vanilla JS migration ✅ COMPLETE
- [x] Alpine.js removed from importmap.php and assets/vendor/
- [x] ~120 lines vanilla JS in assets/app.js replaces all Alpine components
- [x] CSP eval errors resolved

## Phase 12 — Open-source GitHub release ✅ COMPLETE
- [x] LICENSE, README.md, CONTRIBUTING.md, CODE_OF_CONDUCT.md, SECURITY.md
- [x] .env.example, .gitignore updated
- [x] .github/workflows/ci.yaml
- [x] .github/ISSUE_TEMPLATE/ and pull_request_template.md
- [x] Public GitHub repo created, initial commit pushed ("Initial public release")
- [x] Screenshots taken and wired into README
- [ ] Set topics, description, social preview (optional)

## CSS / design fixes ✅ COMPLETE
- [x] Fix AssetMapper crash (moved tailwind.source.css out of assets/)
- [x] Add missing DaisyUI v5 theme variables (--border, --radius-selector, --size-selector, etc.)
- [x] Fix badge palette (badge-primary for 24h, badge-neutral for no-police)
- [x] Retake screenshots (light + dark, all 4 pages)

## Mobile fixes ✅ COMPLETE
- [x] viewport-fit=cover for iOS safe area
- [x] Nav: home icon on mobile, full title on sm+
- [x] Fixed button + footer safe-area insets
- [x] main pb-20 prevents button from covering content

## Pre-outreach fixes ✅ COMPLETE
- [x] Change default locale from German to English (translation.yaml, routes.yaml, HomeController)
- [x] Fix hardcoded "← Zurück" on followup/stopped.html.twig → use `nav.back|trans`
- [x] Fix hardcoded German rate-limit error in plan/_letter.html.twig → use `plan.rate_limited|trans`
- [x] Fix hardcoded "Remove" aria-label in app.js → use `data-remove-label` from template
- [x] Fix PDF all-German (pdf.html.twig) → all strings via `|trans`, 8 locale keys added
- [x] Fix PDF download filename "sicherheitsplan.pdf" → locale-aware via translation key + data attribute
- [x] Add `nav.back`, `plan.rate_limited`, `plan.remove`, `pdf.*` to all 8 locale files

## Open items (human tasks — cannot be automated)
- [ ] Fill real Impressum data: e.V. name, address, registration number, Vorstand (Phase 11)
- [ ] Replace `kontakt@yourdomain.help` and `datenschutz@yourdomain.help` in impressum/datenschutz templates
- [ ] Set GitHub repo topics, description, and social preview image (Phase 12)

## Open items (code — post-outreach)
- [ ] Accessibility audit (Phase 9)
- [ ] Theme toggle aria-label is English-only (app.js line 23) — needs data-attribute like Remove buttons

---

## Key decisions (locked)
- Stack: Symfony 8 / PHP 8.4 / HTMX 2 / Tailwind 4 / DaisyUI 5 / Vanilla JS (no Alpine)
- DB: PostgreSQL via Doctrine ORM + migrations
- Privacy: ZERO user data stored (safety plan = localStorage only)
- AI: Claude API stateless, all output through SafetyOutputFilter
- Analytics: Plausible only (no Google)
- Law: German (DSGVO + Impressum required on every page)
- Deployment: Hetzner Frankfurt, operated as German e.V. nonprofit
