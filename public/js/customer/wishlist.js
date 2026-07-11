/**
 * Wishlist — Toggle, Remove, Add to Cart, Quantity
 */
(function () {
    'use strict';

    window.toggleWishlist = async function (btn, bookId) {
        if (!btn || !bookId) return;
        try {
            const resp = await fetch('/wishlist/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ book_id: bookId })
            });
            const data = await resp.json();
            if (data.added) {
                btn.classList.add('active', 'wishlisted');
                const icon = btn.querySelector('i');
                if (icon) { icon.classList.remove('far'); icon.classList.add('fas'); }
            } else {
                btn.classList.remove('active', 'wishlisted');
                const icon = btn.querySelector('i');
                if (icon) { icon.classList.remove('fas'); icon.classList.add('far'); }
            }
        } catch (e) {
            console.error('Wishlist toggle error:', e);
        }
    };

    window.removeWishlistItem = async function (btn, bookId) {
        const item = document.getElementById('wishlist-item-' + bookId);
        if (!item) return;
        try {
            const resp = await fetch('/wishlist/toggle', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ book_id: bookId })
            });
            const data = await resp.json();
            if (!data.added) {
                item.style.opacity = '0';
                item.style.transform = 'translateX(20px)';
                item.style.transition = 'all 0.3s ease';
                setTimeout(() => {
                    item.remove();
                    const remaining = document.querySelectorAll('.wishlist-item').length;
                    const countEl = document.querySelector('.wishlist-count');
                    if (countEl) countEl.textContent = remaining + ' ' + (remaining === 1 ? 'book' : 'books');
                    if (remaining === 0) location.reload();
                }, 300);
            }
        } catch (e) {
            console.error('Wishlist remove error:', e);
        }
    };

    window.addWishlistToCart = function (btn, bookId) {
        const qtyInput = document.getElementById('wishlist-qty-' + bookId);
        const quantity = qtyInput ? parseInt(qtyInput.value) || 1 : 1;
        if (typeof Cart !== 'undefined' && Cart.addItem) {
            Cart.addItem(bookId, quantity, 'physical');
        }
    };

    window.changeWishlistQty = function (btn, delta, bookId) {
        const input = document.getElementById('wishlist-qty-' + bookId);
        if (!input) return;
        let val = parseInt(input.value) + delta;
        const max = parseInt(input.max) || 99;
        if (val < 1) val = 1;
        if (val > max) val = max;
        input.value = val;
    };

})();