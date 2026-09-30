/*
 * Audience measurement (§10.5): GA4 loaded only after consent and never
 * blocking rendering — mainland China visitors may be partially measured.
 */
const COOKIE = 'cookie_consent';
const queue = [];
let gtagReady = false;

function readCookie(name) {
    return document.cookie.split('; ').find((row) => row.startsWith(`${name}=`))?.split('=')[1] ?? null;
}

function measurementId() {
    return document.querySelector('meta[name="ga4-id"]')?.content || null;
}

function loadGa4() {
    const id = measurementId();
    if (!id || gtagReady) return;

    window.dataLayer = window.dataLayer || [];
    window.gtag = function gtag() { window.dataLayer.push(arguments); };
    window.gtag('js', new Date());
    window.gtag('config', id, { anonymize_ip: true });

    const script = document.createElement('script');
    script.async = true;
    script.src = `https://www.googletagmanager.com/gtag/js?id=${encodeURIComponent(id)}`;
    document.head.appendChild(script);

    gtagReady = true;
    queue.splice(0).forEach(([name, params]) => window.gtag('event', name, params));
}

export const analytics = {
    getConsent() {
        return readCookie(COOKIE);
    },
    setConsent(value) {
        const secure = location.protocol === 'https:' ? '; Secure' : '';
        document.cookie = `${COOKIE}=${value}; Max-Age=${60 * 60 * 24 * 180}; Path=/; SameSite=Lax${secure}`;
        if (value === 'all') loadGa4();
    },
    boot() {
        if (this.getConsent() === 'all') {
            // Deferred to idle time: never competes with the page's own resources.
            (window.requestIdleCallback || ((cb) => setTimeout(cb, 1500)))(loadGa4);
        }
    },
};

export function track(name, params = {}) {
    if (!name) return;
    if (gtagReady && window.gtag) {
        window.gtag('event', name, params);
    } else if (analytics.getConsent() === 'all') {
        queue.push([name, params]);
    }
}
