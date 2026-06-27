<header class="navbar" id="navbar">
    <div class="navbar-inner container">

        {{-- Logo --}}
        <a href="{{ route('customer.home') }}" class="navbar-logo" aria-label="Bookshop Home">
            <div class="navbar-logo-icon"><i class="fas fa-book-open"></i></div>
            <span class="navbar-logo-text">Book<span class="navbar-logo-accent">shop</span></span>
        </a>

        {{-- Desktop Nav --}}
        <nav class="navbar-nav">
            <ul class="navbar-links">
                <li><a href="{{ route('customer.home') }}" class="navbar-link {{ request()->routeIs('customer.home') ? 'navbar-link-active' : '' }}">Home</a></li>
                <li><a href="{{ route('books.index') }}" class="navbar-link {{ request()->routeIs('books.index') && !request('sort') ? 'navbar-link-active' : '' }}">Books</a></li>
                <li><a href="{{ route('books.index', ['sort' => 'latest']) }}" class="navbar-link {{ request('sort') === 'latest' ? 'navbar-link-active' : '' }}">New Arrivals</a></li>
            </ul>
        </nav>

        {{-- Search (Desktop) --}}
        <form action="{{ route('books.index') }}" method="GET" class="navbar-search">
            <div class="navbar-search-wrapper">
                <i class="fas fa-search navbar-search-icon"></i>
                <input type="text" name="search" class="navbar-search-input" placeholder="Search books, authors..." value="{{ request('search') }}">
                @if(request('search'))
                    <a href="{{ route('books.index') }}" class="navbar-search-clear"><i class="fas fa-times"></i></a>
                @endif
            </div>
        </form>

        {{-- Actions --}}
        <div class="navbar-actions">
            @auth('customer')
                @php
                    $currentUser = Auth::guard('customer')->user();
                    $avatarUrl = ($currentUser->image && $currentUser->image !== 'default.png')
                        ? asset('storage/' . $currentUser->image)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($currentUser->name) . '&background=10B981&color=fff&size=60';
                @endphp

                {{-- Search Icon (Mobile only) --}}
                <button class="navbar-icon-btn navbar-search-toggle" id="navbarSearchToggle"><i class="fas fa-search"></i></button>

                {{-- Notifications (Desktop + Mobile) --}}
                <button class="navbar-icon-btn" id="navbarNotificationBtn"><i class="fas fa-bell"></i><span class="navbar-icon-badge" id="notificationBadge" style="display:none;">0</span></button>

                {{-- Wishlist (Desktop only) --}}
                <a href="{{ route('customer.wishlist.index') }}" class="navbar-icon-btn navbar-desktop-only" id="navbarWishlistBtn"><i class="fas fa-heart"></i></a>

                {{-- Cart --}}
                <button class="navbar-cart-btn" id="navbarCartBtn"><i class="fas fa-bag-shopping"></i><span class="navbar-cart-badge" id="cartCount">0</span></button>

                {{-- User (Desktop only) --}}
                <div class="navbar-user navbar-desktop-only" id="navbarUser">
                    <button class="navbar-user-trigger"><img src="{{ $avatarUrl }}" alt="{{ $currentUser->name }}" class="navbar-user-avatar"></button>
                    <div class="navbar-user-panel" id="navbarUserPanel">
                        <div class="navbar-user-header">
                            <img src="{{ $avatarUrl }}" alt="{{ $currentUser->name }}" class="navbar-user-header-avatar">
                            <div><strong>{{ $currentUser->name }}</strong><span>{{ $currentUser->email }}</span></div>
                        </div>
                        <div class="navbar-user-links">
                            <a href="{{ route('customer.profile') }}"><i class="fas fa-user"></i> My Profile</a>
                            <a href="{{ route('customer.orders.index') }}"><i class="fas fa-receipt"></i> My Orders</a>
                            <a href="{{ route('customer.wishlist.index') }}"><i class="fas fa-heart"></i> Wishlist</a>
                            <a href="{{ route('customer.ebooks.library') }}"><i class="fas fa-bookmark"></i> My Library</a>
                        </div>
                        <div class="navbar-user-footer">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="navbar-user-logout"><i class="fas fa-arrow-right-from-bracket"></i> Sign Out</button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- More Menu (Mobile only) --}}
                <button class="navbar-icon-btn navbar-mobile-only" id="navbarMoreBtn"><i class="fas fa-ellipsis"></i></button>

            @else
                <button class="navbar-auth-btn navbar-auth-signin" id="navbarSignInBtn">Sign In</button>
                <button class="navbar-auth-btn navbar-auth-register" id="navbarRegisterBtn">Register</button>
            @endauth
        </div>
    </div>

    {{-- Mobile Search Overlay --}}
    <div class="navbar-search-overlay" id="navbarSearchOverlay">
        <div class="navbar-search-overlay-header">
            <button class="navbar-search-back" id="navbarSearchBack"><i class="fas fa-arrow-left"></i></button>
            <form action="{{ route('books.index') }}" method="GET" class="navbar-search-overlay-form">
                <i class="fas fa-search"></i>
                <input type="text" name="search" class="navbar-search-overlay-input" placeholder="Search books..." autofocus>
            </form>
        </div>
    </div>

    {{-- Notifications Panel --}}
    <div class="navbar-notification-panel" id="notificationPanel">
        <div class="notification-panel-header">
            <strong>Notifications</strong>
            <button onclick="markAllRead()" class="notification-mark-all">Mark all read</button>
        </div>
        <div class="notification-list" id="notificationList">
            <div class="notification-empty"><i class="fas fa-bell-slash"></i><p>No notifications yet</p></div>
        </div>
    </div>

    {{-- Mobile More Menu (Horizontal Grid) --}}
    <div class="mobile-more-overlay" id="mobileMoreOverlay" onclick="toggleMobileMore()"></div>
    <div class="mobile-more-panel" id="mobileMorePanel">
        <div class="mobile-more-header">
            <h3>Menu</h3>
            <button onclick="toggleMobileMore()"><i class="fas fa-xmark"></i></button>
        </div>
        <div class="mobile-more-grid">
            <a href="{{ route('customer.profile') }}" class="mobile-more-item"><i class="fas fa-user"></i><span>Profile</span></a>
            <a href="{{ route('customer.orders.index') }}" class="mobile-more-item"><i class="fas fa-receipt"></i><span>Orders</span></a>
            <a href="{{ route('customer.wishlist.index') }}" class="mobile-more-item"><i class="fas fa-heart"></i><span>Wishlist</span></a>
            <a href="{{ route('customer.ebooks.library') }}" class="mobile-more-item"><i class="fas fa-bookmark"></i><span>Library</span></a>
            <a href="#" class="mobile-more-item" onclick="toggleNotifications()"><i class="fas fa-bell"></i><span>Notifications</span></a>
            <a href="#" class="mobile-more-item" onclick="openLoginModal()"><i class="fas fa-tag"></i><span>Coupons</span></a>
            <a href="mailto:support@bookshop.com" class="mobile-more-item"><i class="fas fa-envelope"></i><span>Contact</span></a>
            <a href="#" class="mobile-more-item"><i class="fas fa-question-circle"></i><span>FAQ</span></a>
        </div>
        <div class="mobile-more-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="mobile-more-logout"><i class="fas fa-sign-out-alt"></i> Sign Out</button>
            </form>
        </div>
    </div>
</header>