/**
 * Bookshop Cart Drawer — Premium Interactions
 * Open/close, item management, shipping calculation, animations
 */

const Cart = {
    drawer: null,
    overlay: null,
    itemsContainer: null,
    totalEl: null,
    countEl: null,
    shippingText: null,
    shippingProgress: null,
    cartDrawerCount: null,
    emptyState: null,
    footer: null,

    init() {
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

        // Event delegation for all cart actions
        document.addEventListener('click', (e) => {
            // Add to cart
            const addBtn = e.target.closest('.btn-add-cart');
            if (addBtn) {
                e.preventDefault();
                const qtyInput = document.getElementById('quantity');
                const quantity = qtyInput ? parseInt(qtyInput.value) || 1 : 1;
                this.addItem(addBtn.dataset.bookId, quantity);
                return;
            }

            // Qty up
            const qtyUp = e.target.closest('.cart-qty-up');
            if (qtyUp) {
                e.preventDefault();
                const itemId = qtyUp.dataset.itemId;
                const newQty = parseInt(qtyUp.dataset.qty) + 1;
                this.updateQty(itemId, newQty);
                return;
            }

            // Qty down
            const qtyDown = e.target.closest('.cart-qty-down');
            if (qtyDown) {
                e.preventDefault();
                const itemId = qtyDown.dataset.itemId;
                const newQty = parseInt(qtyDown.dataset.qty) - 1;
                if (newQty < 1) {
                    this.removeItem(itemId);
                } else {
                    this.updateQty(itemId, newQty);
                }
                return;
            }

            // Remove item
            const removeBtn = e.target.closest('.cart-item-remove');
            if (removeBtn) {
                e.preventDefault();
                this.removeItem(removeBtn.dataset.itemId);
                return;
            }
        });

        // Close cart
        document.getElementById('cartDrawerClose')?.addEventListener('click', () => this.close());
        document.getElementById('cartContinueShopping')?.addEventListener('click', () => this.close());
        this.overlay?.addEventListener('click', () => this.close());
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') this.close();
        });
    },

    open() {
        if (this.drawer) {
            this.drawer.classList.add('open');
            this.drawer.setAttribute('aria-hidden', 'false');
        }
        if (this.overlay) this.overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
    },

    close() {
        if (this.drawer) {
            this.drawer.classList.remove('open');
            this.drawer.setAttribute('aria-hidden', 'true');
        }
        if (this.overlay) this.overlay.classList.remove('show');
        document.body.style.overflow = '';
    },

    toggle() {
        if (this.drawer?.classList.contains('open')) {
            this.close();
        } else {
            this.open();
        }
    },

        async addItem(bookId, quantity = 1) {
        try {
            const resp = await fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ book_id: bookId, quantity }),
            });
            const data = await resp.json();
            this.updateCartUI(data.cart, 'add');
        } catch (err) {
            console.error('Add to cart error:', err);
        }
    },

    async updateQty(itemId, quantity) {
        if (quantity < 1) return;
        try {
            const resp = await fetch(`/cart/update/${itemId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ quantity }),
            });
            const data = await resp.json();
            this.updateCartUI(data.cart, 'update');
        } catch (err) {
            console.error('Update qty error:', err);
        }
    },

    async removeItem(itemId) {
        try {
            const resp = await fetch(`/cart/remove/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });
            const data = await resp.json();
            this.updateCartUI(data.cart, 'remove');
        } catch (err) {
            console.error('Remove item error:', err);
        }
    },

    async loadCart() {
        try {
            const resp = await fetch('/cart/data', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                },
            });
            const data = await resp.json();
            this.updateCartUI(data.cart, 'full');
        } catch (err) {
            console.error('Load cart error:', err);
        }
    },

    updateCartUI(cart, action = 'full') {
        const count = cart.total_items || 0;

        // Update count badges
        document.querySelectorAll('#cartCount').forEach(el => {
            el.textContent = count;
        });
        if (this.cartDrawerCount) {
            this.cartDrawerCount.textContent = count + ' item' + (count !== 1 ? 's' : '');
        }

        // Update total
        if (this.totalEl) {
            this.totalEl.textContent = this.formatCurrency(cart.total);
        }

        // Update shipping bar
        this.updateShippingBar(cart.total);

        // Show/hide footer
        if (this.footer) {
            this.footer.style.display = count > 0 ? '' : 'none';
        }

        if (!this.itemsContainer) return;

        // Rebuild for add/remove/full
        if (action === 'add' || action === 'remove' || action === 'full') {
            this.renderItems(cart.items || []);
        }

        // Update in-place for quantity changes
        if (action === 'update') {
            (cart.items || []).forEach(item => {
                const itemEl = this.itemsContainer.querySelector('.cart-item[data-item-id="' + item.id + '"]');
                if (!itemEl) return;

                const qtySpan = itemEl.querySelector('.cart-qty span');
                if (qtySpan) qtySpan.textContent = item.quantity;

                const downBtn = itemEl.querySelector('.cart-qty-down');
                const upBtn = itemEl.querySelector('.cart-qty-up');
                if (downBtn) downBtn.dataset.qty = item.quantity;
                if (upBtn) upBtn.dataset.qty = item.quantity;

                const priceEl = itemEl.querySelector('.cart-item-price');
                if (priceEl) {
                    priceEl.textContent = this.formatCurrency(item.price * item.quantity);
                }
            });
        }
    },

    renderItems(items) {
        if (!items || items.length === 0) {
            this.itemsContainer.innerHTML = `
                <div class="cart-empty-state">
                    <div class="cart-empty-icon"><i class="fas fa-shopping-bag"></i></div>
                    <h4>Your cart is empty</h4>
                    <p>Looks like you have not added any books yet.</p>
                    <a href="/books" class="cart-empty-btn"><i class="fas fa-book-open"></i> Browse Books</a>
                </div>`;
        } else {
            this.itemsContainer.innerHTML = items.map(item => `
                <div class="cart-item" data-item-id="${item.id}">
                    <img src="${item.image}" alt="${item.title}" class="cart-item-image">
                    <div class="cart-item-info">
                        <div class="cart-item-title">${item.title}</div>
                        <div class="cart-item-price">${this.formatCurrency(item.price * item.quantity)}</div>
                        <div class="cart-item-actions">
                            <div class="cart-qty">
                                <button type="button" class="cart-qty-down" data-item-id="${item.id}" data-qty="${item.quantity}">−</button>
                                <span>${item.quantity}</span>
                                <button type="button" class="cart-qty-up" data-item-id="${item.id}" data-qty="${item.quantity}">+</button>
                            </div>
                            <button type="button" class="cart-item-remove" data-item-id="${item.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `).join('');
        }
    },

    updateShippingBar(total) {
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

    formatCurrency(amount) {
        return Number(amount).toLocaleString('en-US') + ' MMK';
    },

    
};

document.addEventListener('DOMContentLoaded', () => Cart.init());
// Wishlist toggle — optimistic UI update
document.addEventListener('click', function (e) {
    const heartBtn = e.target.closest('.wishlist-toggle, .book-card-wishlist, .wishlist-btn');
    if (!heartBtn) return;

    e.preventDefault();

    const bookId = heartBtn.dataset.bookId;
    const icon = heartBtn.querySelector('i');
    const isCurrentlyLiked = heartBtn.classList.contains('active') || icon?.classList.contains('fas');

    // Optimistic update
    if (isCurrentlyLiked) {
        heartBtn.classList.remove('active');
        if (icon) { icon.classList.remove('fas'); icon.classList.add('far'); }
    } else {
        heartBtn.classList.add('active');
        if (icon) { icon.classList.remove('far'); icon.classList.add('fas'); }
    }

    // Send to server
    fetch('/wishlist/toggle', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ book_id: bookId })
    }).catch(() => {
        // Revert on error
        if (isCurrentlyLiked) {
            heartBtn.classList.add('active');
            if (icon) { icon.classList.add('fas'); icon.classList.remove('far'); }
        } else {
            heartBtn.classList.remove('active');
            if (icon) { icon.classList.remove('fas'); icon.classList.add('far'); }
        }
    });
});