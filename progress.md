# Progress Log — Suicide Prevention Platform

## START HERE — next session prompt
"Continue from progress.md — demo is live at https://demo-suicide-prevention.tony-stark.xyz (demo/demo1234), provisioned via OpenTofu. Public GitHub repo exists (initial release pushed). Alpine.js removed, vanilla JS replaces it. Navigation added. CSP hardened with nonces. Next: commit all local changes, retake screenshots, accessibility audit (Phase 9), fill Impressum data (Phase 11)."

---

## What this project is
International multilingual suicide prevention platform. Symfony 8 / PHP 8.4 /
HTMX 2 / Tailwind 4 / DaisyUI. Operated as a German e.V. nonprofit.
Zero user data stored. Safety plan lives in localStorage only.
Alpine.js removed — replaced with plain vanilla JS.

---

## Phases 0–8 ✅ COMPLETE
See previous entries below. All core features built and working.

## Phase 13 — Warm DaisyUI design ✅ COMPLETE
Custom warm-light/warm-dark DaisyUI theme pair. Auto-switch by time + prefers-color-scheme + localStorage.

## Phase 12 — Open-source GitHub release ✅ COMPLETE
Public GitHub repo created and pushed ("Initial public release"). Screenshots taken and wired into README.

---

## Demo deployment (Phase 14) ✅ COMPLETE
- Provisioned via OpenTofu (terraform/ — main.tf, variables.tf, outputs.tf, cloud-init.yaml.tpl)
- Hetzner server: 162.55.173.128
- Live URL: https://demo-suicide-prevention.tony-stark.xyz (subdomain on tony-stark.xyz via GoDaddy DNS)
- Auth: HTTP basic auth — demo / demo1234
- Caddy handles TLS automatically (Let's Encrypt)
- compose.demo.yaml + .env.demo + Caddyfile.demo
- Seeding via `app:seed` command (no DoctrineFixturesBundle needed in prod)
- GeoIP MMDB at /var/data/dbip-country-lite.mmdb (volume-mounted)
- `make demo-provision DEMO_IP=<ip>` for fresh deploys
- `make demo-redeploy` for updates

## Alpine.js → Vanilla JS migration ✅ COMPLETE
Alpine.js removed entirely (was causing CSP eval errors, CSP build incompatible without bundler).
Replaced with ~120 lines of vanilla JS in assets/app.js:
- themeManager: data-theme on <html>, toggle button, FOUC prevention via inline nonce-script in <head>
- faqItem (transparency button): aria-expanded + arrow toggle via data-arrow-up/data-arrow-down
- safetyPlan: full DOM-based plan builder (renderList, textRow, contacts, add/remove/save/export)
- countrySelect: htmx.ajax() on change event
- HTMX CSRF injection preserved

## CSP hardening ✅ COMPLETE
- NelmioSecurityBundle nonces on script-src
- importmap() receives nonce via {{ importmap('app', {nonce: csp_nonce('script')}) }}
- browsing-topics Permissions-Policy removed from Caddyfile.demo (deprecated, browser warning)
- data: added to script-src (Symfony AssetMapper CSS loader)
- hx-boost removed from <body> (caused HTMX crash on <a> without href)

## Navigation ✅ COMPLETE
- Global nav bar added to base.html.twig
- Links: app title (home) / Talk / Safety plan / Crisis lines
- Active page highlighted
- Translation keys nav.main/talk/plan/resources added to all 8 locale files

---

## Uncommitted local changes (need git commit)
- src/Command/SeedDemoDataCommand.php (new)
- src/Controller/HomeController.php (root redirect + home_country route)
- config/routes.yaml (home_root standalone route)
- config/packages/nelmio_security.yaml (nonce + data: in script-src)
- Makefile (fixtures:load → app:seed, /var/data mkdir)
- importmap.php (alpinejs removed)
- assets/app.js (full vanilla JS rewrite)
- assets/vendor/ (alpinejs removed)
- frankenphp/Caddyfile (browsing-topics removed)
- frankenphp/Caddyfile.demo (browsing-topics removed)
- templates/base.html.twig (nav, no Alpine, nonce script, hx-boost removed)
- templates/home/index.html.twig (faqItem → vanilla)
- templates/plan/index.html.twig (full Alpine removal, data-* attrs)
- templates/resources/index.html.twig (Alpine removal, data-base-url)
- translations/messages.*.yaml (nav keys added)

## Open items
- [ ] git commit all local changes
- [ ] Retake screenshots (light + dark) and update README
- [ ] Accessibility audit (Phase 9)
- [ ] Fill real Impressum data (Phase 11)

---

## Environment facts (needed every session)
- Working dir: /home/kmauel/Projects/suicide-prevention-app
- Docker stack: `make up` to start
- PHP binary: `docker compose exec php php`
- Symfony console: `make sf c="<command>"`
- Tests: `make test`
- Tailwind: `make tw` / `make tw-watch`
- GeoIP: `make geoip` (monthly refresh)
- Demo deploy: `make demo-provision DEMO_IP=162.55.173.128`
- Demo redeploy: `make demo-redeploy DEMO_IP=162.55.173.128`

## Key architectural gotchas
- Country PK is string `code` (not int) → CrisisResource JoinColumn needs `referencedColumnName: 'code'`
- Rate limiter: not autowireable by type — wired as `@limiter.reasons_api` in services.yaml
- MonologBundle not in Symfony 8 skeleton — installed separately
- Fallback letters in `resources/fallback_letters/` NOT `translations/`
- assets/styles/app.css is COMPILED Tailwind output; source is app.source.css
- DB-IP MMDB lives in container volume /var/data/ — regenerate with `make geoip` on new installs
- Routes all have /{_locale} prefix → bare / needs separate entry in routes.yaml (home_root)
- Demo server builds from /app on remote — scp changed files before `make demo-provision`
- FrankenPHP worker mode: cache:clear alone not enough after template changes, need container restart
