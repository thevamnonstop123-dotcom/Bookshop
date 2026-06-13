/**
 * Bookshop Books Listing — Interactions
 * Mobile filter sidebar, price validation
 */
(function () {
    'use strict';

    document.addEventListener('DOMContentLoaded', function () {
        initMobileSidebar();
        initPriceValidation();
    });

    // ========== MOBILE SIDEBAR ==========
    function initMobileSidebar() {
        const toggle = document.getElementById('filterToggle');
        const sidebar = document.getElementById('booksSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const closeBtn = document.getElementById('sidebarClose');

        if (!toggle || !sidebar || !overlay) return;

        function open() {
            sidebar.classList.add('open');
            overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function close() {
            sidebar.classList.remove('open');
            overlay.classList.remove('show');
            document.body.style.overflow = '';
        }

        toggle.addEventListener('click', open);
        overlay.addEventListener('click', close);
        if (closeBtn) closeBtn.addEventListener('click', close);

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && sidebar.classList.contains('open')) {
                close();
            }
        });
    }

    // ========== PRICE VALIDATION ==========
    function initPriceValidation() {
        const form = document.getElementById('priceFilterForm');
        if (!form) return;

        form.addEventListener('submit', function (e) {
            const minInput = document.getElementById('minPrice');
            const maxInput = document.getElementById('maxPrice');
            const min = parseInt(minInput.value) || 0;
            const max = parseInt(maxInput.value) || 0;

            if (min < 0) minInput.value = 0;
            if (max < 0) maxInput.value = 0;

            if (min > 0 && max > 0 && max < min) {
                e.preventDefault();
                alert('Maximum price must be greater than minimum price.');
            }
        });
    }
})();