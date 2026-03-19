# Progress Log — Suicide Prevention Platform

## START HERE — next session prompt
"Continue from progress.md — demo is live at https://demo-suicide-prevention.tony-stark.xyz (demo/demo1234), provisioned via OpenTofu. Public GitHub repo exists and is up to date. CSS loading fixed, DaisyUI v5 theme variables added, mobile nav fixed, badges correct. Next: accessibility audit (Phase 9), fill real Impressum data (Phase 11)."

---

## What this project is
International multilingual suicide prevention platform. Symfony 8 / PHP 8.4 /
HTMX 2 / Tailwind 4 / DaisyUI 5 / Vanilla JS. Operated as a German e.V. nonprofit.
Zero user data stored. Safety plan lives in localStorage only.

---

## Phases 0–8 ✅ COMPLETE
All core features built and working: entities, migrations, services, controllers,
templates, translations (8 locales), PDF export, followup queue, security hardening.

## Phase 12 — Open-source GitHub release ✅ COMPLETE
Public GitHub repo created and up to date. Screenshots, README, CONTRIBUTING,
CODE_OF_CONDUCT, SECURITY, .env.example, CI workflow all in place.

## Phase 13 — Warm DaisyUI design ✅ COMPLETE
Custom warm-light/warm-dark DaisyUI v5 theme pair. Auto-switch by time +
prefers-color-scheme + localStorage. All DaisyUI v5 theme variables set
(--radius-selector, --border, --size-selector, etc.).

## Phase 14 — Demo deployment ✅ COMPLETE
- Provisioned via OpenTofu (terraform/ — main.tf, variables.tf, outputs.tf, cloud-init.yaml.tpl)
- Hetzner server: 162.55.173.128
- Live URL: https://demo-suicide-prevention.tony-stark.xyz (subdomain on tony-stark.xyz via GoDaddy DNS)
- Auth: HTTP basic auth — demo / demo1234
- Caddy handles TLS automatically (Let's Encrypt)
- compose.demo.yaml + .env.demo + Caddyfile.demo
- Seeding via `app:seed` command (SeedDemoDataCommand.php)
- `make demo-provision DEMO_IP=<ip>` for fresh deploys
- `make demo-redeploy DEMO_IP=<ip>` for updates (git pull + rebuild + cache:warmup)

---

## CSS / design fixes ✅ COMPLETE
- **CSS was broken**: app.source.css was inside assets/ — AssetMapper crashed parsing
  `@import "tailwindcss"`. Fixed by moving source to tailwind.source.css at project root.
  Updated Makefile tw/tw-watch targets accordingly.
- **DaisyUI v5 theme variables missing**: custom themes used v4 names (--rounded-*).
  Added --border, --radius-selector, --radius-box, --radius-field, --size-selector,
  --size-field, --depth, --noise to both warm-light and warm-dark.
- **Badge palette**: replaced badge-info (cyan) with badge-primary (amber) for 24h badges;
  badge-warning with badge-neutral for no-police indicator. Consistent with warm theme.

## Alpine.js → Vanilla JS migration ✅ COMPLETE
Alpine.js removed (CSP eval errors). Replaced with ~120 lines vanilla JS in assets/app.js:
themeManager, transparency toggle, safetyPlan builder, countrySelect.

## Mobile fixes ✅ COMPLETE
- viewport-fit=cover for iOS safe area
- Nav: home icon (SVG) on mobile, full title on sm+. Three page links fit at 390px.
- Fixed crisis button: bottom uses env(safe-area-inset-bottom)
- Footer: padding-bottom includes env(safe-area-inset-bottom)
- main pb-20 so fixed button never covers page content

---

## Open items
- [ ] Accessibility audit (Phase 9)
- [ ] Fill real Impressum data (Phase 11)

---

## Environment facts (needed every session)
- Working dir: /home/kmauel/Projects/suicide-prevention-app
- Docker stack: `make up` to start
- PHP binary: `docker compose exec php php`
- Symfony console: `make sf c="<command>"`
- Tests: `make test`
- Tailwind: `make tw` / `make tw-watch` (source: tailwind.source.css, output: assets/styles/app.compiled.css)
- GeoIP: `make geoip` (monthly refresh)
- Screenshots: `~/.nvm/versions/node/v24.14.0/bin/node /tmp/screenshots.mjs`
- Demo deploy: `make demo-provision DEMO_IP=162.55.173.128`
- Demo redeploy: `make demo-redeploy DEMO_IP=162.55.173.128`

## Key architectural gotchas
- Country PK is string `code` (not int) → CrisisResource JoinColumn needs `referencedColumnName: 'code'`
- Rate limiter: not autowireable by type — wired as `@limiter.reasons_api` in services.yaml
- MonologBundle not in Symfony 8 skeleton — installed separately
- Fallback letters in `resources/fallback_letters/` NOT `translations/`
- Tailwind source is `tailwind.source.css` (project root) — NOT inside assets/ (AssetMapper would crash on @import "tailwindcss")
- Compiled CSS: `assets/styles/app.compiled.css` — tracked in git, regenerate with `make tw`
- DB-IP MMDB lives in container volume /var/data/ — regenerate with `make geoip` on new installs
- Routes all have /{_locale} prefix → bare / needs separate entry in routes.yaml (home_root)
- FrankenPHP worker mode: cache:clear alone not enough after template changes, need container restart
- DaisyUI v5 uses --radius-selector / --border / --size-selector (not v4's --rounded-* names)
- app:seed command seeds demo data (no DoctrineFixturesBundle needed in prod)
