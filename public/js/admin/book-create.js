/**
 * Bookshop Admin — Book Create Form
 * E-book toggle, AI description, AI bulk create
 */
(function () {
    'use strict';

    // ========== E-BOOK TOGGLE ==========
    window.toggleEbookFields = function (checkbox) {
        const ebookFields = document.getElementById('ebookFields');
        const stockField = document.getElementById('stockField');
        const imageField = document.getElementById('imageField');
        const imageInput = document.getElementById('image');
        const imageRequired = document.getElementById('imageRequired');
        const stockInput = document.getElementById('stock_quantity');
        const imageHint = imageField ? imageField.querySelector('.admin-form-image-hint') : null;

        if (checkbox.checked) {
            // E-book mode
            if (ebookFields) ebookFields.style.display = 'block';
            if (stockField) stockField.style.opacity = '0.5';
            if (stockInput) {
                stockInput.value = '9999';
                stockInput.removeAttribute('required');
            }
            if (imageInput) imageInput.removeAttribute('required');
            if (imageRequired) imageRequired.style.display = 'none';
            if (imageHint) imageHint.textContent = 'Optional for e-books. JPG, JPEG, PNG. Max 2MB.';
        } else {
            // Physical book mode
            if (ebookFields) ebookFields.style.display = 'none';
            if (stockField) stockField.style.opacity = '1';
            if (stockInput) {
                stockInput.value = '0';
                stockInput.setAttribute('required', 'required');
            }
            if (imageInput) imageInput.setAttribute('required', 'required');
            if (imageRequired) imageRequired.style.display = '';
            if (imageHint) imageHint.textContent = 'JPG, JPEG or PNG. Max 2MB.';
        }
    };

    // ========== AI GENERATE DESCRIPTION ==========
    window.generateDescription = async function () {
        const title = document.getElementById('title');
        const categorySelect = document.getElementById('category_id');
        const description = document.getElementById('description');
        const btn = document.getElementById('generateDescBtn');
        const loader = document.getElementById('aiDescLoading');

        if (!title || title.value.trim().length < 3) {
            alert('Please enter a book title first (minimum 3 characters).');
            if (title) title.focus();
            return;
        }

        const category = categorySelect ? categorySelect.selectedOptions[0]?.text || '' : '';
        const routes = window.bookCreateRoutes || {};

        if (btn) {
            btn.disabled = true;
            btn.querySelector('i').className = 'fas fa-spinner fa-spin';
        }
        if (loader) loader.style.display = 'inline';

        try {
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            const resp = await fetch(routes.aiDescription, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ title: title.value.trim(), category: category }),
            });

            const data = await resp.json();
            if (data.description && description) {
                description.value = data.description;
            }
        } catch (err) {
            console.error('AI Description error:', err);
        }

        if (btn) {
            btn.disabled = false;
            btn.querySelector('i').className = 'fas fa-wand-magic-sparkles';
        }
        if (loader) loader.style.display = 'none';
    };

    // ========== AI BULK CREATE ==========
    window.bulkCreateBooks = async function () {
        const categoryId = document.getElementById('aiCategory').value;
        const language = document.getElementById('aiLanguage').value;
        const count = document.getElementById('aiCount').value;
        const stock = document.getElementById('aiStock').value;
        const topic = document.getElementById('aiTopic').value;

        const btn = document.getElementById('bulkCreateBtn');
        const loader = document.getElementById('aiBulkLoading');
        const result = document.getElementById('aiBulkResult');
        const routes = window.bookCreateRoutes || {};

        if (btn) {
            btn.disabled = true;
            btn.querySelector('i').className = 'fas fa-spinner fa-spin';
        }
        if (loader) loader.style.display = 'inline';
        if (result) result.innerHTML = '';

        try {
            const csrf = document.querySelector('meta[name="csrf-token"]').content;
            const resp = await fetch(routes.aiBulkCreate, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    category_id: categoryId,
                    language: language,
                    count: count,
                    topic: topic,
                    stock: stock,
                }),
            });

            const data = await resp.json();
            if (result) {
                result.innerHTML = '<div class="admin-alert admin-alert-success"><i class="fas fa-circle-check"></i> ' +
                    data.message + '</div>';
            }

            setTimeout(function () {
                window.location.href = routes.booksIndex;
            }, 2000);
        } catch (err) {
            console.error('AI Bulk Create error:', err);
            if (result) {
                result.innerHTML = '<div class="admin-alert admin-alert-error" style="background:#FEF2F2;color:#991B1B;border-color:#FECACA;">' +
                    '<i class="fas fa-circle-exclamation"></i> Failed to generate books. Please try again.</div>';
            }
        }

        if (btn) {
            btn.disabled = false;
            btn.querySelector('i').className = 'fas fa-wand-magic-sparkles';
        }
        if (loader) loader.style.display = 'none';
    };

    // ========== INIT ==========
    document.addEventListener('DOMContentLoaded', function () {
        const ebookCheckbox = document.getElementById('isEbookCheckbox');
        if (ebookCheckbox && ebookCheckbox.checked) {
            toggleEbookFields(ebookCheckbox);
        }
    });

})();