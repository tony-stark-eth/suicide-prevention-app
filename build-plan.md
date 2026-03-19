# Build Plan — Prevention Platform

## How to use this
Claude Code: check off boxes as tasks complete. Use `ultrathink` for complex service logic.
Start each session with: "Continue from @docs/build-plan.md"

---

## Phase 0 — Project scaffold ✅ COMPLETE
- [x] `composer create-project symfony/skeleton prevention-platform`
- [x] Install dependencies
- [x] Configure `config/packages/doctrine.yaml` — PostgreSQL
- [x] Configure `config/packages/translation.yaml` — default locale `de`, fallback `en`, 8 locales
- [x] Configure `config/packages/framework.yaml` — rate limiter, csrf, asset_mapper
- [x] Configure `config/packages/monolog.yaml` — error-only logging, no POST bodies
- [x] Configure `config/packages/rate_limiter.yaml` — `reasons_api`: 10/hour sliding window
- [x] `.env` and `.gitignore` configured
- [x] `importmap.php` with htmx@2.0.3 (Alpine.js removed — replaced with vanilla JS)
- [x] Tailwind 4 via PostCSS in asset pipeline + DaisyUI 5
- [x] `assets/app.js` — vanilla JS (themeManager, faqItem, safetyPlan, countrySelect)
- [x] `assets/styles/app.css` with Tailwind + DaisyUI directives

## Phase 1 — Database entities + migrations ✅ COMPLETE
- [x] `src/Entity/Country.php`
- [x] `src/Entity/CrisisResource.php`
- [x] `src/Entity/FollowupQueue.php`
- [x] Repositories for Country and CrisisResource
- [x] Migrations applied
- [x] Fixtures loaded — 20 countries + all crisis resources from seed-data.md

## Phase 2 — Core services ✅ COMPLETE
- [x] `GeolocationService.php` — DB-IP Lite MMDB, fallback 'de'
- [x] `CrisisResourceService.php`
- [x] `SafetyOutputFilter.php`
- [x] `ClaudeService.php` — claude-sonnet-4-20250514, stateless
- [x] `FollowupService.php` — AES-256-CBC
- [x] `RequestBodyStripListener.php`

## Phase 3 — Controllers + routing ✅ COMPLETE
- [x] All controllers created (Home, Talk, Plan, Ai, Resource, Followup, Legal)
- [x] `config/routes.yaml` — locale prefix + bare `/` standalone entry (home_root)
- [x] CSRF header injection in `assets/app.js`

## Phase 4 — Templates ✅ COMPLETE
- [x] All templates created — base, home, talk, plan, resources, followup, legal, error
- [x] Alpine.js x-data removed — vanilla JS data-* attributes used instead
- [x] Global nav bar in base.html.twig
- [x] DaisyUI semantic classes throughout

## Phase 5 — Translations ✅ COMPLETE
- [x] All 8 locale files: de, en, ru, ko, ja, lt, uk, es
- [x] nav.main/talk/plan/resources keys added to all locales

## Phase 6 — PDF export ✅ COMPLETE
- [x] dompdf installed, PlanController::export() renders PDF

## Phase 7 — Followup queue ✅ COMPLETE
- [x] `ProcessFollowupsCommand.php`, email templates, Symfony Mailer configured

## Phase 8 — Security hardening ✅ COMPLETE
- [x] NelmioSecurityBundle CSP with script-src nonces
- [x] Security headers, rate limiter tested
- [x] browsing-topics Permissions-Policy removed (deprecated)
- [x] data: added to script-src for AssetMapper CSS loader

## Phase 9 — Accessibility + mobile (PENDING)
- [ ] axe-core audit on all pages
- [ ] Keyboard navigation on safety plan builder
- [ ] Screen reader test (NVDA + Firefox)
- [ ] Color contrast ≥ 4.5:1
- [ ] 320px width test, touch targets ≥ 44px

## Phase 10 / Phase 14 — Deployment ✅ COMPLETE (demo)
- [x] Provisioned via OpenTofu (terraform/ — main.tf, variables.tf, outputs.tf, cloud-init.yaml.tpl)
- [x] Demo: Hetzner 162.55.173.128, Caddy TLS, HTTP basic auth (demo/demo1234)
- [x] Live URL: https://demo-suicide-prevention.tony-stark.xyz
- [x] compose.demo.yaml + .env.demo + Caddyfile.demo
- [x] `app:seed` command (SeedDemoDataCommand.php)
- [x] `make demo-provision DEMO_IP=<ip>` / `make demo-redeploy`
- [ ] Plausible Analytics (self-hosted)
- [ ] Hetzner automated backups
- [ ] unattended-upgrades

## Phase 11 — Launch prep (PENDING)
- [ ] Fill in real Impressum data (name, address, Vereinsregisternummer)
- [ ] Legal review of Datenschutzerklärung (budget €300)
- [ ] Apply to Google for Nonprofits
- [ ] Submit to IASP crisis centre directory
- [ ] Submit to findahelpline.com
- [ ] Google Search Console + sitemap

## Phase 12 — Open-source GitHub release ✅ COMPLETE
- [x] LICENSE, README.md, CONTRIBUTING.md, CODE_OF_CONDUCT.md, SECURITY.md
- [x] .env.example, .gitignore updated
- [x] .github/workflows/ci.yaml — PHPUnit on push/PR
- [x] .github/ISSUE_TEMPLATE/ and pull_request_template.md
- [x] Public GitHub repo created, initial commit pushed, screenshots in README

## Phase 13 — Warm DaisyUI design ✅ COMPLETE
- [x] DaisyUI 5 via Bun, custom warm-light/warm-dark theme pair
- [x] Auto-switch: time-of-day + prefers-color-scheme + localStorage
- [x] All templates migrated to DaisyUI semantic classes
- [x] themeManager in vanilla JS
- [ ] Retake screenshots (light + dark) and update README
