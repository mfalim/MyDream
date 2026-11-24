import http from 'k6/http';
import { check, group, sleep } from 'k6';
import { Trend } from 'k6/metrics';

/**
 * MyDream Wedding Organizer - Full k6 Load & Stress Test
 *
 * Cakupan lengkap:
 *   1. Web Publik           : /, /store, /paket, /vendor, /inspirasi, /event, /blog, /tentang-kami, /login, /user/explore-vendor
 *   2. Web Client           : /user/dashboard, /user/konfirmasi-vendor, /user/rundown-timeline, /user/ulasan,
 *                             /user/riwayat-pembelian, /user/tagihan, /user/inspirasi, /user/cart
 *   3. Web Member           : /member/dashboard
 *   4. Web Vendor           : /vendor/dashboard, /vendor/kas, /vendor/kalender, /vendor/tracking, /vendor/profil
 *   5. Web Admin            : /admin/dashboard, /admin/events, /admin/calendar, /admin/tracking, /admin/organizer-event,
 *                             /admin/payments, /admin/payroll, /admin/members, /admin/member-categories, /admin/vendors,
 *                             /admin/packages, /admin/categories, /admin/articles, /admin/public-events, /admin/inspirations
 *   6. Mobile API Publik    : /api/home, /api/about, /api/catalog, /api/catalog/categories, /api/catalog/packages,
 *                             /api/store, /api/content/articles, /api/content/events, /api/content/inspirations
 *   7. Mobile API Client    : /api/client/dashboard, /api/client/profile, /api/client/bookings, /api/client/events,
 *                             /api/client/reviews, /api/cart, /api/me
 *   8. Mobile API Member    : /api/member/dashboard, /api/me
 *   (Mobile API tidak menguji Admin & Vendor sesuai arsitektur Mobile_MyDream)
 *
 * Cara pakai:
 *   # Smoke test cepat (5 VU, ~35 detik):
 *   k6 run -e BASE_URL=https://domain-vps.com -e PROFILE=smoke tests/load/k6-load-test.js
 *
 *   # Load test penuh semua role (Web + Mobile API):
 *   k6 run -e BASE_URL=https://domain-vps.com \
 *     -e ADMIN_LOGIN=admin@email.com -e ADMIN_PASSWORD=pass \
 *     -e CLIENT_LOGIN=client@email.com -e CLIENT_PASSWORD=pass \
 *     -e MEMBER_LOGIN=member@email.com -e MEMBER_PASSWORD=pass \
 *     -e VENDOR_LOGIN=vendor@email.com -e VENDOR_PASSWORD=pass \
 *     tests/load/k6-load-test.js
 *
 *   # Stress test (30 -> 80 -> 150 VU):
 *   k6 run -e BASE_URL=https://domain-vps.com -e PROFILE=stress tests/load/k6-load-test.js
 */

const BASE_URL = (__ENV.BASE_URL || 'https://127.0.0.1:8000').replace(/\/+$/, '');
const PROFILE = (__ENV.PROFILE || 'load').toLowerCase();

const PROFILES = {
    smoke: [
        { duration: '10s', target: 5 },
        { duration: '20s', target: 5 },
        { duration: '5s', target: 0 },
    ],
    load: [
        { duration: '30s', target: 20 },
        { duration: '1m', target: 20 },
        { duration: '1m', target: 50 },
        { duration: '30s', target: 0 },
    ],
    stress: [
        { duration: '30s', target: 30 },
        { duration: '1m', target: 80 },
        { duration: '1m', target: 150 },
        { duration: '30s', target: 0 },
    ],
};

const webPublicDuration = new Trend('mydream_web_public_duration', true);
const webClientDuration = new Trend('mydream_web_client_duration', true);
const webMemberDuration = new Trend('mydream_web_member_duration', true);
const webVendorDuration = new Trend('mydream_web_vendor_duration', true);
const webAdminDuration = new Trend('mydream_web_admin_duration', true);
const apiPublicDuration = new Trend('mydream_api_public_duration', true);
const apiClientDuration = new Trend('mydream_api_client_duration', true);
const apiMemberDuration = new Trend('mydream_api_member_duration', true);

export const options = {
    stages: PROFILES[PROFILE] || PROFILES.load,
    thresholds: {
        http_req_failed: ['rate<0.01'],
        mydream_web_public_duration: ['p(95)<1500'],
        mydream_api_public_duration: ['p(95)<800'],
    },
};

function loginApi(login, password) {
    if (!login || !password) return null;

    const res = http.post(
        `${BASE_URL}/api/auth/login`,
        JSON.stringify({ login, password }),
        { headers: { 'Content-Type': 'application/json', Accept: 'application/json' } }
    );

    if (res.status !== 200) {
        console.warn(`Login API gagal untuk ${login}: HTTP ${res.status}`);
        return null;
    }

    try {
        const body = res.json();
        return body.token || body.access_token || body.data?.token || null;
    } catch (_) {
        return null;
    }
}

function loginWebCookieHeader(login, password) {
    if (!login || !password) return null;

    const jar = new http.CookieJar();
    const loginPage = http.get(`${BASE_URL}/login`, { jar });
    const match = loginPage.body && loginPage.body.match(/name="_token"\s+value="([^"]+)"/);
    if (!match) {
        console.warn(`CSRF token tidak ditemukan di /login untuk ${login}`);
        return null;
    }

    const res = http.post(
        `${BASE_URL}/login`,
        { _token: match[1], login, password },
        { jar, redirects: 0 }
    );

    if (res.status !== 302) {
        console.warn(`Login Web gagal untuk ${login}: HTTP ${res.status}`);
        return null;
    }

    const cookies = jar.cookiesForURL(BASE_URL);
    const pairs = [];
    for (const [name, values] of Object.entries(cookies)) {
        if (values && values.length > 0) {
            pairs.push(`${name}=${values[0]}`);
        }
    }

    return pairs.length > 0 ? pairs.join('; ') : null;
}

export function setup() {
    return {
        // Mobile API tokens (Client & Member saja — tanpa Admin & Vendor)
        clientApiToken: __ENV.CLIENT_TOKEN || loginApi(__ENV.CLIENT_LOGIN, __ENV.CLIENT_PASSWORD),
        memberApiToken: __ENV.MEMBER_TOKEN || loginApi(__ENV.MEMBER_LOGIN, __ENV.MEMBER_PASSWORD),

        // Web session cookies (Client, Member, Vendor, Admin)
        clientWebCookie: loginWebCookieHeader(__ENV.CLIENT_LOGIN, __ENV.CLIENT_PASSWORD),
        memberWebCookie: loginWebCookieHeader(__ENV.MEMBER_LOGIN, __ENV.MEMBER_PASSWORD),
        vendorWebCookie: loginWebCookieHeader(__ENV.VENDOR_LOGIN, __ENV.VENDOR_PASSWORD),
        adminWebCookie: loginWebCookieHeader(__ENV.ADMIN_LOGIN, __ENV.ADMIN_PASSWORD),
    };
}

function hitWebGroup(groupName, paths, cookieHeader, metric) {
    group(groupName, () => {
        const headers = cookieHeader ? { Cookie: cookieHeader } : {};
        for (const path of paths) {
            const res = http.get(`${BASE_URL}${path}`, {
                headers,
                tags: { group: groupName, endpoint: path },
            });
            metric.add(res.timings.duration);
            check(res, {
                [`${groupName} ${path} status 200`]: (r) => r.status === 200,
            });
        }
    });
}

function hitApiGroup(groupName, paths, token, metric) {
    group(groupName, () => {
        const headers = { Accept: 'application/json' };
        if (token) headers.Authorization = `Bearer ${token}`;

        for (const path of paths) {
            const res = http.get(`${BASE_URL}${path}`, {
                headers,
                tags: { group: groupName, endpoint: path },
            });
            metric.add(res.timings.duration);
            check(res, {
                [`${groupName} ${path} status 200`]: (r) => r.status === 200,
            });
        }
    });
}

export default function (data) {
    // 1. Web Publik
    hitWebGroup(
        'Web Publik',
        ['/', '/store', '/paket', '/vendor', '/inspirasi', '/event', '/blog', '/tentang-kami', '/login', '/user/explore-vendor'],
        null,
        webPublicDuration
    );

    sleep(0.4);

    // 2. Web Client Dashboard
    if (data.clientWebCookie) {
        hitWebGroup(
            'Web Client',
            [
                '/user/dashboard',
                '/user/konfirmasi-vendor',
                '/user/rundown-timeline',
                '/user/ulasan',
                '/user/riwayat-pembelian',
                '/user/tagihan',
                '/user/inspirasi',
                '/user/cart',
            ],
            data.clientWebCookie,
            webClientDuration
        );
    }

    // 3. Web Member Dashboard
    if (data.memberWebCookie) {
        hitWebGroup('Web Member', ['/member/dashboard'], data.memberWebCookie, webMemberDuration);
    }

    // 4. Web Vendor Dashboard
    if (data.vendorWebCookie) {
        hitWebGroup(
            'Web Vendor',
            ['/vendor/dashboard', '/vendor/kas', '/vendor/kalender', '/vendor/tracking', '/vendor/profil'],
            data.vendorWebCookie,
            webVendorDuration
        );
    }

    // 5. Web Admin Dashboard
    if (data.adminWebCookie) {
        hitWebGroup(
            'Web Admin',
            [
                '/admin/dashboard',
                '/admin/events',
                '/admin/calendar',
                '/admin/tracking',
                '/admin/organizer-event',
                '/admin/payments',
                '/admin/payroll',
                '/admin/members',
                '/admin/member-categories',
                '/admin/vendors',
                '/admin/packages',
                '/admin/categories',
                '/admin/articles',
                '/admin/public-events',
                '/admin/inspirations',
            ],
            data.adminWebCookie,
            webAdminDuration
        );
    }

    sleep(0.4);

    // 6. Mobile API Publik (Tanpa Admin & Vendor API)
    hitApiGroup(
        'Mobile API Publik',
        [
            '/api/home',
            '/api/about',
            '/api/catalog',
            '/api/catalog/categories',
            '/api/catalog/packages',
            '/api/store',
            '/api/content/articles',
            '/api/content/events',
            '/api/content/inspirations',
        ],
        null,
        apiPublicDuration
    );

    // 7. Mobile API Client
    if (data.clientApiToken) {
        hitApiGroup(
            'Mobile API Client',
            [
                '/api/client/dashboard',
                '/api/client/profile',
                '/api/client/bookings',
                '/api/client/events',
                '/api/client/reviews',
                '/api/cart',
                '/api/me',
            ],
            data.clientApiToken,
            apiClientDuration
        );
    }

    // 8. Mobile API Member
    if (data.memberApiToken) {
        hitApiGroup(
            'Mobile API Member',
            ['/api/member/dashboard', '/api/me'],
            data.memberApiToken,
            apiMemberDuration
        );
    }

    sleep(1);
}
