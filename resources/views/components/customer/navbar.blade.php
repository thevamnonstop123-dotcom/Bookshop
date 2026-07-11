<header class="navbar" id="navbar">
    <div class="navbar-inner container">

        {{-- Logo --}}
        <a href="{{ route('customer.home') }}" class="navbar-logo" aria-label="Bookshop Home">
            <div class="navbar-logo-icon"><i class="fas fa-book-open"></i></div>
            <span class="navbar-logo-text">Book<span class="navbar-logo-accent">shop</span></span>
        </a>

        {{-- Desktop Nav Links --}}
        <nav class="navbar-nav navbar-desktop-only">
            <ul class="navbar-links">
                <li><a href="{{ route('customer.home') }}" class="navbar-link {{ request()->routeIs('customer.home') ? 'navbar-link-active' : '' }}">Home</a></li>
                <li><a href="{{ route('books.index') }}" class="navbar-link {{ request()->routeIs('books.index') && !request('sort') ? 'navbar-link-active' : '' }}">Books</a></li>
                <li><a href="{{ route('authors.index') }}" class="navbar-link {{ request()->routeIs('authors.*') ? 'navbar-link-active' : '' }}">Authors</a></li>
                <li><a href="{{ route('about') }}" class="navbar-link {{ request()->routeIs('about') ? 'navbar-link-active' : '' }}">Contact Us</a></li>
            </ul>
        </nav>

        {{-- Search (Desktop) --}}
        <div class="navbar-search navbar-desktop-only" id="desktopSearchWrap">
            <form action="{{ route('books.index') }}" method="GET" class="navbar-search-wrapper">
                <i class="fas fa-search navbar-search-icon"></i>
                <input type="text" name="search" class="navbar-search-input" 
                       placeholder="Search books, authors..." 
                       value="{{ request('search') }}"
                       id="desktopSearchInput"
                       autocomplete="off">
                @if(request('search'))
                    <a href="{{ route('books.index') }}" class="navbar-search-clear"><i class="fas fa-times"></i></a>
                @endif
            </form>

            {{-- Desktop search dropdown --}}
            <div class="navbar-search-dropdown" id="desktopSearchDropdown" style="display:none;">
                @php $recentSearches = session('recent_searches', []); @endphp
                @if(!empty($recentSearches))
                    <div class="navbar-search-dropdown-section">
                        <div class="navbar-search-dropdown-header">
                            <span class="navbar-search-dropdown-title">Recent</span>
                            <button type="button" class="navbar-search-clear-all" onclick="clearSearchHistory()">Clear All</button>
                        </div>
                        @foreach($recentSearches as $index => $term)
                            <div class="navbar-search-suggestion-item-wrap">
                                <a href="{{ route('books.index', ['search' => $term]) }}" class="navbar-search-dropdown-item">
                                    <i class="fas fa-clock-rotate-left"></i> {{ $term }}
                                </a>
                                <button type="button" class="navbar-search-suggestion-delete" onclick="deleteSearchHistory({{ $index }})" aria-label="Remove {{ $term }}">
                                    <i class="fas fa-xmark"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
                <div class="navbar-search-dropdown-section">
                    <span class="navbar-search-dropdown-title">Popular</span>
                    <a href="{{ route('books.index', ['search' => 'Programming']) }}" class="navbar-search-dropdown-item">
                        <i class="fas fa-fire"></i> Programming
                    </a>
                    <a href="{{ route('books.index', ['search' => 'Psychology']) }}" class="navbar-search-dropdown-item">
                        <i class="fas fa-fire"></i> Psychology
                    </a>
                    <a href="{{ route('books.index', ['search' => 'Fiction']) }}" class="navbar-search-dropdown-item">
                        <i class="fas fa-fire"></i> Fiction
                    </a>
                </div>
            </div>
        </div>

        {{-- Desktop Actions --}}
        <div class="navbar-actions navbar-desktop-only">
            @auth('customer')
                @php
                    $currentUser = Auth::guard('customer')->user();
                    $avatarUrl = ($currentUser->image && $currentUser->image !== 'default.png')
                        ? asset('storage/' . $currentUser->image)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($currentUser->name) . '&background=1E3A8A&color=fff&size=60';
                @endphp

                <button class="navbar-icon-btn" id="navbarNotificationBtn" aria-label="Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="navbar-icon-badge" id="notificationBadge" style="display:none;">0</span>
                </button>
                <button class="navbar-cart-btn" id="navbarCartBtn" aria-label="Cart">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="navbar-cart-badge" id="cartCount" style="display:none;">0</span>
                </button>

                {{-- User Dropdown --}}
                <div class="navbar-user" id="navbarUser">
                    <button class="navbar-user-trigger">
                        <img src="{{ $avatarUrl }}" alt="{{ $currentUser->name }}" class="navbar-user-avatar">
                    </button>
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
                                <button type="submit" class="navbar-user-logout">
                                    <i class="fas fa-arrow-right-from-bracket"></i> Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @else
                <button class="navbar-auth-btn navbar-auth-signin" id="navbarSignInBtn">Sign In</button>
                <button class="navbar-auth-btn navbar-auth-register" id="navbarRegisterBtn">Register</button>
            @endauth
        </div>

        {{-- MOBILE TOP NAV ACTIONS --}}
        <div class="navbar-mobile-actions" id="navbarMobileActions">
            <button class="navbar-icon-btn" onclick="openMobileSearch()" aria-label="Search">
                <i class="fas fa-search"></i>
            </button>
            @auth('customer')
                @php
                    $mobileUser = Auth::guard('customer')->user();
                    $mobileAvatarUrl = ($mobileUser->image && $mobileUser->image !== 'default.png')
                        ? asset('storage/' . $mobileUser->image)
                        : 'https://ui-avatars.com/api/?name=' . urlencode($mobileUser->name) . '&background=1E3A8A&color=fff&size=60';
                @endphp
                <button class="navbar-icon-btn" id="mobileNotificationBtn" aria-label="Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="navbar-icon-badge" id="mobileNotificationBadge" style="display:none;">0</span>
                </button>
                <a href="{{ route('customer.wishlist.index') }}" class="navbar-icon-btn" aria-label="Wishlist">
                    <i class="fas fa-heart"></i>
                </a>
                <a href="{{ route('customer.profile') }}" class="navbar-icon-btn navbar-mobile-avatar-btn" aria-label="Profile">
                    <img src="{{ $mobileAvatarUrl }}" alt="{{ $mobileUser->name }}" class="navbar-user-avatar" style="width:28px;height:28px;border-radius:50%;object-fit:cover;">
                </a>
            @else
                <button class="navbar-auth-btn navbar-auth-signin" onclick="openLoginModal()">Sign In</button>
            @endauth
        </div>
    </div>

    {{-- Mobile Search Overlay --}}
    <div class="navbar-search-overlay" id="navbarSearchOverlay">
        <div class="navbar-search-overlay-header">
            <button class="navbar-search-back" onclick="closeMobileSearch()" aria-label="Back">
                <i class="fas fa-arrow-left"></i>
            </button>
            <form action="{{ route('books.index') }}" method="GET" class="navbar-search-overlay-form" id="mobileSearchForm">
                <i class="fas fa-search"></i>
                <input type="text" name="search" class="navbar-search-overlay-input" 
                    id="mobileSearchInput"
                    placeholder="Search books, authors, categories..." 
                    autocomplete="off">
            </form>
        </div>
        <div class="navbar-search-suggestions">
            @php $recentSearches = session('recent_searches', []); @endphp
            @if(!empty($recentSearches))
                <div class="navbar-search-suggestions-section">
                    <div class="navbar-search-suggestions-header">
                        <h4 class="navbar-search-suggestions-title">Recent Searches</h4>
                        <button type="button" class="navbar-search-clear-all" onclick="clearSearchHistory()">Clear All</button>
                    </div>
                    @foreach($recentSearches as $index => $term)
                        <div class="navbar-search-suggestion-item-wrap">
                            <a href="{{ route('books.index', ['search' => $term]) }}" class="navbar-search-suggestion-item">
                                <i class="fas fa-clock-rotate-left"></i> {{ $term }}
                            </a>
                            <button type="button" class="navbar-search-suggestion-delete" onclick="deleteSearchHistory({{ $index }})" aria-label="Remove {{ $term }}">
                                <i class="fas fa-xmark"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif
            <div class="navbar-search-suggestions-section">
                <h4 class="navbar-search-suggestions-title">Popular</h4>
                <a href="{{ route('books.index', ['search' => 'Psychology']) }}" class="navbar-search-suggestion-item">
                    <i class="fas fa-fire"></i> Psychology
                </a>
                <a href="{{ route('books.index', ['search' => 'Laravel']) }}" class="navbar-search-suggestion-item">
                    <i class="fas fa-fire"></i> Laravel
                </a>
                <a href="{{ route('books.index', ['search' => 'Fiction']) }}" class="navbar-search-suggestion-item">
                    <i class="fas fa-fire"></i> Fiction
                </a>
                <a href="{{ route('books.index', ['search' => 'Programming']) }}" class="navbar-search-suggestion-item">
                    <i class="fas fa-fire"></i> Programming
                </a>
            </div>
        </div>
    </div>

    {{-- Notifications Panel --}}
    <div class="navbar-notification-panel" id="notificationPanel">
        <div class="notification-panel-header">
            <strong>Notifications</strong>
            <button onclick="markAllRead()" class="notification-mark-all">Mark all read</button>
        </div>
        <div class="notification-list" id="notificationList">
            <div class="notification-empty">
                <i class="fas fa-bell-slash"></i>
                <p>No notifications yet</p>
            </div>
        </div>
    </div>
</header>