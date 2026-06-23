<header class="navbar" id="navbar">
    <div class="navbar-inner container">

        {{-- Logo --}}
        <a href="{{ route('customer.home') }}" class="navbar-logo" aria-label="Bookshop Home">
            <div class="navbar-logo-icon">
                <i class="fas fa-book-open"></i>
            </div>
            <span class="navbar-logo-text">
                Book<span class="navbar-logo-accent">shop</span>
            </span>
        </a>

        {{-- Desktop Navigation Links --}}
        <nav class="navbar-nav" id="navbarNav">
            <ul class="navbar-links">
                <li>
                    <a href="{{ route('customer.home') }}" class="navbar-link {{ request()->routeIs('customer.home') ? 'navbar-link-active' : '' }}">
                        Home
                    </a>
                </li>
                <li>
                    <a href="{{ route('books.index') }}" class="navbar-link {{ request()->routeIs('books.index') && !request('sort') ? 'navbar-link-active' : '' }}">
                        Books
                    </a>
                </li>
                <li>
                    <a href="{{ route('books.index', ['sort' => 'latest']) }}" class="navbar-link {{ request('sort') === 'latest' ? 'navbar-link-active' : '' }}">
                        New Arrivals
                    </a>
                </li>
            </ul>
        </nav>

        {{-- Search --}}
        <form action="{{ route('books.index') }}" method="GET" class="navbar-search" id="navbarSearch">
            <div class="navbar-search-wrapper">
                <i class="fas fa-search navbar-search-icon"></i>
                <input
                    type="text"
                    name="search"
                    class="navbar-search-input"
                    placeholder="Search books, authors..."
                    value="{{ request('search') }}"
                    autocomplete="off"
                >
                @if(request('search'))
                    <a href="{{ route('books.index') }}" class="navbar-search-clear" aria-label="Clear search">
                        <i class="fas fa-times"></i>
                    </a>
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

                {{-- User Menu --}}
                <div class="navbar-user" id="navbarUser">
                    <button class="navbar-user-trigger" id="navbarUserTrigger">
                        <img src="{{ $avatarUrl }}" alt="{{ $currentUser->name }}" class="navbar-user-avatar">
                        <i class="fas fa-chevron-down navbar-user-chevron"></i>
                    </button>
                    <div class="navbar-user-panel" id="navbarUserPanel">
                        <div class="navbar-user-header">
                            <img src="{{ $avatarUrl }}" alt="{{ $currentUser->name }}" class="navbar-user-header-avatar">
                            <div>
                                <strong>{{ $currentUser->name }}</strong>
                                <span>{{ $currentUser->email }}</span>
                            </div>
                        </div>
                        <div class="navbar-user-links">
                            <a href="{{ route('customer.profile') }}"><i class="fas fa-user"></i> My Profile</a>
                            <a href="{{ route('customer.orders.index') }}"><i class="fas fa-receipt"></i> My Orders</a>
                            <a href="{{ route('customer.ebooks.library') }}"><i class="fas fa-bookmark"></i> My Library</a>
                        </div>
                        <div class="navbar-user-footer">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="navbar-user-logout">
                                    <i class="fas fa-arrow-right-from-bracket"></i> Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                {{-- Cart --}}
                <button class="navbar-cart-btn" id="navbarCartBtn" aria-label="Open cart">
                    <i class="fas fa-bag-shopping"></i>
                    <span class="navbar-cart-badge" id="cartCount">0</span>
                </button>

            @else
                <button class="navbar-auth-btn navbar-auth-signin" id="navbarSignInBtn">Sign In</button>
                <button class="navbar-auth-btn navbar-auth-register" id="navbarRegisterBtn">Register</button>
            @endauth

            {{-- Mobile Toggle (Hamburger) --}}
            <button class="navbar-mobile-toggle" id="navbarMobileToggle" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>
    </div>

    {{-- Mobile Overlay --}}
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
</header>