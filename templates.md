# Templates & Frontend Patterns

## Core conventions

- Dark theme: `bg-stone-950 text-stone-100` as body defaults
- All interactive elements need explicit ARIA labels
- Every page has a skip-to-content link as first focusable element
- Crisis button fixed bottom-right on every page except legal pages
- HTMX partials are prefixed with `_` (e.g. `_letter.html.twig`, `_country.html.twig`)
- No inline styles — Tailwind utilities only
- RTL languages (Arabic/Hebrew/Farsi if added later): use `dir` attribute on `<html>`

---

## base.html.twig structure

```twig
<!DOCTYPE html>
<html lang="{{ app.request.locale }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {# NO Google Analytics tag here — Plausible only #}
    {# NO Facebook Pixel #}
    <title>{% block title %}{{ 'app.title'|trans }}{% endblock %}</title>
    {{ importmap('app') }}
</head>
<body hx-boost="true" class="min-h-screen bg-stone-950 text-stone-100 font-sans antialiased">

    {# Skip link — accessibility #}
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 z-50 bg-blue-600 text-white px-4 py-2 rounded">
        {{ 'nav.skip_to_content'|trans }}
    </a>

    {% block body %}{% endblock %}

    {# Crisis button — fixed, always visible, except on legal pages #}
    {% if not (app.request.attributes.get('_route') starts with 'legal') %}
    <div class="fixed bottom-4 right-4 z-50" aria-label="{{ 'talk.crisis_region_aria'|trans }}">
        <a href="{{ path('talk') }}"
           class="flex items-center gap-2 bg-red-700 hover:bg-red-600 text-white text-sm font-medium px-5 py-3 rounded-full shadow-xl transition-colors focus:ring-4 focus:ring-red-400 focus:outline-none">
            {{ 'talk.crisis_button'|trans }}
        </a>
    </div>
    {% endif %}

    <footer class="border-t border-stone-800 py-6 px-4 mt-16 text-center text-stone-500 text-xs">
        <a href="{{ path('legal_impressum') }}" class="hover:text-stone-300 mr-4">Impressum</a>
        <a href="{{ path('legal_datenschutz') }}" class="hover:text-stone-300 mr-4">Datenschutz</a>
        <span>{{ 'footer.no_data'|trans }}</span>
    </footer>

</body>
</html>
```

---

## HTMX patterns used in this project

### Pattern 1: Lazy-load partial on first interaction (transparency accordion)
```twig
<button
    hx-get="{{ path('talk_transparency', {country: country_code}) }}"
    hx-target="#transparency-content"
    hx-swap="innerHTML"
    hx-trigger="click once"   {# loads once, then Alpine handles toggle #}
    aria-expanded="false"
    aria-controls="transparency-content"
>
    {{ 'transparency.toggle'|trans }}
</button>
<div id="transparency-content" aria-live="polite"></div>
```

### Pattern 2: Input-triggered AI generation (reasons letter)
```twig
<input
    type="text"
    name="name"
    maxlength="50"
    hx-post="{{ path('api_reasons') }}"
    hx-trigger="keyup changed delay:800ms"
    hx-target="#letter-output"
    hx-swap="innerHTML"
    hx-indicator="#letter-loading"
>
<div id="letter-loading" class="htmx-indicator text-stone-500 text-sm mt-2">
    {{ 'plan.generating'|trans }}
</div>
<div id="letter-output" aria-live="polite" aria-label="{{ 'plan.letter_region_aria'|trans }}">
</div>
```

### Pattern 3: Country selector swap (resource directory)
```twig
<select
    hx-get="{{ path('resources_country', {countryCode: '__CODE__'}) }}"
    hx-target="#resource-list"
    hx-swap="innerHTML"
    hx-trigger="change"
    onchange="this.setAttribute('hx-get', '{{ path('resources') }}/' + this.value)"
>
    {% for country in all_countries %}
    <option value="{{ country.code }}" {{ country.code == initial_country ? 'selected' : '' }}>
        {{ country.flagEmoji }} {{ country.nameEn }}
    </option>
    {% endfor %}
</select>
<div id="resource-list" aria-live="polite">
    {# Initially populated server-side for the detected country #}
    {% include 'resources/_country.html.twig' %}
</div>
```

### Pattern 4: Form POST with HTMX swap (followup opt-in)
```twig
{# The outer div is the swap target — replaced on success #}
<div id="followup-form"
     hx-post="{{ path('followup_optin') }}"
     hx-target="#followup-form"
     hx-swap="outerHTML"
     hx-trigger="submit"
>
    <input type="email" name="email" required placeholder="{{ 'followup.email_placeholder'|trans }}">
    <input type="hidden" name="country" value="{{ country_code }}">
    <button type="submit">{{ 'followup.submit'|trans }}</button>
</div>
```

---

## Alpine.js safety plan (x-data pattern)

The safety plan builder uses Alpine.js for local state. All data stays in browser.
Never add hx-post or fetch() calls that send plan data to server.

```javascript
// assets/app.js — register the Alpine component
function safetyPlan() {
    return {
        warningSigns: [''],
        copingStrategies: [''],
        trustedContacts: [{ name: '', phone: '' }],
        reasons: [''],

        init() {
            this.loadFromStorage();
            // Auto-save on any change via $watch
            this.$watch('warningSigns', () => this.save());
            this.$watch('copingStrategies', () => this.save());
            this.$watch('trustedContacts', () => this.save());
            this.$watch('reasons', () => this.save());
        },

        save() {
            localStorage.setItem('safetyPlan_v1', JSON.stringify({
                warningSigns: this.warningSigns,
                copingStrategies: this.copingStrategies,
                trustedContacts: this.trustedContacts,
                reasons: this.reasons,
                updatedAt: new Date().toISOString(),
            }));
        },

        loadFromStorage() {
            const stored = localStorage.getItem('safetyPlan_v1');
            if (!stored) return;
            try {
                const data = JSON.parse(stored);
                this.warningSigns = data.warningSigns?.length ? data.warningSigns : [''];
                this.copingStrategies = data.copingStrategies?.length ? data.copingStrategies : [''];
                this.trustedContacts = data.trustedContacts?.length ? data.trustedContacts : [{ name: '', phone: '' }];
                this.reasons = data.reasons?.length ? data.reasons : [''];
            } catch (e) {
                // Corrupt storage — reset silently
                localStorage.removeItem('safetyPlan_v1');
            }
        },

        exportPdf() {
            // Send plan data to /plan/export — server renders PDF with dompdf, returns download
            // Server does NOT store this data
            const data = JSON.parse(localStorage.getItem('safetyPlan_v1') || '{}');
            fetch('{{ path("plan_export") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(data),
            })
            .then(r => r.blob())
            .then(blob => {
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'sicherheitsplan.pdf';
                a.click();
                URL.revokeObjectURL(url);
            });
        },

        // Array helpers
        addItem(arr, defaultVal) { this[arr].push(defaultVal); },
        removeItem(arr, index) { this[arr].splice(index, 1); if (!this[arr].length) this[arr].push(typeof defaultVal === 'object' ? { name: '', phone: '' } : ''); },
    }
}
```

---

## CSRF for HTMX

Add to `assets/app.js`:
```javascript
// Send Symfony CSRF token with every HTMX request
document.addEventListener('htmx:configRequest', (e) => {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    if (token) e.detail.headers['X-CSRF-Token'] = token;
});
```

Add to `base.html.twig` `<head>`:
```twig
<meta name="csrf-token" content="{{ csrf_token('htmx') }}">
```

Validate in controllers that mutate state:
```php
if (!$this->isCsrfTokenValid('htmx', $request->headers->get('X-CSRF-Token'))) {
    throw new AccessDeniedException('Invalid CSRF token');
}
```

---

## _letter.html.twig (HTMX partial)

```twig
{# Returned by AiController — renders inside #letter-output #}
<div class="mt-4 p-5 bg-stone-800 rounded-xl border border-stone-700 space-y-3">
    {% for paragraph in letter|split("\n\n") %}
        {% if paragraph|trim %}
        <p class="text-stone-300 leading-relaxed text-sm">{{ paragraph|trim }}</p>
        {% endif %}
    {% endfor %}

    {# Transition prompt into safety plan #}
    <div class="pt-3 border-t border-stone-700 mt-4">
        <p class="text-stone-500 text-xs">{{ 'plan.letter_transition'|trans }}</p>
    </div>
</div>
```

---

## resources/_country.html.twig (HTMX partial)

```twig
{# Grouped by resource type #}
{% set typeOrder = ['hotline_phone', 'hotline_text', 'hotline_chat', 'online_therapy', 'therapist_finder', 'peer_support', 'youth', 'lgbtq', 'self_help'] %}
{% set grouped = resources|reduce((carry, r) => carry|merge({(r.type): (carry[r.type] ?? [])|merge([r])}), {}) %}

{% for type in typeOrder %}
{% if grouped[type] is defined %}
<section class="mb-6">
    <h3 class="text-stone-500 text-xs uppercase tracking-widest mb-3">{{ ('resource.type.' ~ type)|trans }}</h3>
    {% for resource in grouped[type] %}
    <div class="bg-stone-900 border border-stone-800 rounded-xl p-4 mb-3 {{ resource.isPrimary ? 'border-blue-800' : '' }}">
        <div class="flex justify-between items-start mb-1">
            <h4 class="text-stone-200 font-medium text-sm">{{ resource.name }}</h4>
            <div class="flex gap-2 text-xs">
                {% if resource.isFree %}<span class="text-green-400">{{ 'resource.free'|trans }}</span>{% endif %}
                {% if resource.is24h %}<span class="text-blue-400">24/7</span>{% endif %}
                {% if resource.noPoliceByDefault %}<span class="text-yellow-400">{{ 'resource.no_police'|trans }}</span>{% endif %}
            </div>
        </div>
        {% if resource.description %}
        <p class="text-stone-500 text-xs mb-2">{{ resource.description }}</p>
        {% endif %}
        <div class="flex gap-3 mt-2">
            {% if resource.phone %}
            <a href="tel:{{ resource.phone|replace({' ': ''}) }}"
               class="text-blue-400 hover:text-blue-300 text-sm font-mono"
               aria-label="{{ 'resource.call_aria'|trans({'name': resource.name}) }}">
                📞 {{ resource.phone }}
            </a>
            {% endif %}
            {% if resource.url %}
            <a href="{{ resource.url }}" target="_blank" rel="noopener noreferrer"
               class="text-blue-400 hover:text-blue-300 text-sm"
               aria-label="{{ 'resource.website_aria'|trans({'name': resource.name}) }}">
                🌐 {{ 'resource.website'|trans }}
            </a>
            {% endif %}
        </div>
    </div>
    {% endfor %}
</section>
{% endif %}
{% endfor %}
```
