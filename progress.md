# Progress Log — Suicide Prevention Platform

## START HERE — next session prompt
"Continue from progress.md — Phase 13 complete. Retake screenshots (light + dark) and update README."

---

## What this project is
International multilingual suicide prevention platform. Symfony 8 / PHP 8.4 /
HTMX 2 / Alpine.js / Tailwind 4. Operated as a German e.V. nonprofit.
Zero user data stored. Safety plan lives in localStorage only.

---

## Phase 0 ✅ COMPLETE
- Symfony 8.0.7 scaffolded, Docker stack healthy (FrankenPHP + Postgres 16 + Mailpit)
- All composer deps installed

## Phase 1 ✅ COMPLETE
- Entities: Country (string PK `code`), CrisisResource (FK `country_code`), FollowupQueue
- Repositories: CountryRepository, CrisisResourceRepository
- Migration applied; 20 countries + 32 crisis resources seeded via fixtures

## Phase 2 ✅ COMPLETE
- GeolocationService — DB-IP Lite MMDB (CC BY 4.0, no account needed)
- CrisisResourceService — getPrimary, getForCountry, getAllCountries, getResourceList
- SafetyOutputFilter — 8 blocked patterns, empty-string fallback, locale-aware fallback letters
- ClaudeService — claude-sonnet-4-20250514, stateless, 30s timeout, SafetyOutputFilter applied
- FollowupService — AES-256-CBC email encryption, 3-row schedule (+24h/+7d/+30d), wipeEmail after send
- RequestBodyStripListener — high-priority listener ensures no POST body logged
- ProcessFollowupsCommand — `app:process-followups`
- Fallback letters: 8 locales in `resources/fallback_letters/` (NOT translations/ — Symfony would try to parse .txt)

## Phase 3 ✅ COMPLETE
- 7 controllers: Home, Talk, Plan, Ai, Resource, Followup, Legal
- Locale-prefixed routing: `/{_locale}/` with de|en|ru|ko|ja|lt|uk|es
- Rate limiter wired manually in services.yaml as `@limiter.reasons_api`

## Phase 4 ✅ COMPLETE
- 16 templates: base, home, talk (+transparency partial), plan (+letter partial +pdf),
  resources (+country partial), followup (confirmed/stopped), legal, error/404

## Phase 5 ✅ COMPLETE
- 8 translation files (de, en, ru, ko, ja, lt, uk, es) — all keys present in all locales

## Phase 6 ✅ COMPLETE — folded into PlanController
- PDF export via dompdf — ephemeral, no storage

## Phase 7 ✅ COMPLETE
- ProcessFollowupsCommand drives FollowupService::processQueue()
- Full lifecycle: schedule → send → wipe encryptedEmail → delete row 24h after send

## Phase 8 ✅ COMPLETE (partial — manual testing deferred)
- NelmioSecurityBundle: X-Frame-Options DENY, X-Content-Type-Options nosniff
- Referrer-Policy: no-referrer
- CSP: default-src none, self for scripts/styles/fonts/connect
- Monolog: error-only, no POST body logging (MonologBundle installed separately)
- 25 unit tests, 41 assertions, all green

## DB-IP integration ✅ COMPLETE
- `make geoip` downloads `dbip-country-lite-{YYYY-MM}.mmdb.gz` monthly, no account
- Database at `/var/data/dbip-country-lite.mmdb` (7 MB, in container volume — not committed)
- GEOIP_DB_PATH updated in .env
- CC BY 4.0 attribution added to datenschutz.html.twig §8

## Phase 13 — Warm, inclusive design + DaisyUI + adaptive colour scheme ✅ COMPLETE
Design feedback: current palette (stone-950 near-black) reads as cold and clinical. Goal: warm, human, and inviting — not a hospital or a government site. DaisyUI added for maintainability: built-in theming, semantic component classes, far less custom CSS to maintain.

### Approach
- **DaisyUI 4** as a Tailwind CSS plugin — handles theming (`data-theme` on `<html>`), provides `btn`, `card`, `input`, `badge`, `alert` etc.
- **Custom theme pair:** `warm-light` + `warm-dark` defined in DaisyUI config — amber/brown/rose undertones, warm cream backgrounds
- **Light mode:** default for daytime — soft warm white, dark text
- **Dark mode:** warm dark (not pure black) — default at night
- **Auto-switch:** `prefers-color-scheme` → JS time-of-day (dark 20:00–07:00) → `localStorage` override → manual toggle
- **Template migration:** replace all hand-rolled utility soup with DaisyUI semantic classes (`bg-base-100`, `text-base-content`, `btn btn-primary`, etc.)

### Tasks
- [ ] Install DaisyUI 4 (npm + PostCSS or via CDN in importmap)
- [ ] Define `warm-light` + `warm-dark` custom DaisyUI themes in Tailwind config
- [ ] Update `app.source.css` to load DaisyUI plugin + custom themes
- [ ] Update `base.html.twig` — `data-theme` on `<html>`, Alpine.js theme init + toggle button
- [ ] Migrate templates to DaisyUI components — `btn`, `card`, `input`, `badge`, `navbar`, `alert`
- [ ] Remove hardcoded colour classes (`stone-950`, `stone-900`, etc.)
- [ ] Verify crisis button contrast in both modes (`btn btn-error`)
- [ ] Recompile (`make tw`) and verify all pages
- [ ] Retake screenshots (light + dark) and update README

## Phase 12 — Open-source GitHub release ✅ (code complete, push pending)
All file tasks done. Remaining: create GitHub repo + initial commit + push.

Files created:
- LICENSE — copyright Kevin Mauel 2026
- README.md — mission, quickstart, make commands, env vars table
- CONTRIBUTING.md — translations, crisis resources, code style
- CODE_OF_CONDUCT.md — Contributor Covenant 2.1 + safe messaging note
- SECURITY.md — responsible disclosure, no public CVEs for safety issues
- .env.example — every var with inline comments
- .gitignore — added /var/data/ and assets/styles/app.css
- .github/workflows/ci.yaml — PHPUnit, migrations, schema validate, lint:container
- .github/ISSUE_TEMPLATE/bug_report.md
- .github/ISSUE_TEMPLATE/crisis_resource_update.md
- .github/pull_request_template.md

Still needed (human actions):
- Create public GitHub repo (name suggestion: `suicide-prevention-app` or `krisenhilfe`)
- Verify no secrets in files: `git diff --stat` + scan .env files
- Initial commit + push
- Set repo topics: suicide-prevention, mental-health, symfony, php, htmx, multilingual, nonprofit
- Add description + social preview image on GitHub
- Update SECURITY.md with real contact email once domain is live

---

## Environment facts (needed every session)
- Working dir: /home/kmauel/Projects/suicide-prevention-app
- Docker stack: `make up` to start
- ALL project dirs are root-owned (Docker bind mount) → write files via:
  `docker compose exec php bash -c 'cat > /app/path/file' << 'EOF'`
  OR use `docker compose exec php tee /app/path/file << 'EOF'`
- PHP binary: `docker compose exec php php`
- Symfony console: `make sf c="<command>"`
- Tests: `make test`
- Tailwind: `make tw` (compile once) / `make tw-watch` (dev)
- GeoIP: `make geoip` (monthly refresh)

## Key architectural gotchas
- Country PK is string `code` (not int) → CrisisResource JoinColumn needs
  `referencedColumnName: 'code'`
- Rate limiter: not autowireable by type — wired as `@limiter.reasons_api` in services.yaml
- MonologBundle not in Symfony 8 skeleton — installed separately
- Fallback letters in `resources/fallback_letters/` NOT `translations/`
  (Symfony translator picks up *.{locale}.{format} files and .txt has no registered loader)
- assets/styles/app.css is the COMPILED Tailwind output; source is app.source.css
- DB-IP MMDB lives in container volume /var/data/ — regenerate with `make geoip` on new installs
