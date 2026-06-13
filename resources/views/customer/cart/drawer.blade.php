{{-- Cart Overlay --}}
<div class="cart-overlay" id="cartOverlay"></div>

{{-- Cart Drawer --}}
<aside class="cart-drawer" id="cartDrawer" aria-label="Shopping cart" aria-hidden="true">

    {{-- Header --}}
    <div class="cart-drawer-header">
        <div class="cart-drawer-header-left">
            <div class="cart-drawer-header-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <path d="M16 10a4 4 0 0 1-8 0"/>
                </svg>
            </div>
            <div>
                <h3 class="cart-drawer-title">Your Cart</h3>
                <span class="cart-drawer-count" id="cartDrawerCount">0 items</span>
            </div>
        </div>
        <button class="cart-drawer-close" id="cartDrawerClose" aria-label="Close cart">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"/>
                <line x1="6" y1="6" x2="18" y2="18"/>
            </svg>
        </button>
    </div>

    {{-- Free Shipping Banner --}}
    <div class="cart-shipping-banner" id="cartShippingBanner">
        <div class="cart-shipping-content">
            <div class="cart-shipping-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="1" y="3" width="15" height="13"/>
                    <polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/>
                    <circle cx="5.5" cy="18.5" r="2.5"/>
                    <circle cx="18.5" cy="18.5" r="2.5"/>
                </svg>
            </div>
            <div class="cart-shipping-text">
                <span id="shippingMessage">Add <strong>50,000 MMK</strong> more for free shipping</span>
                <div class="cart-shipping-bar">
                    <div class="cart-shipping-fill" id="shippingProgressFill" style="width: 0%;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Items Container (JS handles both empty & filled states) --}}
    <div class="cart-drawer-body" id="cartDrawerBody">
        <div class="cart-items-list" id="cartItemsList">
            {{-- Loading skeleton appears here via JS if needed --}}
        </div>
    </div>

    {{-- Footer --}}
    <div class="cart-drawer-footer" id="cartDrawerFooter">
        <div class="cart-footer-inner">
            {{-- Savings --}}
            <div class="cart-footer-savings" id="cartFooterSavings" style="display: none;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/>
                    <line x1="7" y1="7" x2="7.01" y2="7"/>
                </svg>
                <span>You save <strong id="cartSavingsAmount">0 MMK</strong></span>
            </div>

            {{-- Total --}}
            <div class="cart-footer-total">
                <span class="cart-footer-total-label">Total</span>
                <span class="cart-footer-total-amount" id="cartDrawerTotal">0 MMK</span>
            </div>

            {{-- Checkout Button --}}
            <a href="{{ route('checkout.index') }}" class="cart-checkout-btn" id="cartCheckoutBtn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                </svg>
                Secure Checkout
            </a>

            {{-- Continue Shopping --}}
            <button class="cart-continue-btn" id="cartContinueShopping">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="19" y1="12" x2="5" y2="12"/>
                    <polyline points="12 19 5 12 12 5"/>
                </svg>
                Continue Shopping
            </button>
        </div>
    </div>

    {{-- Loading Skeleton --}}
    <div class="cart-loading" id="cartLoading" style="display: none;">
        <div class="cart-skeleton-item"></div>
        <div class="cart-skeleton-item"></div>
        <div class="cart-skeleton-item"></div>
    </div>
</aside>