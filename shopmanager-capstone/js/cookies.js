/* =============================================================================
   js/cookies.js  –  Client-side Theme & Cookie Management
   
   This script handles the immediate application of themes and manages
   user consent for cookies.
   ============================================================================= */

(function () {
    'use strict';

    /**
     * Helper to retrieve a cookie value by name.
     * Used to check theme and consent status without refreshing the page.
     */
    function getCookie(name) {
        const match = document.cookie.match(new RegExp('(?:^|;\\s*)' + name + '=([^;]*)'));
        return match ? decodeURIComponent(match[1]) : null;
    }

    /**
     * Sets a browser cookie.
     * theme and lang cookies are set for 365 days.
     */
    function setCookie(name, value, days) {
        const expires = new Date(Date.now() + days * 86400000).toUTCString();
        document.cookie = name + '=' + encodeURIComponent(value) + 
            '; expires=' + expires + 
            '; path=/' + 
            '; SameSite=Strict';
    }

    /**
     * Applies the theme class to the body.
     * This runs immediately to ensure the UI matches the user's cookie.
     */
    function applyTheme() {
        const theme = getCookie('theme');
        if (theme === 'dark') {
            document.body.classList.add('theme-dark');
        } else if (theme === 'light') {
            document.body.classList.remove('theme-dark');
        }
    }

    // Initialize logic when the browser is ready
    document.addEventListener('DOMContentLoaded', function () {
        applyTheme();

        const banner   = document.getElementById('cookieBanner');
        const acceptBtn  = document.getElementById('acceptCookies');

        // Show the consent banner if the 'consent' cookie is missing
        if (banner && getCookie('consent') !== 'true') {
            banner.style.display = 'flex';
        }

        // Handle the 'Accept Cookies' button click
        if (acceptBtn) {
            acceptBtn.addEventListener('click', function () {
                setCookie('consent', 'true', 365);
                
                // Save the current theme preference into a cookie
                const currentTheme = document.body.classList.contains('theme-dark') ? 'dark' : 'light';
                setCookie('theme', currentTheme, 365);
                
                banner.style.display = 'none';
            });
        }
    });
})();