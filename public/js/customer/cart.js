/* =============================================
   Cart Drawer - Complete
   ============================================= */

const Cart = {
    drawer: null,
    overlay: null,
    itemsContainer: null,
    totalEl: null,
    countEl: null,

    init() {
        this.drawer = document.getElementById('cartDrawer');
        this.overlay = document.getElementById('cartOverlay');
        this.itemsContainer = document.getElementById('cartItems');
        this.totalEl = document.getElementById('cartTotal');
        this.countEl = document.getElementById('cartCount');

        // Load existing cart data
        this.loadCart();

        // Event delegation for Add to Cart buttons
        document.addEventListener('click', (e) => {
            const btn = e.target.closest('.btn-add-cart');
            if (btn) {
                e.preventDefault();
                this.addItem(btn);
            }

            // Cart drawer qty up
            const qtyUp = e.target.closest('.cart-qty-up');
            if (qtyUp) {
                e.preventDefault();
                const itemId = qtyUp.dataset.itemId;
                const qty = parseInt(qtyUp.dataset.qty) + 1;
                this.updateQty(itemId, qty);
            }

            // Cart drawer qty down
            const qtyDown = e.target.closest('.cart-qty-down');
            if (qtyDown) {
                e.preventDefault();
                const itemId = qtyDown.dataset.itemId;
                const qty = parseInt(qtyDown.dataset.qty) - 1;
                this.updateQty(itemId, qty);
            }

            // Cart drawer remove
            const removeBtn = e.target.closest('.cart-item-remove');
            if (removeBtn) {
                e.preventDefault();
                const itemId = removeBtn.dataset.itemId;
                this.removeItem(itemId);
            }
        });

        // Close cart
        document.getElementById('cartClose')?.addEventListener('click', () => this.close());
        this.overlay?.addEventListener('click', () => this.close());

        // Close on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') this.close();
        });
    },

    open() {
        if (this.drawer) this.drawer.classList.add('open');
        if (this.overlay) this.overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
    },

    close() {
        if (this.drawer) this.drawer.classList.remove('open');
        if (this.overlay) this.overlay.classList.remove('open');
        document.body.style.overflow = '';
        document.documentElement.style.overflow = '';
    },

    toggle() {
        if (this.drawer && this.drawer.classList.contains('open')) {
            this.close();
        } else {
            this.open();
        }
    },

    async addItem(btn) {
        const bookId = btn.dataset.bookId;
        const qtyInput = document.getElementById('quantity');
        const quantity = qtyInput ? parseInt(qtyInput.value) : 1;

        try {
            App.loading(true);
            const response = await App.ajax.post('/cart/add', {
                book_id: bookId,
                quantity: quantity,
            });

            App.toast('Added to cart!', 'success');
            this.updateCartUI(response.cart);
            this.open();
        } catch (error) {
            console.error('Add to cart error:', error);
        } finally {
            App.loading(false);
        }
    },

    async updateQty(cartItemId, quantity) {
        if (quantity < 1) return;

        try {
            const response = await App.ajax.put(`/cart/update/${cartItemId}`, {
                quantity: quantity,
            });
            this.updateCartUI(response.cart);
        } catch (error) {
            console.error('Update quantity error:', error);
        }
    },

    async removeItem(cartItemId) {
        try {
            const response = await App.ajax.delete(`/cart/remove/${cartItemId}`);
            this.updateCartUI(response.cart);
            App.toast('Item removed.', 'warning');
        } catch (error) {
            console.error('Remove item error:', error);
        }
    },

    async loadCart() {
        if (!this.itemsContainer) {
            this.itemsContainer = document.getElementById('cartItems');
            this.totalEl = document.getElementById('cartTotal');
            this.countEl = document.getElementById('cartCount');
        }
        try {
            const response = await App.ajax.get('/cart/data');
            if (response && response.cart) {
                this.updateCartUI(response.cart);
            }
        } catch (error) {
            // Cart is empty or not logged in
        }
    },

    updateCartUI(cart) {
        document.querySelectorAll('#cartCount').forEach(el => {
            el.textContent = `(${cart.total_items})`;
        });

        if (this.itemsContainer) {
            if (cart.items.length === 0) {
                this.itemsContainer.innerHTML = `
                    <div class="cart-empty">
                        <div class="empty-icon">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <h4>Your cart is empty</h4>
                        <p>Discover great books and add them here!</p>
                    </div>`;
            } else {
                this.itemsContainer.innerHTML = cart.items.map(item => `
                    <div class="cart-item">
                        <img src="${item.image}" alt="${item.title}" class="cart-item-image">
                        <div class="cart-item-info">
                            <div class="cart-item-title">${item.title}</div>
                            <div class="cart-item-price">${App.formatCurrency(item.price)}</div>
                            <div class="cart-item-actions">
                                <div class="cart-qty">
                                    <button class="cart-qty-down" data-item-id="${item.id}" data-qty="${item.quantity}">−</button>
                                    <span>${item.quantity}</span>
                                    <button class="cart-qty-up" data-item-id="${item.id}" data-qty="${item.quantity}">+</button>
                                </div>
                                <button class="cart-item-remove" data-item-id="${item.id}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `).join('');
            }
        }

        if (this.totalEl) {
            this.totalEl.textContent = App.formatCurrency(cart.total);
        }

        this.updateShippingBar(cart.total);
    },

    updateShippingBar(total) {
        const threshold = 50000;
        const bar = document.getElementById('shippingBar');
        const text = document.getElementById('shippingText');
        const progress = document.getElementById('shippingProgress');
        
        if (!bar || !text || !progress) return;

        if (total >= threshold) {
            text.innerHTML = '<i class="fas fa-check-circle"></i> <strong>Congratulations!</strong> You get FREE shipping!';
            progress.style.width = '100%';
            bar.style.background = 'linear-gradient(135deg, #d1fae5, #a7f3d0)';
        } else {
            const remaining = threshold - total;
            const pct = (total / threshold) * 100;
            text.innerHTML = 'Add <strong>' + App.formatCurrency(remaining) + '</strong> more for free shipping!';
            progress.style.width = pct + '%';
            bar.style.background = 'linear-gradient(135deg, #ecfdf5, #d1fae5)';
        }
    }
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => Cart.init());