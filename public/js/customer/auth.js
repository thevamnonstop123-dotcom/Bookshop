/**
 * Bookshop Auth — All Auth Interactions
 * Modal, password toggles, strength meter, social login
 */
(function () {
    'use strict';

    // ========== MODAL OPEN/CLOSE ==========
    window.openLoginModal = function () {
        const modal = document.getElementById('loginModal');
        if (modal) {
            // Show overlay immediately and mark active
            modal.style.display = 'flex';
            modal.classList.add('active');
            modal.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden'; // Prevent background scrolling
        } else {
            console.error("Modal element #loginModal not found in the DOM.");
        }
    };

    window.closeLoginModal = function () {
        const modal = document.getElementById('loginModal');
        if (modal) { 
            modal.classList.remove('active'); 
            modal.setAttribute('aria-hidden', 'true'); 
            // Hide overlay with inline style to ensure it doesn't take up layout space
            modal.style.display = 'none';
        }
        document.body.style.overflow = ''; // Restore background scrolling
    };

    window.switchToRegister = function (e) {
        if (e) e.preventDefault();
        const loginForm = document.getElementById('loginFormContent');
        const registerForm = document.getElementById('registerFormContent');
        if (loginForm) loginForm.style.display = 'none';
        if (registerForm) registerForm.style.display = 'block';
    };

    window.switchToLogin = function (e) {
        if (e) e.preventDefault();
        const loginForm = document.getElementById('loginFormContent');
        const registerForm = document.getElementById('registerFormContent');
        if (registerForm) registerForm.style.display = 'none';
        if (loginForm) loginForm.style.display = 'block';
    };

    // Close on overlay click
    document.addEventListener('click', function (e) {
        const modal = document.getElementById('loginModal');
        if (modal && e.target === modal) window.closeLoginModal();
    });

    // Social buttons: open in same tab (server handles redirect)
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.social-btn').forEach(function (btn) {
            btn.addEventListener('click', function (e) {
                // allow normal anchor behavior; this listener simply adds a subtle animation
                btn.classList.add('pressed');
                setTimeout(function () { btn.classList.remove('pressed'); }, 220);
            });
        });
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
                window.location.reload();
            } else {
                const data = await resp.json();
                if (errorEl) { errorEl.style.display = 'block'; errorEl.innerHTML = '<i class="fas fa-circle-exclamation"></i> ' + (data.message || 'Invalid credentials'); }
            }
        } catch (err) {
            if (errorEl) { errorEl.style.display = 'block'; errorEl.innerHTML = '<i class="fas fa-circle-exclamation"></i> Something went wrong.'; }
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
                window.location.reload();
            } else {
                const data = await resp.json();
                if (errorEl) { errorEl.style.display = 'block'; errorEl.innerHTML = '<i class="fas fa-circle-exclamation"></i> ' + (data.message || Object.values(data.errors || {}).flat().join('<br>')); }
            }
        } catch (err) {
            if (errorEl) { errorEl.style.display = 'block'; errorEl.innerHTML = '<i class="fas fa-circle-exclamation"></i> Something went wrong.'; }
        }

        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fas fa-user-plus"></i> Create Account'; }
    };
})();