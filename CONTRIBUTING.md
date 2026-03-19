# Contributing

Thank you for helping make this platform better. Every contribution — from fixing a phone number to adding a whole new language — directly helps people in crisis.

---

## Most impactful: crisis resource updates

Phone numbers, websites, and text lines change. Outdated numbers are dangerous.

**How to report an outdated or missing resource:**
Use the [crisis resource update issue template](.github/ISSUE_TEMPLATE/crisis_resource_update.md). Include:
- Country code (ISO 3166-1 alpha-2, e.g. `DE`, `US`)
- The old value (if applicable)
- The new/correct value
- A source URL (government health authority, IASP directory, or findahelpline.com preferred)

**How to add a resource in code:**
1. Open `src/DataFixtures/CrisisResourceFixtures.php`
2. Add an entry following the existing pattern
3. Open `docs/seed-data.md` and add the same entry there (it's the canonical reference)
4. Run `make sf c="doctrine:fixtures:load --append"` to verify it loads without error

---

## Adding or improving translations

Translation files live in `translations/messages.{locale}.yaml`.

Supported locales: `de`, `en`, `ru`, `ko`, `ja`, `lt`, `uk`, `es`.

**To add a new locale:**
1. Copy `translations/messages.en.yaml` to `translations/messages.{locale}.yaml`
2. Translate all values (leave keys unchanged)
3. Add `{locale}` to the `requirements: { _locale: ... }` list in `config/routes.yaml`
4. Add a fallback letter at `resources/fallback_letters/{locale}.txt` — this is displayed if the AI is unavailable. It must follow safe messaging guidelines (no methods, no statistics, focus on connection and hope).
5. Add the locale to the language switcher in `templates/base.html.twig`

**Safe messaging:** All AI-generated content goes through `SafetyOutputFilter`. If you add a fallback letter, it will be shown verbatim — please have it reviewed by someone with mental health communication experience.

---

## Bug fixes and features

1. Fork the repo and create a feature branch (`git checkout -b fix/my-fix`)
2. Make your changes
3. Run `make test` — all 25 tests must pass
4. Check for PHP errors: `make sf c="lint:container"`
5. Open a pull request using the PR template

### Code style

- PHP 8.4: enums, readonly properties, constructor promotion, named arguments
- Services: `final` classes with constructor DI — no setter injection
- One responsibility per class — no fat controllers
- No inline PHP in Twig templates
- Tailwind 4 utility classes in templates — no separate CSS files

### Absolute rules (from the project constitution in `CLAUDE.md`)

- ZERO user data stored in the database
- Safety plan stays in `localStorage` only
- Claude API calls are stateless — no conversation history
- All AI output through `SafetyOutputFilter` before rendering
- No Google Analytics

---

## Development setup

See [README.md](README.md) for the full quickstart.

```bash
cp .env.example .env.local
make start
make geoip
make sf-migrate
make sf c="doctrine:fixtures:load --append"
```

---

## Code of Conduct

This project follows the [Contributor Covenant 2.1](CODE_OF_CONDUCT.md). Be kind.

Given the subject matter, please be especially mindful in discussions. Lived experience of mental health crises is valuable — and sometimes re-triggering. We follow safe messaging guidelines in all project communications.
