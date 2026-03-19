import './styles/app.compiled.css';
import htmx from 'htmx.org';

window.htmx = htmx;

// ── HTMX CSRF ─────────────────────────────────────────────────────────────────
document.addEventListener('htmx:configRequest', (e) => {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    if (token) e.detail.headers['X-CSRF-Token'] = token;
});

// ── Theme ──────────────────────────────────────────────────────────────────────
function detectTheme() {
    const h = new Date().getHours();
    const dark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    return (dark || h >= 20 || h < 7) ? 'warm-dark' : 'warm-light';
}

function applyTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    const btn = document.getElementById('theme-toggle');
    if (!btn) return;
    btn.setAttribute('aria-label', theme === 'warm-dark' ? 'Switch to light mode' : 'Switch to dark mode');
    btn.querySelector('[data-sun]').hidden = (theme !== 'warm-dark');
    btn.querySelector('[data-moon]').hidden = (theme === 'warm-dark');
}

document.addEventListener('DOMContentLoaded', () => {
    const theme = localStorage.getItem('theme') || detectTheme();
    applyTheme(theme);

    document.getElementById('theme-toggle')?.addEventListener('click', () => {
        const next = document.documentElement.getAttribute('data-theme') === 'warm-dark'
            ? 'warm-light' : 'warm-dark';
        localStorage.setItem('theme', next);
        applyTheme(next);
    });

    initTransparencyToggle();
    initSafetyPlan();
    initCountrySelect();
});

// ── Transparency FAQ toggle ────────────────────────────────────────────────────
function initTransparencyToggle() {
    const btn = document.getElementById('transparency-toggle');
    if (!btn) return;
    btn.addEventListener('click', () => {
        const open = btn.getAttribute('aria-expanded') === 'true';
        btn.setAttribute('aria-expanded', String(!open));
        btn.querySelector('[data-arrow-up]').hidden = open;
        btn.querySelector('[data-arrow-down]').hidden = !open;
    });
}

// ── Country select (resources page) ───────────────────────────────────────────
function initCountrySelect() {
    const sel = document.getElementById('country-select');
    if (!sel) return;
    sel.addEventListener('change', () => {
        const url = sel.dataset.baseUrl + '/' + sel.value;
        htmx.ajax('GET', url, { target: '#resource-list', swap: 'innerHTML' });
    });
}

// ── Safety plan ───────────────────────────────────────────────────────────────
function initSafetyPlan() {
    const root = document.getElementById('safety-plan');
    if (!root) return;

    let plan = loadPlan();
    renderPlan();

    function loadPlan() {
        try {
            const stored = localStorage.getItem('safetyPlan_v1');
            if (!stored) return defaultPlan();
            const d = JSON.parse(stored);
            return {
                warningSigns:     d.warningSigns?.length     ? d.warningSigns     : [''],
                copingStrategies: d.copingStrategies?.length ? d.copingStrategies : [''],
                trustedContacts:  d.trustedContacts?.length  ? d.trustedContacts  : [{ name: '', phone: '' }],
                reasons:          d.reasons?.length          ? d.reasons          : [''],
                updatedAt:        d.updatedAt || null,
            };
        } catch {
            localStorage.removeItem('safetyPlan_v1');
            return defaultPlan();
        }
    }

    function defaultPlan() {
        return { warningSigns: [''], copingStrategies: [''], trustedContacts: [{ name: '', phone: '' }], reasons: [''], updatedAt: null };
    }

    function savePlan() {
        plan.updatedAt = new Date().toISOString();
        localStorage.setItem('safetyPlan_v1', JSON.stringify(plan));
        const ind = document.getElementById('plan-saved');
        if (ind) ind.hidden = false;
    }

    function renderList(id, items, buildRow) {
        const el = document.getElementById(id);
        if (!el) return;
        el.innerHTML = '';
        items.forEach((item, i) => el.appendChild(buildRow(item, i)));
    }

    function textRow(value, placeholder, ariaLabel, onInput, onRemove) {
        const row = document.createElement('div');
        row.className = 'flex gap-2 mb-2';
        const input = document.createElement('input');
        input.type = 'text';
        input.className = 'input input-bordered input-sm flex-1';
        input.placeholder = placeholder;
        input.setAttribute('aria-label', ariaLabel);
        input.value = value;
        input.addEventListener('input', onInput);
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn btn-ghost btn-sm btn-square';
        btn.setAttribute('aria-label', 'Remove');
        btn.textContent = '✕';
        btn.addEventListener('click', onRemove);
        row.appendChild(input);
        row.appendChild(btn);
        return row;
    }

    function renderPlan() {
        const d = root.dataset;

        renderList('warning-signs-list', plan.warningSigns, (v, i) =>
            textRow(v, d.warningPlaceholder, `${d.warningLabel} ${i + 1}`,
                e => { plan.warningSigns[i] = e.target.value; savePlan(); },
                () => { plan.warningSigns.splice(i, 1); if (!plan.warningSigns.length) plan.warningSigns = ['']; savePlan(); renderPlan(); }
            )
        );

        renderList('coping-list', plan.copingStrategies, (v, i) =>
            textRow(v, d.copingPlaceholder, `${d.copingLabel} ${i + 1}`,
                e => { plan.copingStrategies[i] = e.target.value; savePlan(); },
                () => { plan.copingStrategies.splice(i, 1); if (!plan.copingStrategies.length) plan.copingStrategies = ['']; savePlan(); renderPlan(); }
            )
        );

        renderList('contacts-list', plan.trustedContacts, (contact, i) => {
            const row = document.createElement('div');
            row.className = 'flex gap-2 mb-2';
            for (const [field, placeholder, type, label] of [
                ['name',  d.contactName,  'text', `${d.contactName} ${i + 1}`],
                ['phone', d.contactPhone, 'tel',  `${d.contactPhone} ${i + 1}`],
            ]) {
                const inp = document.createElement('input');
                inp.type = type;
                inp.className = 'input input-bordered input-sm flex-1';
                inp.placeholder = placeholder;
                inp.setAttribute('aria-label', label);
                inp.value = contact[field];
                inp.addEventListener('input', e => { plan.trustedContacts[i][field] = e.target.value; savePlan(); });
                row.appendChild(inp);
            }
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-ghost btn-sm btn-square';
            btn.setAttribute('aria-label', 'Remove');
            btn.textContent = '✕';
            btn.addEventListener('click', () => { plan.trustedContacts.splice(i, 1); if (!plan.trustedContacts.length) plan.trustedContacts = [{ name: '', phone: '' }]; savePlan(); renderPlan(); });
            row.appendChild(btn);
            return row;
        });

        renderList('reasons-list', plan.reasons, (v, i) =>
            textRow(v, d.reasonPlaceholder, `${d.reasonLabel} ${i + 1}`,
                e => { plan.reasons[i] = e.target.value; savePlan(); },
                () => { plan.reasons.splice(i, 1); if (!plan.reasons.length) plan.reasons = ['']; savePlan(); renderPlan(); }
            )
        );

        const ind = document.getElementById('plan-saved');
        if (ind) ind.hidden = !plan.updatedAt;
    }

    root.querySelector('[data-add="warningSigns"]')?.addEventListener('click', () => { plan.warningSigns.push(''); savePlan(); renderPlan(); });
    root.querySelector('[data-add="copingStrategies"]')?.addEventListener('click', () => { plan.copingStrategies.push(''); savePlan(); renderPlan(); });
    root.querySelector('[data-add="trustedContacts"]')?.addEventListener('click', () => { plan.trustedContacts.push({ name: '', phone: '' }); savePlan(); renderPlan(); });
    root.querySelector('[data-add="reasons"]')?.addEventListener('click', () => { plan.reasons.push(''); savePlan(); renderPlan(); });

    root.querySelector('[data-action="exportPdf"]')?.addEventListener('click', () => {
        const url = root.dataset.exportUrl;
        fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify(plan),
        })
        .then(r => r.blob())
        .then(blob => {
            const a = document.createElement('a');
            a.href = URL.createObjectURL(blob);
            a.download = 'sicherheitsplan.pdf';
            a.click();
            URL.revokeObjectURL(a.href);
        })
        .catch(() => {});
    });
}
