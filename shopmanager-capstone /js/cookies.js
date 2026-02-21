/* =============================================================================
   js/cookies.js  –  Cookie consent manager & theme/language applier
   No external dependencies.  Runs on DOMContentLoaded.
   ============================================================================= */
(function () {
    'use strict';

    // ── Tiny cookie helpers ───────────────────────────────────────────────────
    function getCookie(name) {
        const match = document.cookie.match(new RegExp('(?:^|;\\s*)' + name + '=([^;]*)'));
        return match ? decodeURIComponent(match[1]) : null;
    }

    function setCookie(name, value, days) {
        const expires = new Date(Date.now() + days * 86400000).toUTCString();
        document.cookie = name + '=' + encodeURIComponent(value) +
            '; expires=' + expires +
            '; path=/' +
            '; SameSite=Strict' +
            (location.protocol === 'https:' ? '; Secure' : '');
    }

    function deleteCookie(name) {
        document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/; SameSite=Strict';
    }

    // ── Apply stored theme ────────────────────────────────────────────────────
    function applyTheme() {
        const theme = getCookie('theme');
        if (theme === 'dark') {
            document.body.classList.add('theme-dark');
        } else {
            document.body.classList.remove('theme-dark');
        }
    }

    // ── Init on DOM ready ─────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', function () {

        // Apply theme immediately (prevents flash)
        applyTheme();

        const banner   = document.getElementById('cookieBanner');
        const acceptBtn  = document.getElementById('acceptCookies');
        const declineBtn = document.getElementById('declineCookies');

        if (!banner) return;   // banner element not on this page

        // Show banner only if consent has NOT been given yet
        if (getCookie('consent') !== 'true') {
            banner.style.display = 'flex';
        }

        // ── Accept ──────────────────────────────────────────────────────────
        if (acceptBtn) {
            acceptBtn.addEventListener('click', function () {
                setCookie('consent', 'true', 365);

                // Persist current theme preference (default: light)
                const currentTheme = document.body.classList.contains('theme-dark') ? 'dark' : 'light';
                setCookie('theme', currentTheme, 365);

                // Persist language preference (default: en)
                setCookie('lang', 'en', 365);

                banner.style.display = 'none';
            });
        }

        // ── Decline ─────────────────────────────────────────────────────────
        if (declineBtn) {
            declineBtn.addEventListener('click', function () {
                // Remove any existing pref cookies
                deleteCookie('theme');
                deleteCookie('lang');
                deleteCookie('cart');
                // Still set consent = false so banner doesn't keep appearing
                setCookie('consent', 'false', 365);
                banner.style.display = 'none';
            });
        }
    });

    // ── Expose helpers globally so settings.php can call them ────────────────
    window.CookieManager = {
        get: getCookie,
        set: setCookie,
        delete: deleteCookie,
        applyTheme: applyTheme
    };

})();
