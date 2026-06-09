{{-- Cart Overlay --}}
<div class="cart-overlay" id="cartOverlay"></div>

{{-- Cart Drawer --}}
<div class="cart-drawer" id="cartDrawer">

    {{-- Header --}}
    <div class="cart-header">
        <div class="cart-header-left">
            <div class="cart-header-icon">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <h3>Your Cart <span id="cartCount">(0)</span></h3>
        </div>
        <button class="cart-close" id="cartClose">
            <i class="fas fa-times"></i>
        </button>
    </div>

    {{-- Free Shipping Bar --}}
    <div class="cart-shipping-bar" id="shippingBar">
        <i class="fas fa-truck"></i>
        <span id="shippingText">Add <strong>50,000 MMK</strong> more for free shipping!</span>
        <div class="progress-bar">
            <div class="progress-fill" id="shippingProgress" style="width: 0%;"></div>
        </div>
    </div>

    {{-- Items --}}
    <div class="cart-items" id="cartItems">
        <div class="cart-empty">
            <div class="empty-icon">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <h4>Your cart is empty</h4>
            <p>Discover great books and add them here!</p>
        </div>
    </div>

    {{-- Footer --}}
    <div class="cart-footer">
        <div class="cart-footer-inner">
            <div class="cart-total-row">
                <span>Total</span>
                <span class="cart-total-amount" id="cartTotal">0 MMK</span>
            </div>
            <div class="cart-savings" id="cartSavings" style="display: none;"></div>
            <a href="{{ route('checkout.index') }}" class="btn btn-accent">
                <i class="fas fa-lock"></i> Secure Checkout
            </a>
        </div>
    </div>

</div>