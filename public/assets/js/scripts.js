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

// Lightbox para imagen de post
(function () {
    const postImg = document.querySelector('.post-image-full');
    const lightbox = document.getElementById('image-lightbox');
    const lightboxImg = document.getElementById('lightbox-image');
    const lightboxClose = document.getElementById('lightbox-close');

    if (postImg && lightbox && lightboxImg && lightboxClose) {
        postImg.style.cursor = 'pointer';
        postImg.addEventListener('click', function () {
            lightboxImg.src = postImg.src;
            lightbox.classList.add('active');
            lightbox.style.display = 'flex';
        });
        lightboxClose.addEventListener('click', function () {
            lightbox.classList.remove('active');
            lightbox.style.display = 'none';
        });
        lightbox.addEventListener('click', function (e) {
            if (e.target === lightbox) {
                lightbox.classList.remove('active');
                lightbox.style.display = 'none';
            }
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && lightbox.classList.contains('active')) {
                lightbox.classList.remove('active');
                lightbox.style.display = 'none';
            }
        });
    }
})();

// Logout modal confirmation
(function () {
    const logoutBtn = document.getElementById('logout-btn');
    const logoutCancel = document.getElementById('logout-cancel');
    const logoutModal = document.getElementById('logout-modal');

    if (!logoutBtn || !logoutModal) return;

    logoutBtn.addEventListener('click', function (e) {
        e.preventDefault();
        logoutModal.style.display = 'flex';
    });

    if (logoutCancel) {
        logoutCancel.addEventListener('click', function (e) {
            e.preventDefault();
            logoutModal.style.display = 'none';
        });
    }

    // Close modal when clicking outside (on the backdrop)
    logoutModal.addEventListener('click', function (e) {
        if (e.target === logoutModal) {
            logoutModal.style.display = 'none';
        }
    });
})();

// Password change modal (only on profile page)
(function () {
    const changePasswordBtn = document.getElementById('change-password-btn');
    const passwordCancel = document.getElementById('password-cancel');
    const passwordModal = document.getElementById('password-modal');
    const passwordForm = document.getElementById('password-form');

    if (!changePasswordBtn || !passwordModal) return;

    changePasswordBtn.addEventListener('click', function (e) {
        e.preventDefault();
        passwordModal.style.display = 'flex';
    });

    if (passwordCancel) {
        passwordCancel.addEventListener('click', function (e) {
            e.preventDefault();
            passwordModal.style.display = 'none';
            // Limpiar el formulario
            if (passwordForm) {
                passwordForm.reset();
                document.getElementById('current-pwd-error').style.display = 'none';
                document.getElementById('new-pwd-error').style.display = 'none';
                document.getElementById('confirm-pwd-error').style.display = 'none';
                document.getElementById('password-success').style.display = 'none';
            }
        });
    }

    // Close modal when clicking outside (on the backdrop)
    passwordModal.addEventListener('click', function (e) {
        if (e.target === passwordModal) {
            passwordModal.style.display = 'none';
            // Limpiar el formulario
            if (passwordForm) {
                passwordForm.reset();
                document.getElementById('current-pwd-error').style.display = 'none';
                document.getElementById('new-pwd-error').style.display = 'none';
                document.getElementById('confirm-pwd-error').style.display = 'none';
                document.getElementById('password-success').style.display = 'none';
            }
        }
    });

    // Manejar envío del formulario
    if (passwordForm) {
        passwordForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const currentPassword = document.getElementById('current_password').value;
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            // Limpiar errores previos
            document.getElementById('current-pwd-error').style.display = 'none';
            document.getElementById('new-pwd-error').style.display = 'none';
            document.getElementById('confirm-pwd-error').style.display = 'none';
            document.getElementById('password-success').style.display = 'none';

            // Enviar datos al servidor
            const formData = new FormData(passwordForm);

            fetch('/change-password', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    const data = response.json();
                    if (!response.ok) {
                        return data.then(error => Promise.reject(error));
                    }
                    return data;
                })
                .then(data => {
                    // Éxito
                    const successEl = document.getElementById('password-success');
                    successEl.textContent = data.success || 'Contraseña actualizada correctamente';
                    successEl.style.display = 'block';
                    passwordForm.reset();

                    // Cerrar modal después de 2 segundos
                    setTimeout(() => {
                        passwordModal.style.display = 'none';
                        successEl.style.display = 'none';
                    }, 2000);
                })
                .catch(error => {
                    // Mostrar error específico
                    const errorMsg = error.error || 'Error desconocido';

                    if (errorMsg.includes('contraseña actual')) {
                        document.getElementById('current-pwd-error').textContent = errorMsg;
                        document.getElementById('current-pwd-error').style.display = 'block';
                    } else if (errorMsg.includes('coinciden')) {
                        document.getElementById('confirm-pwd-error').textContent = errorMsg;
                        document.getElementById('confirm-pwd-error').style.display = 'block';
                    } else if (errorMsg.includes('6 caracteres')) {
                        document.getElementById('new-pwd-error').textContent = errorMsg;
                        document.getElementById('new-pwd-error').style.display = 'block';
                    } else {
                        document.getElementById('current-pwd-error').textContent = errorMsg;
                        document.getElementById('current-pwd-error').style.display = 'block';
                    }
                });
        });
    }
})();
