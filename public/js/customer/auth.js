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
            
            // Check for error in URL
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('error')) {
                const errorEl = document.getElementById('loginError');
                if (errorEl) {
                    errorEl.style.display = 'block';
                    errorEl.innerHTML = '<i class="fas fa-circle-exclamation"></i> Invalid email or password. Please try again.';
                }
                // Clean URL without reloading
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
    
    // Clear error message when closing modal
    const errorEl = document.getElementById('loginError');
    if (errorEl) {
        errorEl.style.display = 'none';
        errorEl.innerHTML = '';
    }
    
    // Optional: Clear session error via AJAX (to prevent showing again)
    fetch('/clear-login-error', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    }).catch(err => console.log('Error cleared'));
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
            const container = document.querySelector('.password-strength');
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
    document.addEventListener('DOMContentLoaded', function() {
        const passwordField = document.getElementById('password');
        if (passwordField) {
            passwordField.addEventListener('input', updatePasswordStrength);
        }
    });

    // ========== AUTO-OPEN MODAL ON PAGE LOAD ==========
    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('login') || urlParams.has('login=open') || urlParams.has('login-open')) {
            setTimeout(function() {
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