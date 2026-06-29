/**
 * Bookshop Books Page — Filters & Interactions
 */
(function () {
    'use strict';

    // ========== SCROLL POSITION PRESERVATION ==========
    var scrollY = 0;
    window.addEventListener('beforeunload', function () {
        scrollY = window.scrollY;
        sessionStorage.setItem('booksScrollY', scrollY);
    });

    window.addEventListener('load', function () {
        var savedScroll = sessionStorage.getItem('booksScrollY');
        if (savedScroll && (window.location.search.includes('sort=') || window.location.search.includes('category='))) {
            setTimeout(function () {
                window.scrollTo(0, parseInt(savedScroll));
                sessionStorage.removeItem('booksScrollY');
            }, 150);
        }
    });

    // ========== MOBILE SIDEBAR ==========
    function MobileSidebar() {
        this.toggle = document.getElementById('filterToggle');
        this.sidebar = document.getElementById('booksSidebar');
        this.overlay = document.getElementById('sidebarOverlay');
        this.closeBtn = document.getElementById('sidebarClose');

        if (!this.toggle || !this.sidebar || !this.overlay) return;

        var self = this;

        this.toggle.addEventListener('click', function () { self.open(); });
        this.overlay.addEventListener('click', function () { self.close(); });
        if (this.closeBtn) this.closeBtn.addEventListener('click', function () { self.close(); });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') self.close();
        });
    }

    MobileSidebar.prototype.open = function () {
        this.sidebar.classList.add('open');
        this.overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
    };

    MobileSidebar.prototype.close = function () {
        this.sidebar.classList.remove('open');
        this.overlay.classList.remove('show');
        document.body.style.overflow = '';
    };

    // ========== PRICE VALIDATOR ==========
    function PriceValidator() {
        this.form = document.getElementById('priceFilterForm');
        if (!this.form) return;

        var self = this;
        this.form.addEventListener('submit', function (e) { self.validate(e); });
    }

    PriceValidator.prototype.validate = function (e) {
        var minInput = document.getElementById('minPrice');
        var maxInput = document.getElementById('maxPrice');
        var min = minInput ? parseInt(minInput.value) || 0 : 0;
        var max = maxInput ? parseInt(maxInput.value) || 0 : 0;

        if (max > 0 && max < min) {
            e.preventDefault();
            // Inline toast instead of alert
            var toast = document.createElement('div');
            toast.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;padding:14px 20px;border-radius:12px;font-size:14px;font-weight:500;background:var(--color-danger-bg);color:#991B1B;border:1px solid #FECACA;animation:toastIn 0.3s ease;';
            toast.innerHTML = '<i class="fas fa-circle-exclamation"></i> Maximum price must be greater than minimum price.';
            document.body.appendChild(toast);
            setTimeout(function () { toast.remove(); }, 3000);
        }
    };

    // ========== INIT ==========
    document.addEventListener('DOMContentLoaded', function () {
        new MobileSidebar();
        new PriceValidator();
    });

    window.sortBooks = function (value) {
        const url = new URL(window.location.href);
        if (value) {
            url.searchParams.set('sort', value);
        } else {
            url.searchParams.delete('sort');
        }
        url.searchParams.delete('page'); // Reset to page 1
        window.location.href = url.toString();
    };

})();