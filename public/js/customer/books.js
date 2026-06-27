(function () {
    'use strict';

    class BookFilters {
        constructor() {
            this.sidebar = new MobileSidebar();
            this.priceValidator = new PriceValidator();
        }
    }

    class MobileSidebar {
        constructor() {
            this.toggle = document.getElementById('filterToggle');
            this.sidebar = document.getElementById('booksSidebar');
            this.overlay = document.getElementById('sidebarOverlay');
            this.closeBtn = document.getElementById('sidebarClose');
            this.init();
        }

        init() {
            if (!this.toggle || !this.sidebar || !this.overlay) return;
            this.toggle.addEventListener('click', () => this.open());
            this.overlay.addEventListener('click', () => this.close());
            if (this.closeBtn) this.closeBtn.addEventListener('click', () => this.close());
            document.addEventListener('keydown', (e) => e.key === 'Escape' && this.close());
        }

        open() {
            this.sidebar.classList.add('open');
            this.overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        close() {
            this.sidebar.classList.remove('open');
            this.overlay.classList.remove('show');
            document.body.style.overflow = '';
        }
    }

    class PriceValidator {
        constructor() {
            this.form = document.getElementById('priceFilterForm');
            this.init();
        }

        init() {
            if (!this.form) return;
            this.form.addEventListener('submit', (e) => this.validate(e));
        }

        validate(e) {
            const min = parseInt(document.getElementById('minPrice')?.value) || 0;
            const max = parseInt(document.getElementById('maxPrice')?.value) || 0;

            if (max > 0 && max < min) {
                e.preventDefault();
                alert('Maximum price must be greater than minimum price.');
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => new BookFilters());
})();

