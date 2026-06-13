/**
 * Bookshop Admin Form — Shared Utilities
 * Image preview
 */
(function () {
    'use strict';

    window.previewImage = function (event) {
        const file = event.target.files[0];
        if (!file) return;

        const preview = document.getElementById('imagePreview');
        const placeholder = document.getElementById('imagePlaceholder');

        if (preview) {
            preview.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        }

        if (placeholder) {
            placeholder.style.display = 'none';
        }
    };

})();