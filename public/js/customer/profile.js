/**
 * Bookshop Profile — Premium Interactions
 */
(function () {
    'use strict';

    // ========== PHOTO UPLOAD ==========
    window.uploadPhoto = async function (event) {
        const file = event.target.files[0];
        if (!file) return;

        if (file.size > 2 * 1024 * 1024) {
            showToast('Image must be under 2MB.', 'error');
            return;
        }

        const formData = new FormData();
        formData.append('image', file);

        try {
            const resp = await fetch('/profile/photo', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });
            const data = await resp.json();

            if (resp.ok) {
                document.querySelectorAll('#bannerAvatar, #photoPreview').forEach(function (el) {
                    el.src = data.image_url;
                });
                showToast('Photo updated successfully!', 'success');
            } else {
                showToast(data.message || 'Upload failed.', 'error');
            }
        } catch (err) {
            showToast('Upload failed. Please try again.', 'error');
        }
    };

    // ========== EMAIL MODAL ==========
    window.openEmailModal = function () {
        const overlay = document.getElementById('emailModalOverlay');
        if (!overlay) return;
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
        const input = overlay.querySelector('input[name="email"]');
        if (input) input.focus();
    };

    window.closeEmailModal = function () {
        const overlay = document.getElementById('emailModalOverlay');
        if (!overlay) return;
        overlay.classList.remove('open');
        document.body.style.overflow = '';
    };

    // ========== ADDRESS MODAL ==========
    window.openAddressModal = function () {
        const form = document.getElementById('addressModalForm');
        if (!form) return;
        form.action = '/profile/address';
        document.getElementById('addressModalMethod').value = 'POST';
        document.getElementById('addressModalTitle').textContent = 'Add New Address';
        document.getElementById('addressModalSubmitBtn').innerHTML = '<i class="fas fa-plus"></i> Add Address';
        document.getElementById('addrModalName').value = '';
        document.getElementById('addrModalPhone').value = '';
        document.getElementById('addrModalLine').value = '';

        document.getElementById('addressModalOverlay').classList.add('open');
        document.body.style.overflow = 'hidden';
        document.getElementById('addrModalName').focus();
    };

    window.editAddressModal = function (id, name, phone, line) {
        const form = document.getElementById('addressModalForm');
        if (!form) return;
        form.action = '/profile/address/' + id;
        document.getElementById('addressModalMethod').value = 'PUT';
        document.getElementById('addressModalTitle').textContent = 'Edit Address';
        document.getElementById('addressModalSubmitBtn').innerHTML = '<i class="fas fa-save"></i> Update Address';
        document.getElementById('addrModalName').value = name;
        document.getElementById('addrModalPhone').value = phone;
        document.getElementById('addrModalLine').value = line;

        document.getElementById('addressModalOverlay').classList.add('open');
        document.body.style.overflow = 'hidden';
        document.getElementById('addrModalName').focus();
    };

    window.closeAddressModal = function () {
        const overlay = document.getElementById('addressModalOverlay');
        if (!overlay) return;
        overlay.classList.remove('open');
        document.body.style.overflow = '';
    };

    // ========== PASSWORD TOGGLE ==========
    window.togglePassword = function (btn) {
        const input = btn.parentElement.querySelector('input');
        const icon = btn.querySelector('i');
        if (!input || !icon) return;
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    };

    // ========== PASSWORD STRENGTH ==========
    function initPasswordStrength() {
        const input = document.getElementById('newPassword');
        if (!input) return;

        const strengthBar = document.getElementById('passwordStrength');
        const fill = document.getElementById('strengthFill');
        const reqs = {
            length: document.getElementById('req-length'),
            uppercase: document.getElementById('req-uppercase'),
            number: document.getElementById('req-number'),
            special: document.getElementById('req-special'),
        };

        input.addEventListener('input', function () {
            const val = this.value;
            if (!val) { strengthBar.style.display = 'none'; return; }
            strengthBar.style.display = '';

            const checks = {
                length: val.length >= 8,
                uppercase: /[A-Z]/.test(val),
                number: /[0-9]/.test(val),
                special: /[^A-Za-z0-9]/.test(val),
            };

            const met = Object.values(checks).filter(Boolean).length;

            Object.keys(checks).forEach(function (key) {
                if (reqs[key]) {
                    reqs[key].classList.toggle('met', checks[key]);
                    const icon = reqs[key].querySelector('i');
                    if (icon) {
                        icon.classList.replace(checks[key] ? 'fa-circle' : 'fa-circle-check', checks[key] ? 'fa-circle-check' : 'fa-circle');
                    }
                }
            });

            const pct = (met / 4) * 100;
            fill.style.width = pct + '%';

            if (met <= 1) fill.style.background = 'var(--color-danger)';
            else if (met === 2) fill.style.background = 'var(--color-accent)';
            else if (met === 3) fill.style.background = 'var(--color-accent-light)';
            else fill.style.background = 'var(--color-success)';
        });
    }

    // ========== TOAST ==========
    window.showToast = function (message, type) {
        const toast = document.createElement('div');
        toast.className = 'profile-toast profile-toast-' + (type || 'success');
        toast.innerHTML = '<i class="fas fa-' + (type === 'error' ? 'circle-exclamation' : 'circle-check') + '"></i> ' + message +
            '<button onclick="this.parentElement.remove()"><i class="fas fa-xmark"></i></button>';
        const container = document.querySelector('.container');
        if (container) {
            const tabsWrapper = container.querySelector('.profile-tabs-wrapper');
            if (tabsWrapper) {
                container.insertBefore(toast, tabsWrapper);
            } else {
                container.insertBefore(toast, container.firstChild);
            }
            setTimeout(function () { if (toast.parentElement) toast.remove(); }, 4000);
        }
    };

    // ========== MODAL ESCAPE ==========
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeEmailModal();
            closeAddressModal();
        }
    });

    // ========== INIT ==========
    document.addEventListener('DOMContentLoaded', function () {
        initPasswordStrength();
    });

})();