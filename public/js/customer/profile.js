/**
 * Bookshop Profile — Interactions
 * Avatar preview, address edit mode
 */
(function () {
    'use strict';

    // ========== AVATAR PREVIEW ==========
    window.previewAvatar = function (event) {
        const file = event.target.files[0];
        if (!file) return;

        const preview = document.getElementById('avatarPreview');
        const bannerPreview = document.getElementById('bannerAvatar');
        const url = URL.createObjectURL(file);

        if (preview) preview.src = url;
        if (bannerPreview) bannerPreview.src = url;
    };

    // ========== EDIT ADDRESS ==========
    function initAddressEdit() {
        const editButtons = document.querySelectorAll('.address-action-edit');
        const formSection = document.getElementById('addressFormSection');
        const form = document.getElementById('addressForm');
        const methodInput = document.getElementById('addressMethod');
        const nameInput = document.getElementById('addrName');
        const phoneInput = document.getElementById('addrPhone');
        const lineInput = document.getElementById('addrLine');
        const titleEl = document.getElementById('addressFormTitle');
        const submitBtn = document.getElementById('addressSubmitBtn');
        const cancelBtn = document.getElementById('addressCancelBtn');

        if (!editButtons.length || !form) return;

        const baseStoreUrl = form.getAttribute('action');
        let editingId = null;

        function setEditMode(id, name, phone, line) {
            editingId = id;
            form.setAttribute('action', '/profile/address/' + id);
            methodInput.value = 'PUT';
            nameInput.value = name;
            phoneInput.value = phone;
            lineInput.value = line;
            titleEl.textContent = 'Edit Address';
            submitBtn.innerHTML = '<i class="fas fa-save"></i> Update Address';
            cancelBtn.style.display = 'inline-flex';

            // Scroll to form
            if (formSection) {
                formSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        function resetForm() {
            editingId = null;
            form.setAttribute('action', baseStoreUrl);
            methodInput.value = 'POST';
            nameInput.value = '';
            phoneInput.value = '';
            lineInput.value = '';
            titleEl.textContent = 'Add New Address';
            submitBtn.innerHTML = '<i class="fas fa-plus"></i> Add Address';
            cancelBtn.style.display = 'none';
        }

        editButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                const id = this.dataset.id;
                const name = this.dataset.name;
                const phone = this.dataset.phone;
                const line = this.dataset.line;
                setEditMode(id, name, phone, line);
            });
        });

        if (cancelBtn) {
            cancelBtn.addEventListener('click', resetForm);
        }
    }

    // ========== INIT ==========
    document.addEventListener('DOMContentLoaded', function () {
        initAddressEdit();
    });

})();