import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';
import intersect from '@alpinejs/intersect';
import { analytics, track } from './analytics';
import { submissionForm } from './submission-form';
import { validatedForm } from './validated-form';

document.documentElement.classList.remove('no-js');

const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

/* ------------------------------------------------------------------ */
/* Progressive reveal on scroll (§4.7)                                  */
/* ------------------------------------------------------------------ */
const revealObserver = 'IntersectionObserver' in window
    ? new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                revealObserver.unobserve(entry.target);
            }
        });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.12 })
    : null;

function observeReveals(root = document) {
    root.querySelectorAll('.reveal:not(.is-visible)').forEach((el) => {
        if (!revealObserver || prefersReducedMotion) {
            el.classList.add('is-visible');
        } else {
            revealObserver.observe(el);
        }
    });
}

/* ------------------------------------------------------------------ */
/* Alpine components                                                    */
/* ------------------------------------------------------------------ */

// Fixed header, lighter once the page scrolls (§3.2)
Alpine.data('siteHeader', () => ({
    scrolled: false,
    mobileOpen: false,
    megaOpen: null,
    init() {
        const update = () => { this.scrolled = window.scrollY > 24; };
        update();
        window.addEventListener('scroll', update, { passive: true });
        this.$watch('mobileOpen', (open) => {
            document.documentElement.classList.toggle('overflow-hidden', open);
        });
    },
    toggleMega(name) {
        this.megaOpen = this.megaOpen === name ? null : name;
    },
    closeAll() {
        this.megaOpen = null;
    },
}));

// Animated key figures, triggered when entering the screen (§4.7)
Alpine.data('counter', (target, decimals = 0) => ({
    display: '0',
    started: false,
    format(value) {
        return new Intl.NumberFormat(document.documentElement.lang, {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals,
        }).format(value);
    },
    init() {
        this.display = this.format(prefersReducedMotion ? target : 0);
    },
    start() {
        if (this.started || prefersReducedMotion) {
            this.display = this.format(target);
            return;
        }
        this.started = true;
        const duration = 1400;
        const t0 = performance.now();
        const step = (now) => {
            const p = Math.min(1, (now - t0) / duration);
            const eased = 1 - Math.pow(1 - p, 3);
            this.display = this.format(target * eased);
            if (p < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    },
}));

// Catalogue filters — reflected in the URL (§6.2)
Alpine.data('projectFilters', () => ({
    panelOpen: false,
    submit() {
        const form = this.$refs.form;
        const params = new URLSearchParams(new FormData(form));
        [...params.keys()].forEach((key) => { if (!params.get(key)) params.delete(key); });
        track('project_filter', Object.fromEntries(params));
        window.location.assign(`${form.action}${params.toString() ? `?${params}` : ''}`);
    },
}));

// Interactive Africa map on the home page (§5.1 section 7)
Alpine.data('africaMap', (countries) => ({
    active: null,
    countries,
    select(code) {
        this.active = this.countries[code] ? code : null;
    },
}));

// Cookie consent — analytics only after consent (§10.5)
Alpine.data('cookieConsent', () => ({
    visible: false,
    settings: false,
    analyticsChecked: false,
    init() {
        const consent = analytics.getConsent();
        this.visible = consent === null;
        this.analyticsChecked = consent === 'all';
        window.addEventListener('open-cookie-settings', () => {
            this.visible = true;
            this.settings = true;
        });
    },
    acceptAll() { this.save('all'); },
    rejectAll() { this.save('necessary'); },
    saveChoice() { this.save(this.analyticsChecked ? 'all' : 'necessary'); },
    save(value) {
        analytics.setConsent(value);
        this.visible = false;
        this.settings = false;
    },
}));

Alpine.data('submissionForm', submissionForm);
Alpine.data('validatedForm', validatedForm);

Alpine.plugin(collapse);
Alpine.plugin(focus);
Alpine.plugin(intersect);

window.Alpine = Alpine;
window.track = track;
Alpine.start();

observeReveals();
analytics.boot();

/* ------------------------------------------------------------------ */
/* Measured events (§10.5) — declarative: data-track="event_name"       */
/* ------------------------------------------------------------------ */
document.addEventListener('click', (event) => {
    const el = event.target.closest('[data-track]');
    if (!el) return;
    const params = {};
    Object.entries(el.dataset).forEach(([key, value]) => {
        if (key.startsWith('track') && key !== 'track') {
            params[key.replace(/^track/, '').replace(/^./, (c) => c.toLowerCase())] = value;
        }
    });
    track(el.dataset.track, params);
});

// Conversions are recorded on the confirmation page, i.e. only after a successful submission.
document.querySelectorAll('[data-track-view]').forEach((el) => {
    track(el.dataset.trackView, el.dataset.trackReference ? { reference: el.dataset.trackReference } : {});
});
