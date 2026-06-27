<nav class="bottom-nav" id="bottomNav">
    <a href="{{ route('customer.home') }}" class="bottom-nav-item {{ request()->routeIs('customer.home') ? 'bottom-nav-item-active' : '' }}">
        <i class="fas fa-home"></i><span>Home</span>
    </a>
    <a href="{{ route('books.index') }}" class="bottom-nav-item {{ request()->routeIs('books.index') && !request('sort') ? 'bottom-nav-item-active' : '' }}">
        <i class="fas fa-book"></i><span>Books</span>
    </a>
    <a href="{{ route('books.index') }}" class="bottom-nav-item">
        <i class="fas fa-layer-group"></i><span>Categories</span>
    </a>
    @auth('customer')
        <a href="{{ route('customer.wishlist.index') }}" class="bottom-nav-item {{ request()->routeIs('customer.wishlist.*') ? 'bottom-nav-item-active' : '' }}">
            <i class="fas fa-heart"></i><span>Wishlist</span>
        </a>
    @else
        <button class="bottom-nav-item" onclick="openLoginModal(); return false;">
            <i class="fas fa-heart"></i><span>Wishlist</span>
        </button>
    @endauth
    @auth('customer')
        <a href="{{ route('customer.profile') }}" class="bottom-nav-item {{ request()->routeIs('customer.profile') ? 'bottom-nav-item-active' : '' }}">
            <i class="fas fa-user"></i><span>Profile</span>
        </a>
    @else
        <button class="bottom-nav-item" onclick="openLoginModal(); return false;">
            <i class="fas fa-user"></i><span>Profile</span>
        </button>
    @endauth
</nav>