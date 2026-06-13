/**
 * Bookshop Book Detail — Interactions
 * Quantity selector, Buy Now flow and format switch (UI-only)
 */
(function () {
    'use strict';

    // ========== QUANTITY SELECTOR ==========
    window.changeQuantity = function (amount) {
        const input = document.getElementById('quantity');
        if (!input) return;

        let value = parseInt(input.value) + amount;
        const max = parseInt(input.getAttribute('max')) || 99;
        if (value < 1) value = 1;
        if (value > max) value = max;
        input.value = value;
    };

    // ========== FORMAT SWITCH (UI-only) ==========
    function initFormatSwitcher() {
        const switcher = document.querySelector('.detail-format-switch');
        if (!switcher) return;
        const options = switcher.querySelectorAll('.format-option');
        options.forEach(opt => {
            opt.addEventListener('click', function () {
                if (this.classList.contains('active')) return;
                options.forEach(o => o.classList.remove('active'));
                this.classList.add('active');
                const format = this.dataset.format;

                // Update visible format tag
                const tag = document.querySelector('.detail-format-tag');
                if (tag) {
                    if (format === 'ebook') {
                        tag.classList.remove('detail-format-physical');
                        tag.classList.add('detail-format-ebook');
                        tag.innerHTML = '<i class="fas fa-bolt"></i> E-Book';
                    } else {
                        tag.classList.remove('detail-format-ebook');
                        tag.classList.add('detail-format-physical');
                        tag.innerHTML = '<i class="fas fa-book"></i> Physical';
                    }
                }

                // Update stock hint
                const stock = document.querySelector('.detail-stock');
                if (stock) {
                    if (format === 'ebook') {
                        stock.className = 'detail-stock detail-stock-ebook';
                        stock.innerHTML = '<i class="fas fa-infinity"></i> Instant access — read anytime';
                    } else {
                        // leave server-provided stock text if present; fallback to generic
                        if (stock.dataset.physicalText) {
                            stock.innerHTML = stock.dataset.physicalText;
                        } else {
                            stock.className = 'detail-stock detail-stock-in';
                            stock.innerHTML = '<i class="fas fa-check-circle"></i> In stock';
                        }
                    }
                }
            });
        });
    }

    // ========== BUY NOW ==========
    document.addEventListener('DOMContentLoaded', function () {
        const buyNowBtn = document.getElementById('buyNowBtn');
        if (!buyNowBtn) return;

        buyNowBtn.addEventListener('click', function () {
            const addToCartBtn = document.querySelector('.btn-add-cart');
            if (addToCartBtn) {
                addToCartBtn.click();
            }
            setTimeout(function () {
                window.location.href = '/checkout';
            }, 500);
        });

        initFormatSwitcher();
    });
})();