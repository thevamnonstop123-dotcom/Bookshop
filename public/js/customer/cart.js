/**
 * Bookshop Cart Drawer — Premium Interactions
 * Open/close, item management, shipping calculation, animations
 */
(function () {
    'use strict';

    const Cart = {
        drawer: null,
        overlay: null,
        itemsContainer: null,
        totalEl: null,
        countEl: null,
        shippingText: null,
        shippingProgress: null,
        cartDrawerCount: null,
        footer: null,

        init: function () {
            this.drawer = document.getElementById('cartDrawer');
            this.overlay = document.getElementById('cartOverlay');
            this.itemsContainer = document.getElementById('cartItemsList');
            this.totalEl = document.getElementById('cartDrawerTotal');
            this.countEl = document.getElementById('cartCount');
            this.shippingText = document.getElementById('shippingMessage');
            this.shippingProgress = document.getElementById('shippingProgressFill');
            this.cartDrawerCount = document.getElementById('cartDrawerCount');
            this.footer = document.getElementById('cartDrawerFooter');

            this.loadCart();
            this.bindEvents();
        },

        bindEvents: function () {
            const self = this;

            document.addEventListener('click', function (e) {
                const addBtn = e.target.closest('.btn-add-cart');
                if (addBtn) {
                    e.preventDefault();
                    const qtyInput = document.getElementById('quantity');
                    const quantity = qtyInput ? parseInt(qtyInput.value) || 1 : 1;
                    self.addItem(addBtn.dataset.bookId, quantity);
                    return;
                }

                const qtyUp = e.target.closest('.cart-qty-up');
                if (qtyUp) {
                    e.preventDefault();
                    const itemId = qtyUp.dataset.itemId;
                    const newQty = parseInt(qtyUp.dataset.qty) + 1;
                    self.updateQty(itemId, newQty);
                    return;
                }

                const qtyDown = e.target.closest('.cart-qty-down');
                if (qtyDown) {
                    e.preventDefault();
                    const itemId = qtyDown.dataset.itemId;
                    const newQty = parseInt(qtyDown.dataset.qty) - 1;
                    if (newQty < 1) {
                        self.removeItem(itemId);
                    } else {
                        self.updateQty(itemId, newQty);
                    }
                    return;
                }

                const removeBtn = e.target.closest('.cart-item-remove');
                if (removeBtn) {
                    e.preventDefault();
                    self.removeItem(removeBtn.dataset.itemId);
                    return;
                }
            });

            const closeBtn = document.getElementById('cartDrawerClose');
            if (closeBtn) closeBtn.addEventListener('click', function () { self.close(); });

            const continueBtn = document.getElementById('cartContinueShopping');
            if (continueBtn) continueBtn.addEventListener('click', function () { self.close(); });

            if (this.overlay) this.overlay.addEventListener('click', function () { self.close(); });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') self.close();
            });
        },

        open: function () {
            if (this.drawer) {
                this.drawer.classList.add('open');
                this.drawer.setAttribute('aria-hidden', 'false');
            }
            if (this.overlay) this.overlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        },

        close: function () {
            if (this.drawer) {
                this.drawer.classList.remove('open');
                this.drawer.setAttribute('aria-hidden', 'true');
            }
            if (this.overlay) this.overlay.classList.remove('show');
            document.body.style.overflow = '';
        },

        toggle: function () {
            if (this.drawer && this.drawer.classList.contains('open')) {
                this.close();
            } else {
                this.open();
            }
        },

        addItem: async function (bookId, quantity) {
            quantity = quantity || 1;
            try {
                const resp = await fetch('/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ book_id: bookId, quantity: quantity }),
                });
                const data = await resp.json();
                this.updateCartUI(data.cart, 'add');
                
            } catch (err) {
                console.error('Add to cart error:', err);
            }
        },

        updateQty: async function (itemId, quantity) {
            if (quantity < 1) return;
            try {
                var resp = await fetch('/cart/update/' + itemId, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ quantity: quantity }),
                });
                var data = await resp.json();
                this.updateCartUI(data.cart, 'update');
            } catch (err) {
                console.error('Update qty error:', err);
            }
        },

        removeItem: async function (itemId) {
            try {
                var resp = await fetch('/cart/remove/' + itemId, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
                var data = await resp.json();
                this.updateCartUI(data.cart, 'remove');
            } catch (err) {
                console.error('Remove item error:', err);
            }
        },

        loadCart: async function () {
            try {
                var resp = await fetch('/cart/data', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                });
                var data = await resp.json();
                this.updateCartUI(data.cart, 'full');
            } catch (err) {
                console.error('Load cart error:', err);
            }
        },

        updateCartUI: function (cart, action) {
            action = action || 'full';
            const count = cart.total_items || 0;

            const allBadges = document.querySelectorAll('#cartCount, #mobileCartCount');
            allBadges.forEach(function (el) {
                el.textContent = count;
                if (count > 0) {
                    el.style.display = 'flex';
                } else {
                    el.style.display = 'none';
                }
            });

            if (this.cartDrawerCount) {
                this.cartDrawerCount.textContent = count + ' item' + (count !== 1 ? 's' : '');
            }

            if (this.totalEl) {
                this.totalEl.textContent = this.formatCurrency(cart.total);
            }

            this.updateShippingBar(cart.total);

            if (this.footer) {
                this.footer.style.display = count > 0 ? '' : 'none';
            }

            if (!this.itemsContainer) return;

            if (action === 'add' || action === 'remove' || action === 'full') {
                this.renderItems(cart.items || []);
            }

            if (action === 'update') {
                const self = this;
                (cart.items || []).forEach(function (item) {
                    const itemEl = self.itemsContainer.querySelector('.cart-item[data-item-id="' + item.id + '"]');
                    if (!itemEl) return;

                    const qtySpan = itemEl.querySelector('.cart-qty span');
                    if (qtySpan) qtySpan.textContent = item.quantity;

                    const downBtn = itemEl.querySelector('.cart-qty-down');
                    const upBtn = itemEl.querySelector('.cart-qty-up');
                    if (downBtn) downBtn.dataset.qty = item.quantity;
                    if (upBtn) upBtn.dataset.qty = item.quantity;

                    const priceEl = itemEl.querySelector('.cart-item-price');
                    if (priceEl) {
                        priceEl.textContent = self.formatCurrency(item.price * item.quantity);
                    }
                });
            }

            // Fire event for navbar badges
            document.dispatchEvent(new CustomEvent('cartUpdated', { detail: { count: count } }));
        },

        renderItems: function (items) {
            if (!items || items.length === 0) {
                this.itemsContainer.innerHTML =
                    '<div class="cart-empty-state">' +
                        '<div class="cart-empty-icon"><i class="fas fa-shopping-bag"></i></div>' +
                        '<h4>Your cart is empty</h4>' +
                        '<p>Looks like you have not added any books yet.</p>' +
                        '<a href="/books" class="cart-empty-btn"><i class="fas fa-book-open"></i> Browse Books</a>' +
                    '</div>';
            } else {
                const self = this;
                this.itemsContainer.innerHTML = items.map(function (item) {
                    return '<div class="cart-item" data-item-id="' + item.id + '">' +
                        '<img src="' + item.image + '" alt="' + item.title + '" class="cart-item-image">' +
                        '<div class="cart-item-info">' +
                            '<div class="cart-item-title">' + item.title + '</div>' +
                            '<div class="cart-item-price">' + self.formatCurrency(item.price * item.quantity) + '</div>' +
                            '<div class="cart-item-actions">' +
                                '<div class="cart-qty">' +
                                    '<button type="button" class="cart-qty-down" data-item-id="' + item.id + '" data-qty="' + item.quantity + '">−</button>' +
                                    '<span>' + item.quantity + '</span>' +
                                    '<button type="button" class="cart-qty-up" data-item-id="' + item.id + '" data-qty="' + item.quantity + '">+</button>' +
                                '</div>' +
                                '<button type="button" class="cart-item-remove" data-item-id="' + item.id + '">' +
                                    '<i class="fas fa-trash"></i>' +
                                '</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>';
                }).join('');
            }
        },

        updateShippingBar: function (total) {
            const threshold = 50000;
            if (!this.shippingText || !this.shippingProgress) return;

            if (total >= threshold) {
                this.shippingText.innerHTML = '<i class="fas fa-check-circle"></i> <strong>Congratulations!</strong> You get FREE shipping!';
                this.shippingProgress.style.width = '100%';
            } else {
                const remaining = threshold - total;
                const pct = (total / threshold) * 100;
                this.shippingText.innerHTML = 'Add <strong>' + this.formatCurrency(remaining) + '</strong> more for free shipping!';
                this.shippingProgress.style.width = pct + '%';
            }
        },

        formatCurrency: function (amount) {
            return Number(amount).toLocaleString('en-US') + ' MMK';
        }
    };

    // ========== WISHLIST TOGGLE — Optimistic UI ==========

    // ========== INIT ==========
    document.addEventListener('DOMContentLoaded', function () {
        Cart.init();
        // initWishlistToggle(); // handled by app.js
    });

    // Expose Cart globally
    window.Cart = Cart;

})();