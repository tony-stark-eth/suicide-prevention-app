import './styles/app.compiled.css';
import Alpine from 'alpinejs';
import htmx from 'htmx.org';

window.htmx = htmx;

// Theme manager — warm-light (day) / warm-dark (night), persisted in localStorage
window.themeManager = function () {
    return {
        theme: localStorage.getItem('theme') || detectTheme(),
        toggle() {
            this.theme = this.theme === 'warm-dark' ? 'warm-light' : 'warm-dark';
            localStorage.setItem('theme', this.theme);
        },
    };
};

window.detectTheme = function () {
    const hour = new Date().getHours();
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
    return (prefersDark || hour >= 20 || hour < 7) ? 'warm-dark' : 'warm-light';
};

// Send Symfony CSRF token with every HTMX request
document.addEventListener('htmx:configRequest', (e) => {
    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    if (token) e.detail.headers['X-CSRF-Token'] = token;
});

// Safety plan Alpine component — all data stays in browser
function safetyPlan() {
    return {
        warningSigns: [''],
        copingStrategies: [''],
        trustedContacts: [{ name: '', phone: '' }],
        reasons: [''],
        savedAt: null,

        init() {
            this.loadFromStorage();
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
            this.savedAt = new Date().toISOString();
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
                this.savedAt = data.updatedAt || null;
            } catch (e) {
                localStorage.removeItem('safetyPlan_v1');
            }
        },

        exportPdf() {
            const data = JSON.parse(localStorage.getItem('safetyPlan_v1') || '{}');
            const exportUrl = document.querySelector('[data-plan-export-url]')?.dataset.planExportUrl
                || '/plan/export';
            fetch(exportUrl, {
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
            })
            .catch(() => {});
        },

        addItem(arr, defaultVal) {
            this[arr].push(typeof defaultVal === 'object' ? { ...defaultVal } : defaultVal);
        },
        removeItem(arr, index) {
            this[arr].splice(index, 1);
            if (!this[arr].length) {
                this[arr].push(typeof this[arr][0] === 'object' ? { name: '', phone: '' } : '');
            }
        },
    };
}

window.safetyPlan = safetyPlan;

Alpine.start();
