// Simple mobile menu toggle and small UI helpers
(function () {
    const toggle = document.querySelector('.nav-toggle');
    const nav = document.getElementById('nav-links');

    if (!toggle || !nav) return;

    toggle.addEventListener('click', function (e) {
        const open = nav.classList.toggle('open');
        toggle.classList.toggle('open', open);
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    // Close menu when clicking a link
    nav.addEventListener('click', function (e) {
        if (e.target.tagName.toLowerCase() === 'a') {
            nav.classList.remove('open');
            toggle.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
        }
    });

    // Close when clicking outside on small screens
    document.addEventListener('click', function (e) {
        if (!nav.classList.contains('open')) return;
        const isInside = nav.contains(e.target) || toggle.contains(e.target);
        if (!isInside) {
            nav.classList.remove('open');
            toggle.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
        }
    });
})();

// Dark mode toggle (supports multiple toggles on the page)
// This is OUTSIDE the above IIFE so it runs even if nav-toggle doesn't exist
(function () {
    const darkToggles = document.querySelectorAll('#dark-toggle');
    const body = document.body;
    const DARK_KEY = 'site-dark-mode';

    function applyDark(pref) {
        if (pref) body.classList.add('dark'); else body.classList.remove('dark');
        // update all toggles state/icon
        darkToggles.forEach(function (btn) {
            btn.setAttribute('aria-pressed', pref ? 'true' : 'false');
            btn.setAttribute('aria-label', pref ? 'Desactivar modo oscuro' : 'Activar modo oscuro');
            // swap icon (use simple emoji fallback)
            try {
                btn.textContent = pref ? '☀️' : '🌙';
            } catch (e) {}
        });
    }

    // Init from localStorage
    try {
        const stored = localStorage.getItem(DARK_KEY);
        if (stored !== null) {
            applyDark(stored === '1');
        } else {
            // set initial aria attributes for toggles
            darkToggles.forEach(function (btn) {
                btn.setAttribute('aria-pressed', body.classList.contains('dark') ? 'true' : 'false');
                btn.setAttribute('aria-label', body.classList.contains('dark') ? 'Desactivar modo oscuro' : 'Activar modo oscuro');
            });
        }
    } catch (e) {}

    if (darkToggles && darkToggles.length) {
        darkToggles.forEach(function (el) {
            el.addEventListener('click', function () {
                const isDark = body.classList.toggle('dark');
                try { localStorage.setItem(DARK_KEY, isDark ? '1' : '0'); } catch (e) {}
                applyDark(isDark);
            });
        });
    }
})();
