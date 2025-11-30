// Toggle menú móvil
(function () {
    const toggle = document.querySelector('.nav-toggle');
    const nav = document.getElementById('nav-links');

    if (!toggle || !nav) return;

    toggle.addEventListener('click', function (e) {
        const open = nav.classList.toggle('open');
        toggle.classList.toggle('open', open);
        toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    // Cerrar menú al hacer clic
    nav.addEventListener('click', function (e) {
        if (e.target.tagName.toLowerCase() === 'a') {
            nav.classList.remove('open');
            toggle.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
        }
    });

    // Cerrar al hacer clic fuera
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

// Alternar modo oscuro
(function () {
    const darkToggles = document.querySelectorAll('#dark-toggle');
    const body = document.body;
    const DARK_KEY = 'site-dark-mode';

    function applyDark(pref) {
        if (pref) body.classList.add('dark'); else body.classList.remove('dark');
        // Actualizar estado
        darkToggles.forEach(function (btn) {
            btn.setAttribute('aria-pressed', pref ? 'true' : 'false');
            btn.setAttribute('aria-label', pref ? 'Desactivar modo oscuro' : 'Activar modo oscuro');
            // Cambiar icono
            try {
                btn.textContent = pref ? '☀️' : '🌙';
            } catch (e) {}
        });
    }

    // Cargar desde localStorage
    try {
        const stored = localStorage.getItem(DARK_KEY);
        if (stored !== null) {
            applyDark(stored === '1');
        } else {
            // Iniciar atributos aria
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

// Lightbox imagen
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

// Modal logout
(function () {
    const logoutBtn = document.getElementById('logout-btn');
    const logoutCancel = document.getElementById('logout-cancel');
    const logoutModal = document.getElementById('logout-modal');
    const logoutLink = logoutModal ? logoutModal.querySelector('a[href="/logout"]') : null;

    if (!logoutBtn || !logoutModal) return;

    // Mostrar modal
    logoutBtn.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();
        logoutModal.classList.add('active');
    });

    // Cancelar
    if (logoutCancel) {
        logoutCancel.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            logoutModal.classList.remove('active');
        });
    }

    // Confirmar logout
    if (logoutLink) {
        logoutLink.addEventListener('click', function (e) {
            logoutModal.classList.remove('active');
        });
    }

    // Cerrar al hacer clic fuera
    logoutModal.addEventListener('click', function (e) {
        if (e.target === logoutModal) {
            e.stopPropagation();
            logoutModal.classList.remove('active');
        }
    });

    // No cerrar al hacer clic dentro
    const modalContent = logoutModal.querySelector('.modal-content');
    if (modalContent) {
        modalContent.addEventListener('click', function (e) {
            e.stopPropagation();
        });
    }
})();

// Modal cambio contraseña
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
            // Limpiar formulario
            if (passwordForm) {
                passwordForm.reset();
                document.getElementById('current-pwd-error').style.display = 'none';
                document.getElementById('new-pwd-error').style.display = 'none';
                document.getElementById('confirm-pwd-error').style.display = 'none';
                document.getElementById('password-success').style.display = 'none';
            }
        });
    }

    // Cerrar al hacer clic fuera (backdrop)
    passwordModal.addEventListener('click', function (e) {
        if (e.target === passwordModal) {
            passwordModal.style.display = 'none';
            // Limpiar formulario
            if (passwordForm) {
                passwordForm.reset();
                document.getElementById('current-pwd-error').style.display = 'none';
                document.getElementById('new-pwd-error').style.display = 'none';
                document.getElementById('confirm-pwd-error').style.display = 'none';
                document.getElementById('password-success').style.display = 'none';
            }
        }
    });

    // Enviar formulario
    if (passwordForm) {
        passwordForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const currentPassword = document.getElementById('current_password').value;
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            // Limpiar errores
            document.getElementById('current-pwd-error').style.display = 'none';
            document.getElementById('new-pwd-error').style.display = 'none';
            document.getElementById('confirm-pwd-error').style.display = 'none';
            document.getElementById('password-success').style.display = 'none';

            // Enviar datos
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
                    // Ok
                    const successEl = document.getElementById('password-success');
                    successEl.textContent = data.success || 'Contraseña actualizada correctamente';
                    successEl.style.display = 'block';
                    passwordForm.reset();

                    // Cerrar en 2s
                    setTimeout(() => {
                        passwordModal.style.display = 'none';
                        successEl.style.display = 'none';
                    }, 2000);
                })
                .catch(error => {
                    // Error
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
