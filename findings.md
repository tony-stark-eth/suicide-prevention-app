# Findings — Suicide Prevention Platform

## Project overview
International, multilingual suicide prevention platform. German e.V. nonprofit, deployed on Hetzner Frankfurt.

## Stack (locked — never deviate)
- Symfony 8, PHP 8.4 (attributes everywhere, no YAML annotations)
- Doctrine ORM + migrations (PostgreSQL)
- HTMX 2 for server-driven partials
- Vanilla JS for local state (Alpine.js removed — caused CSP eval errors)
- Tailwind 4 + DaisyUI 5 via PostCSS in AssetMapper pipeline
- Symfony HttpClient, Symfony Mailer
- Twig only (no React, no Vue, no inline PHP)

## Privacy constraints (architectural, not negotiable)
1. Only `countries` and `crisis_resources` tables store data
2. `followup_queue` stores only encrypted email + hash — wiped after send
3. POST body never touches logs
4. Safety plan lives in browser localStorage ONLY
5. Claude API: stateless, no history, no session
6. No Google Analytics — Plausible (self-hosted) only
7. All AI output through SafetyOutputFilter before rendering
8. DSGVO + Impressum on every page

## AI integration
- Model: `claude-sonnet-4-20250514`
- Rate limit: 10 req/IP/hour via Symfony RateLimiter (sliding window)
- User name: never logged, out of scope after API call
- Fallback: SafetyOutputFilter returns pre-written letter on any pattern match

## Data model
- **Country**: ISO alpha-2 code, nameEn, nameLocal, primaryLanguage, flagEmoji, suicideIllegal, policeDispatchRisk
- **CrisisResource**: country FK, type (enum), name, description, phone, url, textNumber, is24h, isFree, noPoliceByDefault, isPrimary, languages, sortOrder
- **FollowupQueue**: emailHash (SHA-256), encryptedEmail (AES-256-CBC, wiped post-send), countryCode, locale, sendAt (+24h/+7d/+30d rows), sent, sentAt

## Supported countries (20)
Full seed data in seed-data.md. Includes DE, AT, CH, US, GB, AU, CA, KR, JP, RU, UA, FR, ES, BR, MX, IN, ZA, NG, IL, NZ.

## Supported locales (8)
de, en, ru, ko, ja, lt, uk, es

## Security
- NelmioSecurityBundle for CSP
- X-Frame-Options: DENY, X-Content-Type-Options: nosniff, Referrer-Policy: no-referrer
- RequestBodyStripListener removes POST bodies from logs

## Deployment target
- Hetzner CX21, Frankfurt region
- nginx + FrankenPHP (or PHP-FPM 8.3) + PostgreSQL 16
- Let's Encrypt SSL
- UFW: ports 80, 443, 22 only
