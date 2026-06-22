<nav class="bottom-nav" id="bottomNav">
    <a href="{{ route('customer.home') }}" class="bottom-nav-item {{ request()->routeIs('customer.home') ? 'bottom-nav-item-active' : '' }}">
        <i class="fas fa-home"></i>
        <span>Home</span>
    </a>
    <a href="{{ route('books.index') }}" class="bottom-nav-item {{ request()->routeIs('books.index') ? 'bottom-nav-item-active' : '' }}">
        <i class="fas fa-layer-group"></i>
        <span>Categories</span>
    </a>
    @auth('customer')
    <a href="{{ route('customer.wishlist.index') }}" class="bottom-nav-item {{ request()->routeIs('customer.wishlist.*') ? 'bottom-nav-item-active' : '' }}">
        <i class="fas fa-heart"></i>
        <span>Wishlist</span>
    </a>
    @else
        <a href="#" class="bottom-nav-item" onclick="openLoginModal(); return false;">
            <i class="fas fa-heart"></i>
            <span>Wishlist</span>
        </a>
    @endauth
    <a href="{{ route('customer.orders.index') }}" class="bottom-nav-item {{ request()->routeIs('customer.orders.*') ? 'bottom-nav-item-active' : '' }}">
        <i class="fas fa-receipt"></i>
        <span>Orders</span>
    </a>
    <button class="bottom-nav-item" id="moreMenuBtn">
        <i class="fas fa-ellipsis"></i>
        <span>More</span>
    </button>
</nav>

{{-- More Menu Overlay --}}
<div class="more-menu-overlay" id="moreMenuOverlay" onclick="toggleMoreMenu()"></div>
<div class="more-menu" id="moreMenu">
    <div class="more-menu-header">
        <h3>Menu</h3>
        <button onclick="toggleMoreMenu()"><i class="fas fa-xmark"></i></button>
    </div>
<div class="more-menu-links">
    <a href="{{ route('customer.profile') }}"><i class="fas fa-user"></i> Profile</a>
    <a href="{{ route('customer.orders.index') }}"><i class="fas fa-receipt"></i> My Orders</a>
    @auth('customer')
        <a href="{{ route('customer.wishlist.index') }}"><i class="fas fa-heart"></i> Wishlist</a>
    @else
        <a href="#" onclick="openLoginModal(); return false;"><i class="fas fa-heart"></i> Wishlist</a>
    @endauth
    <a href="{{ route('customer.profile') }}#addresses"><i class="fas fa-map-marker-alt"></i> Addresses</a>
    <a href="{{ route('books.index') }}"><i class="fas fa-tag"></i> Browse Books</a>
    <a href="#" onclick="toggleNotifications()"><i class="fas fa-bell"></i> Notifications</a>
    <a href="{{ route('customer.ebooks.library') }}"><i class="fas fa-tablet-alt"></i> My Library</a>
    <a href="mailto:support@bookshop.com"><i class="fas fa-envelope"></i> Contact Us</a>
    <a href="#"><i class="fas fa-question-circle"></i> FAQ</a>
    <a href="#"><i class="fas fa-info-circle"></i> About Us</a>
    <a href="#"><i class="fas fa-shield-alt"></i> Privacy Policy</a>
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit"><i class="fas fa-sign-out-alt"></i> Logout</button>
    </form>
</div>
</div>

