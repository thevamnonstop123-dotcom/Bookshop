<nav class="bottom-nav" id="bottomNav">
    {{-- Home --}}
    <a href="{{ route('customer.home') }}" class="bottom-nav-item {{ request()->routeIs('customer.home') ? 'active' : '' }}">
        <i class="fas fa-home"></i><span>Home</span>
    </a>

    {{-- Books --}}
    <a href="{{ route('books.index') }}" class="bottom-nav-item {{ request()->routeIs('books.index') && !request('category') && !request('author') ? 'active' : '' }}">
        <i class="fas fa-book"></i><span>Books</span>
    </a>

    {{-- More --}}
    <button class="bottom-nav-item" id="morePanelToggle" onclick="toggleMorePanel()" aria-label="More">
        <i class="fas fa-sliders-h" aria-hidden="true"></i><span>More</span>
    </button>

    {{-- Cart with count --}}
    <button class="bottom-nav-item" id="mobileCartBtn" onclick="openCartDrawer()">
        <i class="fas fa-shopping-cart"></i>
        <span>Cart</span>
        <span class="navbar-cart-badge" id="mobileCartCount" style="display: {{ isset($cartCount) && $cartCount > 0 ? 'flex' : 'none' }}">{{ $cartCount ?? 0 }}</span>
    </button>

    {{-- Profile --}}
    @auth('customer')
        <a href="{{ route('customer.profile') }}" class="bottom-nav-item {{ request()->routeIs('customer.profile') ? 'active' : '' }}">
            <i class="fas fa-user"></i><span>Profile</span>
        </a>
    @else
        <button class="bottom-nav-item" onclick="openLoginModal()">
            <i class="fas fa-user"></i><span>Profile</span>
        </button>
    @endauth
</nav>

{{-- More Panel --}}
<div class="mobile-more-panel" id="morePanel">
    <div class="mobile-more-handle"></div>
    <div class="mobile-more-grid">
        <a href="{{ route('books.index') }}" class="mobile-more-item">
            <i class="fas fa-layer-group"></i>
            <span>Categories</span>
        </a>
        <a href="{{ route('authors.index') }}" class="mobile-more-item">
            <i class="fas fa-user-pen"></i>
            <span>Authors</span>
        </a>
        <a href="{{ route('books.index', ['sort' => 'bestseller']) }}" class="mobile-more-item">
            <i class="fas fa-fire"></i>
            <span>Best Sellers</span>
        </a>
        <a href="{{ route('about') }}" class="mobile-more-item">
            <i class="fas fa-envelope"></i>
            <span>Contact Us</span>
        </a>
        @auth('customer')
            <a href="{{ route('customer.orders.index') }}" class="mobile-more-item">
                <i class="fas fa-box"></i>
                <span>Orders</span>
            </a>
            <a href="{{ route('customer.wishlist.index') }}" class="mobile-more-item">
                <i class="fas fa-heart"></i>
                <span>Wishlist</span>
            </a>
            <a href="{{ route('customer.ebooks.library') }}" class="mobile-more-item">
                <i class="fas fa-tablet-screen-button"></i>
                <span>My Library</span>
            </a>
            <a href="{{ route('customer.profile') }}" class="mobile-more-item">
                <i class="fas fa-gear"></i>
                <span>Settings</span>
            </a>
        @endauth
        <a href="#" class="mobile-more-item">
            <i class="fas fa-headset"></i>
            <span>Support</span>
        </a>
    </div>
    @auth('customer')
        <form action="{{ route('logout') }}" method="POST" class="mobile-more-logout-form">
            @csrf
            <button type="submit" class="mobile-more-logout">
                <i class="fas fa-right-from-bracket"></i> Logout
            </button>
        </form>
    @endauth
</div>

{{-- More Panel Overlay --}}
<div class="mobile-more-overlay" id="morePanelOverlay" onclick="toggleMorePanel()"></div>