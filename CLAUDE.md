# Suicide Prevention Platform — Project Constitution

## What this is
An international, multilingual suicide prevention platform. Symfony 8 / PHP 8.3 / HTMX 2 / Alpine.js / Tailwind 4. Deployed on Hetzner (Frankfurt). Operated as a German e.V. nonprofit.

## Stack — never deviate from this
- **Framework:** Symfony 8 with PHP 8.3 attributes everywhere (no YAML annotations)
- **ORM:** Doctrine with migrations for every schema change
- **Frontend:** HTMX 2 for server-driven partials, Alpine.js x-data for local state only
- **CSS:** Tailwind 4 via PostCSS in AssetMapper pipeline — no separate CSS files
- **HTTP client:** Symfony HttpClient (not Guzzle)
- **Mailer:** Symfony Mailer (not SwiftMailer)
- **Templates:** Twig only — no inline PHP, no React, no Vue

## Absolute rules — never break these
1. **ZERO user data in database.** Only `countries` and `crisis_resources` tables store data. `followup_queue` stores only encrypted email + hash, wiped after send.
2. **ZERO server-side logging of user input.** POST body never touches a log file.
3. **Safety plan lives in browser localStorage only.** No endpoint receives plan data except PDF export (ephemeral, no storage).
4. **Claude API calls are stateless.** No conversation history. No session. Prompt → response → done.
5. **No Google Analytics.** Use Plausible (self-hosted) or nothing.
6. **Rate limit all AI endpoints.** 10 req/IP/hour via Symfony RateLimiter.
7. **All AI output passes through SafetyOutputFilter before rendering.**
8. **German law applies.** Every page needs Impressum link. DSGVO compliance is architectural.

## Code style
- PHP 8.3: use enums, readonly properties, constructor promotion, named arguments
- Services are final classes with constructor DI — no setter injection
- One responsibility per class — no fat controllers
- Twig partials for every HTMX target (`_letter.html.twig`, `_country.html.twig`)
- Accessibility first: ARIA labels on every interactive element, `sr-only` skip links

## Key commands
```bash
symfony serve          # local dev
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load --append
php bin/console cache:clear
php bin/console app:process-followups   # manual queue run
```

## Docs — reference these for detail
- @docs/architecture.md   — full directory tree, entities, services
- @docs/build-plan.md     — phased task list with checkboxes (track progress here)
- @docs/templates.md      — Twig template patterns and HTMX conventions
- @docs/legal.md          — DSGVO rules, Impressum requirements, German law
- @docs/seed-data.md      — Crisis resource data for all 20 countries
- @docs/translations.md   — i18n structure and all translation keys
- @docs/ai-prompts.md     — Claude API system prompts and SafetyOutputFilter patterns

## Current phase
→ See @docs/build-plan.md for checked/unchecked tasks
