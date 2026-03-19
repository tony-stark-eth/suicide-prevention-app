# Architecture Reference

## Directory tree (target state)
```
prevention-platform/
├── CLAUDE.md
├── .env
├── .env.local              # gitignored
├── composer.json
├── importmap.php
├── tailwind.config.js
├── config/
│   └── packages/
│       ├── doctrine.yaml
│       ├── framework.yaml
│       ├── monolog.yaml
│       ├── rate_limiter.yaml
│       ├── translation.yaml
│       └── security.yaml
├── src/
│   ├── Controller/
│   │   ├── HomeController.php
│   │   ├── TalkController.php
│   │   ├── PlanController.php       # includes PDF export
│   │   ├── AiController.php
│   │   ├── ResourceController.php
│   │   ├── FollowupController.php
│   │   └── LegalController.php
│   ├── Service/
│   │   ├── ClaudeService.php
│   │   ├── GeolocationService.php
│   │   ├── CrisisResourceService.php
│   │   ├── FollowupService.php
│   │   └── SafetyOutputFilter.php
│   ├── Entity/
│   │   ├── Country.php
│   │   ├── CrisisResource.php
│   │   └── FollowupQueue.php
│   ├── Repository/
│   │   ├── CountryRepository.php
│   │   └── CrisisResourceRepository.php
│   ├── Command/
│   │   └── ProcessFollowupsCommand.php
│   ├── EventListener/
│   │   └── RequestBodyStripListener.php
│   └── DataFixtures/
│       ├── CountryFixtures.php
│       └── CrisisResourceFixtures.php
├── templates/
│   ├── base.html.twig
│   ├── home/index.html.twig
│   ├── talk/
│   │   ├── index.html.twig
│   │   └── _transparency.html.twig   # HTMX partial
│   ├── plan/
│   │   ├── index.html.twig
│   │   ├── _letter.html.twig         # HTMX partial
│   │   └── pdf.html.twig             # dompdf layout
│   ├── resources/
│   │   ├── index.html.twig
│   │   └── _country.html.twig        # HTMX partial
│   ├── followup/
│   │   ├── _confirmed.html.twig      # HTMX swap
│   │   └── stopped.html.twig
│   ├── email/
│   │   └── checkin.html.twig
│   ├── error/
│   │   ├── error.html.twig
│   │   └── error404.html.twig
│   └── legal/
│       ├── impressum.html.twig
│       └── datenschutz.html.twig
├── translations/
│   ├── messages.de.yaml
│   ├── messages.en.yaml
│   ├── messages.ru.yaml
│   ├── messages.ko.yaml
│   ├── messages.ja.yaml
│   ├── messages.lt.yaml
│   ├── messages.uk.yaml
│   ├── messages.es.yaml
│   ├── fallback_letter.de.txt
│   └── fallback_letter.en.txt        # also create for each locale
├── assets/
│   ├── app.js
│   └── styles/app.css
└── docs/                              # this folder — Claude Code reads on demand
```

---

## Composer dependencies

```json
{
  "require": {
    "php": ">=8.3",
    "symfony/framework-bundle": "^8.0",
    "symfony/twig-bundle": "^8.0",
    "doctrine/orm": "^3.0",
    "doctrine/doctrine-bundle": "^2.12",
    "doctrine/doctrine-migrations-bundle": "^3.0",
    "doctrine/doctrine-fixtures-bundle": "^3.0",
    "symfony/translation": "^8.0",
    "symfony/asset-mapper": "^8.0",
    "symfony/http-client": "^8.0",
    "symfony/mailer": "^8.0",
    "symfony/rate-limiter": "^8.0",
    "symfony/security-bundle": "^8.0",
    "symfony/lock": "^8.0",
    "geoip2/geoip2": "^3.0",
    "twig/extra-bundle": "^3.0",
    "twig/intl-extra": "^3.0",
    "nelmio/security-bundle": "^3.4",
    "dompdf/dompdf": "^2.0"
  },
  "require-dev": {
    "symfony/debug-bundle": "^8.0",
    "symfony/web-profiler-bundle": "^8.0",
    "phpunit/phpunit": "^11",
    "symfony/test-pack": "^1.0"
  }
}
```

---

## Entity: Country

```php
#[ORM\Entity(repositoryClass: CountryRepository::class)]
#[ORM\Table(name: 'countries')]
class Country
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 2)]
    private string $code;               // ISO 3166-1 alpha-2: 'de', 'kr', 'ru'

    #[ORM\Column(type: 'string')]
    private string $nameEn;

    #[ORM\Column(type: 'string')]
    private string $nameLocal;

    #[ORM\Column(type: 'string', length: 10)]
    private string $primaryLanguage;    // maps to locale: 'de', 'ko', 'ru'

    #[ORM\Column(type: 'string', length: 5)]
    private string $flagEmoji;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $suicideIllegal;       // affects UI messaging — do not show in 20+ countries

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private bool $policeDispatchRisk;   // show transparency notice if true

    #[ORM\OneToMany(mappedBy: 'country', targetEntity: CrisisResource::class, fetch: 'EXTRA_LAZY')]
    #[ORM\OrderBy(['sortOrder' => 'ASC'])]
    private Collection $resources;
}
```

## Entity: CrisisResource

```php
#[ORM\Entity(repositoryClass: CrisisResourceRepository::class)]
#[ORM\Table(name: 'crisis_resources')]
class CrisisResource
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Country::class, inversedBy: 'resources')]
    #[ORM\JoinColumn(nullable: false)]
    private Country $country;

    // Enum values: hotline_phone | hotline_text | hotline_chat |
    //              online_therapy | peer_support | youth | lgbtq |
    //              therapist_finder | self_help
    #[ORM\Column(type: 'string', length: 30)]
    private string $type;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $phone;             // formatted: "0800 111 0 111"

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $url;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $textNumber;        // SMS short code where applicable

    #[ORM\Column(type: 'boolean')]
    private bool $is24h;

    #[ORM\Column(type: 'boolean')]
    private bool $isFree;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $noPoliceByDefault;    // e.g. Trans Lifeline — surface as trust signal

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $isPrimary;            // one per country — shown in hero button

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $languages;          // ['de', 'tr', 'en']

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $sortOrder;
}
```

## Entity: FollowupQueue

```php
// STORES: no plaintext email, no name, no plan data
#[ORM\Entity]
#[ORM\Table(name: 'followup_queue')]
class FollowupQueue
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
    private int $id;

    // SHA-256(email + APP_SECRET) — for deduplication and unsubscribe token
    #[ORM\Column(type: 'string', length: 64, unique: true)]
    private string $emailHash;

    // AES-256-CBC encrypted email — wiped (set to '') immediately after send
    #[ORM\Column(type: 'text')]
    private string $encryptedEmail;

    #[ORM\Column(type: 'string', length: 2)]
    private string $countryCode;

    #[ORM\Column(type: 'string', length: 10)]
    private string $locale;

    // Three rows created per opt-in: +24h, +7d, +30d
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $sendAt;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private bool $sent;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $sentAt;

    // Row deleted by ProcessFollowupsCommand 24h after sentAt
}
```

---

## Services overview

**ClaudeService** — `generateReasonLetter(string $name, string $locale): string`
- POST to Anthropic API, model `claude-sonnet-4-20250514`, max_tokens 800
- System prompt loaded from @docs/ai-prompts.md
- 30s timeout, no retries (fail silently → SafetyOutputFilter returns fallback)
- $name is never logged — goes out of scope after API call

**GeolocationService** — `detect(?string $ip): string`
- Uses MaxMind GeoLite2-Country.mmdb via geoip2/geoip2
- Returns ISO 3166-1 alpha-2 lowercase, default 'de' on any failure

**CrisisResourceService**
- `getPrimary(string $countryCode): ?CrisisResource`
- `getForCountry(string $countryCode): array` — grouped by type
- `getCountry(string $countryCode): ?Country`
- `getAllCountries(): array` — sorted by nameEn, for dropdown

**SafetyOutputFilter** — `screen(string $text): string`
- Regex patterns blocking method references, system prompt leakage
- Returns pre-written fallback letter on any match
- See @docs/ai-prompts.md for full pattern list

**FollowupService**
- `schedule(string $email, string $countryCode, string $locale): void` — creates 3 FollowupQueue rows
- `processQueue(): void` — called by cron, sends due emails, wipes encryptedEmail after send
- `cancelByToken(string $token): void` — deletes unsent rows by emailHash
- Uses OpenSSL AES-256-CBC for email encryption

---

## Environment variables

```dotenv
# .env (committed — no secrets)
APP_ENV=prod
APP_SECRET=                     # generate with: php bin/console secrets:generate-keys

DATABASE_URL="postgresql://app:secret@127.0.0.1:5432/prevention"
MAILER_DSN=smtp://localhost:25

ANTHROPIC_API_KEY=              # .env.local only
GEOIP_DB_PATH=/var/data/GeoLite2-Country.mmdb
FOLLOWUP_FROM_EMAIL=checkin@yourdomain.help
FOLLOWUP_FROM_NAME="Someone cares"
```

---

## Rate limiter config

```yaml
# config/packages/rate_limiter.yaml
framework:
    rate_limiter:
        reasons_api:
            policy: sliding_window
            limit: 10
            interval: '1 hour'
            lock_factory: lock.default.factory
```

Inject as `RateLimiterFactory $reasonsApiLimiter` in AiController constructor.
Create limiter key from IP: `$limiter->create($request->getClientIp())`.
Return 429 JsonResponse if `!$limiter->consume(1)->isAccepted()`.
