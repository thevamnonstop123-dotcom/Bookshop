/**
 * Bookshop Auth — All Auth Interactions
 */
(function () {
    'use strict';

    // ========== MODAL OPEN/CLOSE ==========
    window.openLoginModal = function () {
        const modal = document.getElementById('loginModal');
        if (modal) {
            modal.style.display = 'flex';
            modal.classList.add('active');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';

            // Reset to login form view and clear all forms
            window.switchToLogin();
            resetAllForms();
            clearAllErrors();

            // Check for error in URL
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('error')) {
                const errorEl = document.getElementById('loginError');
                if (errorEl) {
                    errorEl.style.display = 'block';
                    errorEl.innerHTML = '<i class="fas fa-circle-exclamation"></i> Invalid email or password. Please try again.';
                }
                const newUrl = window.location.pathname;
                window.history.replaceState({}, '', newUrl);
            }
        } else {
            console.error("Modal element #loginModal not found in the DOM.");
        }
    };

    window.closeLoginModal = function () {
        const modal = document.getElementById('loginModal');
        if (modal) {
            modal.classList.remove('active');
            modal.setAttribute('aria-hidden', 'true');
            modal.style.display = 'none';
        }
        document.body.style.overflow = '';

        // Reset all forms when modal closes
        resetAllForms();
        clearAllErrors();
    };

    // ========== FORM RESET HELPERS ==========
    function resetAllForms() {
        const loginForm = document.getElementById('modalLoginForm');
        const registerForm = document.getElementById('modalRegisterForm');

        if (loginForm) loginForm.reset();
        if (registerForm) registerForm.reset();

        // Reset password toggle icons back to eye
        const modalToggleIcon = document.getElementById('modalToggleIcon');
        const modalRegToggleIcon = document.getElementById('modalRegToggleIcon');
        const loginPassField = document.querySelector('#modalLoginForm input[name="password"]');
        const regPassField = document.querySelector('#modalRegisterForm input[name="password"]');

        if (modalToggleIcon) {
            modalToggleIcon.classList.remove('fa-eye-slash');
            modalToggleIcon.classList.add('fa-eye');
        }
        if (modalRegToggleIcon) {
            modalRegToggleIcon.classList.remove('fa-eye-slash');
            modalRegToggleIcon.classList.add('fa-eye');
        }
        if (loginPassField) loginPassField.type = 'password';
        if (regPassField) regPassField.type = 'password';

        // Reset password strength meter
        const container = document.getElementById('passwordStrength');
        if (container) container.innerHTML = '';
    }

    function clearAllErrors() {
        const errorEls = ['loginError', 'registerError', 'loginSuccess'];
        errorEls.forEach(function (id) {
            const el = document.getElementById(id);
            if (el) {
                el.style.display = 'none';
                el.innerHTML = '';
            }
        });
    }

    // ========== FORM SWITCHING ==========
    window.switchToRegister = function (e) {
        if (e) e.preventDefault();
        const loginForm = document.getElementById('loginFormContent');
        const registerForm = document.getElementById('registerFormContent');
        const loginFormEl = document.getElementById('modalLoginForm');
        const registerFormEl = document.getElementById('modalRegisterForm');

        if (loginForm) loginForm.style.display = 'none';
        if (registerForm) registerForm.style.display = 'block';

        // Reset login form when switching away
        if (loginFormEl) loginFormEl.reset();
        clearAllErrors();

        // Reset password toggle
        const icon = document.getElementById('modalToggleIcon');
        const passField = document.querySelector('#modalLoginForm input[name="password"]');
        if (icon) {
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
        if (passField) passField.type = 'password';
    };

    window.switchToLogin = function (e) {
        if (e) e.preventDefault();
        const loginForm = document.getElementById('loginFormContent');
        const registerForm = document.getElementById('registerFormContent');
        const registerFormEl = document.getElementById('modalRegisterForm');

        if (registerForm) registerForm.style.display = 'none';
        if (loginForm) loginForm.style.display = 'block';

        // Reset register form when switching away
        if (registerFormEl) registerFormEl.reset();
        clearAllErrors();

        // Reset password toggle
        const icon = document.getElementById('modalRegToggleIcon');
        const passField = document.querySelector('#modalRegisterForm input[name="password"]');
        if (icon) {
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
        if (passField) passField.type = 'password';

        // Reset password strength meter
        const container = document.getElementById('passwordStrength');
        if (container) container.innerHTML = '';
    };

    // Close on overlay click
    document.addEventListener('click', function (e) {
        const modal = document.getElementById('loginModal');
        if (modal && e.target === modal) window.closeLoginModal();
    });

    // Close on Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') window.closeLoginModal();
    });

    // ========== MODAL PASSWORD TOGGLES ==========
    window.toggleModalPass = function () {
        const field = document.querySelector('#modalLoginForm input[name="password"]');
        const icon = document.getElementById('modalToggleIcon');
        if (!field || !icon) return;

        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    };

    window.toggleModalRegPass = function () {
        const field = document.querySelector('#modalRegisterForm input[name="password"]');
        const icon = document.getElementById('modalRegToggleIcon');
        if (!field || !icon) return;

        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    };

    // ========== PASSWORD STRENGTH METER ==========
    function updatePasswordStrength() {
        const password = document.getElementById('password');
        if (!password) return;

        const value = password.value;
        const hasMinLength = value.length >= 8;
        const hasLetter = /[a-zA-Z]/.test(value);
        const hasNumber = /[0-9]/.test(value);

        let strengthText = '';
        let strengthColor = '';
        let width = '0%';

        if (value.length === 0) {
            const container = document.getElementById('passwordStrength');
            if (container) container.innerHTML = '';
            return;
        }

        if (!hasMinLength) {
            strengthText = '✗ Weak (need 8+ characters)';
            strengthColor = '#EF4444';
            width = '33%';
        } else if (hasMinLength && (!hasLetter || !hasNumber)) {
            strengthText = '⚠ Medium (add letters & numbers)';
            strengthColor = '#F59E0B';
            width = '66%';
        } else {
            strengthText = '✓ Strong password!';
            strengthColor = '#10B981';
            width = '100%';
        }

        const container = document.getElementById('passwordStrength');
        if (container) {
            container.innerHTML = `
                <div class="strength-bar">
                    <div class="strength-fill" style="width: ${width}; background: ${strengthColor};"></div>
                </div>
                <span class="strength-text" style="color: ${strengthColor};">${strengthText}</span>
            `;
        }
    }

    // Initialize strength meter
    document.addEventListener('DOMContentLoaded', function () {
        const passwordField = document.getElementById('password');
        if (passwordField) {
            passwordField.addEventListener('input', updatePasswordStrength);
        }
    });

    // ========== AUTO-OPEN MODAL ON PAGE LOAD ==========
    document.addEventListener('DOMContentLoaded', function () {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('login') || urlParams.has('login=open') || urlParams.has('login-open')) {
            setTimeout(function () {
                openLoginModal();
            }, 100);
        }
    });

    // ========== LOGIN FORM HANDLER ==========
    window.handleLogin = async function (e) {
        e.preventDefault();
        const form = e.target;
        const btn = form.querySelector('button[type="submit"]');
        const errorEl = document.getElementById('loginError');
        const successEl = document.getElementById('loginSuccess');

        if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing in...'; }
        if (errorEl) errorEl.style.display = 'none';
        if (successEl) successEl.style.display = 'none';

        try {
            const resp = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    email: form.email.value,
                    password: form.password.value,
                    remember: form.remember ? form.remember.checked : false
                })
            });

            if (resp.redirected) {
                window.location.href = resp.url;
            } else if (resp.ok) {
                // Reset form before reload
                form.reset();
                window.location.reload();
            } else {
                const data = await resp.json();
                if (errorEl) {
                    errorEl.style.display = 'block';
                    errorEl.innerHTML = '<i class="fas fa-circle-exclamation"></i> ' + (data.message || 'Invalid credentials. Please try again.');
                }
            }
        } catch (err) {
            if (errorEl) {
                errorEl.style.display = 'block';
                errorEl.innerHTML = '<i class="fas fa-circle-exclamation"></i> Something went wrong. Please try again.';
            }
        }

        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-arrow-right-to-bracket"></i> Sign In'; }
    };

    // ========== REGISTER FORM HANDLER ==========
    window.handleRegister = async function (e) {
        e.preventDefault();
        const form = e.target;
        const btn = form.querySelector('button[type="submit"]');
        const errorEl = document.getElementById('registerError');

        if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating...'; }
        if (errorEl) errorEl.style.display = 'none';

        try {
            const resp = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    name: form.name.value,
                    email: form.email.value,
                    phone: form.phone.value,
                    gender: form.gender.value,
                    dob: form.dob.value,
                    password: form.password.value,
                    password_confirmation: form.password_confirmation.value
                })
            });

            if (resp.redirected) {
                window.location.href = resp.url;
            } else if (resp.ok) {
                // Reset form before reload
                form.reset();
                // Clear password strength meter
                const container = document.getElementById('passwordStrength');
                if (container) container.innerHTML = '';
                window.location.reload();
            } else {
                const data = await resp.json();
                if (errorEl) {
                    errorEl.style.display = 'block';
                    errorEl.innerHTML = '<i class="fas fa-circle-exclamation"></i> ' + (data.message || Object.values(data.errors || {}).flat().join('<br>'));
                }
            }
        } catch (err) {
            if (errorEl) {
                errorEl.style.display = 'block';
                errorEl.innerHTML = '<i class="fas fa-circle-exclamation"></i> Something went wrong. Please try again.';
            }
        }

        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-user-plus"></i> Create Account'; }
    };
})();