/**
 * Category Page — Filter Toggle
 */
(function () {
    'use strict';

    window.toggleCategoryFilters = function () {
        const filters = document.getElementById('categoryFilters');
        const overlay = document.getElementById('categoryFiltersOverlay');

        if (filters) filters.classList.toggle('open');
        if (overlay) overlay.classList.toggle('show');
        document.body.style.overflow = filters && filters.classList.contains('open') ? 'hidden' : '';
    };

    // Close on Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            const filters = document.getElementById('categoryFilters');
            if (filters && filters.classList.contains('open')) {
                window.toggleCategoryFilters();
            }
        }
    });

    window.sortCategoryBooks = function (value) {
        const form = document.getElementById('categoryFilterForm');
        if (!form) return;
        const sortInput = form.querySelector('input[name="sort"]') || document.createElement('input');
        sortInput.type = 'hidden';
        sortInput.name = 'sort';
        sortInput.value = value;
        form.appendChild(sortInput);
        form.submit();
    };

})();