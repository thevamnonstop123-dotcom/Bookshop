/* =============================================
   Cart Drawer - Optimized (No Full Rebuild)
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

        this.loadCart();

        // Event delegation for ALL cart actions
        document.addEventListener('click', (e) => {
            // Add to cart
            const addBtn = e.target.closest('.btn-add-cart');
            if (addBtn) {
                e.preventDefault();
                this.addItem(addBtn.dataset.bookId);
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
                this.updateQty(itemId, newQty);
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

        document.getElementById('cartClose')?.addEventListener('click', () => this.close());
        this.overlay?.addEventListener('click', () => this.close());
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') this.close(); });
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
    },

    toggle() {
        this.drawer?.classList.contains('open') ? this.close() : this.open();
    },

    async addItem(bookId) {
        const qtyInput = document.getElementById('quantity');
        const quantity = qtyInput ? parseInt(qtyInput.value) : 1;
        try {
            const resp = await fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ book_id: bookId, quantity })
            });
            const data = await resp.json();
            this.updateCartUI(data.cart, 'add');
            this.open();
        } catch(e) { console.error(e); }
    },

    async updateQty(itemId, quantity) {
        if (quantity < 1) return;
        try {
            const resp = await fetch(`/cart/update/${itemId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ quantity })
            });
            const data = await resp.json();
            this.updateCartUI(data.cart, 'update');
        } catch(e) {}
    },

    async removeItem(itemId) {
        try {
            const resp = await fetch(`/cart/remove/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            const data = await resp.json();
            this.updateCartUI(data.cart, 'remove');
        } catch(e) {}
    },

    async loadCart() {
        try {
            const resp = await fetch('/cart/data', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            const data = await resp.json();
            this.updateCartUI(data.cart, 'full');
        } catch(e) {}
    },

    /**
     * Smart update — only rebuilds when necessary.
     * @param {Object} cart - Cart data from server
     * @param {string} action - 'add' | 'update' | 'remove' | 'full'
     */
    updateCartUI(cart, action = 'full') {
        // Update count badges (always)
        document.querySelectorAll('#cartCount').forEach(el => {
            el.textContent = '(' + cart.total_items + ')';
        });

        // Update total (always)
        if (this.totalEl) {
            this.totalEl.textContent = this.formatCurrency(cart.total);
        }

        // Update shipping bar (always)
        this.updateShippingBar(cart.total);

        if (!this.itemsContainer) return;

        // For add/remove/full — rebuild the items list
        if (action === 'add' || action === 'remove' || action === 'full') {
            this.renderItems(cart.items);
        }

        // For update — only update quantities in-place
        if (action === 'update') {
            cart.items.forEach(item => {
                const itemEl = this.itemsContainer.querySelector('.cart-item[data-item-id="' + item.id + '"]');
                if (itemEl) {
                    // Update qty display
                    const qtySpan = itemEl.querySelector('.cart-qty span');
                    if (qtySpan) qtySpan.textContent = item.quantity;

                    // Update data-qty attributes on buttons
                    const downBtn = itemEl.querySelector('.cart-qty-down');
                    const upBtn = itemEl.querySelector('.cart-qty-up');
                    if (downBtn) downBtn.dataset.qty = item.quantity;
                    if (upBtn) upBtn.dataset.qty = item.quantity;

                    // Update item subtotal
                    const priceEl = itemEl.querySelector('.cart-item-price');
                    if (priceEl) {
                        priceEl.textContent = this.formatCurrency(item.price * item.quantity);
                    }
                }
            });
        }
    },

    /**
     * Render all cart items (full rebuild).
     */
    renderItems(items) {
        if (items.length === 0) {
            this.itemsContainer.innerHTML = `
                <div class="cart-empty">
                    <div class="empty-icon"><i class="fas fa-shopping-bag"></i></div>
                    <h4>Your cart is empty</h4>
                    <p>Discover great books and add them here!</p>
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
        const text = document.getElementById('shippingText');
        const progress = document.getElementById('shippingProgress');
        if (!text || !progress) return;
        if (total >= threshold) {
            text.innerHTML = '<i class="fas fa-check-circle"></i> <strong>Congratulations!</strong> You get FREE shipping!';
            progress.style.width = '100%';
        } else {
            const remaining = threshold - total;
            const pct = (total / threshold) * 100;
            text.innerHTML = 'Add <strong>' + this.formatCurrency(remaining) + '</strong> more for free shipping!';
            progress.style.width = pct + '%';
        }
    },

    formatCurrency(amount) {
        return new Intl.NumberFormat('en-MM').format(amount) + ' MMK';
    }
};

document.addEventListener('DOMContentLoaded', () => Cart.init());