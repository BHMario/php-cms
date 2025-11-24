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
    // Dark mode toggle
    const darkToggle = document.getElementById('dark-toggle');
    const body = document.body;
    const DARK_KEY = 'site-dark-mode';

    function applyDark(pref) {
        if (pref) body.classList.add('dark'); else body.classList.remove('dark');
    }

    // Init from localStorage
    try {
        const stored = localStorage.getItem(DARK_KEY);
        if (stored !== null) {
            applyDark(stored === '1');
        }
    } catch (e) {}

    if (darkToggle) {
        darkToggle.addEventListener('click', function () {
            const isDark = body.classList.toggle('dark');
            try { localStorage.setItem(DARK_KEY, isDark ? '1' : '0'); } catch (e) {}
        });
    }
})();
