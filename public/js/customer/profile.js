/**
 * Bookshop Profile — Premium Interactions + AJAX Tabs
 */
(function () {
    'use strict';

    // ========== PHOTO UPLOAD ==========
    window.uploadPhoto = async function (event) {
        const file = event.target.files[0];
        if (!file) return;
        if (file.size > 2 * 1024 * 1024) { showToast('Image must be under 2MB.', 'error'); return; }
        const formData = new FormData();
        formData.append('image', file);
        try {
            const resp = await fetch('/profile/photo', {
                method: 'POST', body: formData,
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' },
            });
            const data = await resp.json();
            if (resp.ok) {
                document.querySelectorAll('#bannerAvatar, #photoPreview').forEach(function (el) { el.src = data.image_url; });
                showToast('Photo updated!', 'success');
            } else { showToast(data.message || 'Upload failed.', 'error'); }
        } catch (err) { showToast('Upload failed.', 'error'); }
    };

    window.openEmailModal = function () {
        const overlay = document.getElementById('emailModalOverlay');
        if (!overlay) return;
        overlay.classList.add('open'); document.body.style.overflow = 'hidden';
        (overlay.querySelector('input[name="email"]') || {}).focus?.();
    };
    window.closeEmailModal = function () {
        const overlay = document.getElementById('emailModalOverlay');
        if (!overlay) return;
        overlay.classList.remove('open'); document.body.style.overflow = '';
    };

    window.openAddressModal = function () {
        const form = document.getElementById('addressModalForm');
        if (!form) return;
        form.action = '/profile/address';
        document.getElementById('addressModalMethod').value = 'POST';
        document.getElementById('addressModalTitle').textContent = 'Add New Address';
        document.getElementById('addressModalSubmitBtn').innerHTML = '<i class="fas fa-plus"></i> Add Address';
        ['addrModalName','addrModalPhone','addrModalLine'].forEach(function(id) { var el = document.getElementById(id); if (el) el.value = ''; });
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
        overlay.classList.remove('open'); document.body.style.overflow = '';
    };

    window.togglePassword = function (btn) {
        const input = btn.parentElement.querySelector('input');
        const icon = btn.querySelector('i');
        if (!input || !icon) return;
        if (input.type === 'password') { input.type = 'text'; icon.classList.replace('fa-eye', 'fa-eye-slash'); }
        else { input.type = 'password'; icon.classList.replace('fa-eye-slash', 'fa-eye'); }
    };

    window.showToast = function (message, type) {
        const toast = document.createElement('div');
        toast.className = 'profile-toast profile-toast-' + (type || 'success');
        toast.innerHTML = '<i class="fas fa-' + (type === 'error' ? 'circle-exclamation' : 'circle-check') + '"></i> ' + message + '<button onclick="this.parentElement.remove()"><i class="fas fa-xmark"></i></button>';
        const container = document.querySelector('.container');
        if (container) {
            const tw = container.querySelector('.profile-tabs-wrapper');
            if (tw) container.insertBefore(toast, tw);
            else container.insertBefore(toast, container.firstChild);
            setTimeout(function () { if (toast.parentElement) toast.remove(); }, 4000);
        }
    };

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') { closeEmailModal(); closeAddressModal(); }
    });

    // ========== TAB SWITCHING + STICKY ==========
    const tabsWrapper = document.querySelector('.profile-tabs-wrapper');
    const tabs = document.querySelectorAll('.profile-tab');
    const contentArea = document.querySelector('.profile-tab-content');

    if (tabs.length && contentArea) {
        contentArea.id = 'tab-content-area';

        window.switchProfileTab = function (tabName) {
            tabs.forEach(function (el) { el.classList.remove('profile-tab-active'); });
            tabs.forEach(function (tab) {
                if ((tab.getAttribute('onclick') || '').indexOf("'" + tabName + "'") > -1) tab.classList.add('profile-tab-active');
            });

            contentArea.style.opacity = '0.5';

            const url = new URL(window.location.href);
            url.searchParams.set('tab', tabName);

            fetch(url.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                contentArea.innerHTML = data.html;
                contentArea.style.opacity = '1';
                window.history.pushState({ tab: tabName }, '', url.toString());
                if (tabName === 'security') initPasswordStrength();
            })
            .catch(function () {
                contentArea.style.opacity = '1';
                window.location.href = url.toString();
            });
        };

        function initPasswordStrength() {
            const input = document.getElementById('newPassword');
            if (!input) return;
            const strengthBar = document.getElementById('passwordStrength');
            const fill = document.getElementById('strengthFill');
            const reqs = { length: document.getElementById('req-length'), uppercase: document.getElementById('req-uppercase'), number: document.getElementById('req-number'), special: document.getElementById('req-special') };
            input.addEventListener('input', function () {
                const val = this.value;
                if (!val) { if (strengthBar) strengthBar.style.display = 'none'; return; }
                if (strengthBar) strengthBar.style.display = '';
                const checks = { length: val.length >= 8, uppercase: /[A-Z]/.test(val), number: /[0-9]/.test(val), special: /[^A-Za-z0-9]/.test(val) };
                const met = Object.values(checks).filter(Boolean).length;
                Object.keys(checks).forEach(function (key) {
                    if (reqs[key]) {
                        reqs[key].classList.toggle('met', checks[key]);
                        const icon = reqs[key].querySelector('i');
                        if (icon) icon.classList.replace(checks[key] ? 'fa-circle' : 'fa-circle-check', checks[key] ? 'fa-circle-check' : 'fa-circle');
                    }
                });
                const pct = (met / 4) * 100;
                if (fill) fill.style.width = pct + '%';
                if (fill) fill.style.background = met <= 1 ? 'var(--color-danger)' : met === 2 ? 'var(--color-accent)' : met === 3 ? 'var(--color-accent-light)' : 'var(--color-success)';
            });
        }

        // Sticky tabs
        function handleSticky() {
            if (!tabsWrapper) return;
            const banner = document.querySelector('.profile-banner');
            if (!banner) return;
            const nh = parseInt(getComputedStyle(document.documentElement).getPropertyValue('--navbar-height')) || 68;
            if (banner.getBoundingClientRect().bottom <= nh) {
                tabsWrapper.style.cssText = 'position:fixed;top:' + nh + 'px;left:0;right:0;z-index:40;background:var(--color-bg,#F1F5F9);box-shadow:0 2px 8px rgba(0,0,0,0.06);padding:8px 0;';
            } else {
                tabsWrapper.style.cssText = '';
            }
        }
        let ticking = false;
        window.addEventListener('scroll', function () {
            if (!ticking) { requestAnimationFrame(function () { handleSticky(); ticking = false; }); ticking = true; }
        });
        window.addEventListener('resize', handleSticky);
        handleSticky();
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (document.getElementById('newPassword')) initPasswordStrength();
    });
})();
