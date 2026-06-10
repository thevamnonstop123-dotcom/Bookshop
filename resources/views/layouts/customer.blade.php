<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bookshop')</title>

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/cart.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @stack('styles')
</head>
<body>

    {{-- Navbar --}}
    <nav class="customer-navbar">
        <div class="container">
            <a href="{{ route('customer.home') }}" class="navbar-brand">
                <div class="brand-icon"><i class="fas fa-book-open"></i></div>
                Book<span>shop</span>
            </a>

           <ul class="navbar-links">
                <li><a href="{{ route('customer.home') }}" class="{{ request()->routeIs('customer.home') ? 'active' : '' }}">Home</a></li>
                <li><a href="{{ route('books.index') }}" class="{{ request()->routeIs('books.index') && !request('sort') ? 'active' : '' }}">Books</a></li>
                <li><a href="{{ route('books.index', ['sort' => 'latest']) }}" class="{{ request('sort') === 'latest' ? 'active' : '' }}">New Arrivals</a></li>
            </ul>

            <form action="{{ route('books.index') }}" method="GET" class="navbar-search">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search for books, authors..." value="{{ request('search') }}">
            </form>

            <div class="navbar-actions">
                @auth('customer')
                    <div class="navbar-user-menu">
                        <a href="{{ route('customer.profile') }}" class="navbar-user-avatar" title="My Profile">
                            @php
                                $currentUser = Auth::guard('customer')->user();
                                $avatarUrl = ($currentUser->image && $currentUser->image !== 'default.png')
                                    ? asset('storage/' . $currentUser->image)
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($currentUser->name) . '&background=f59e0b&color=fff&size=60';
                            @endphp
                            <img src="{{ $avatarUrl }}" alt="{{ $currentUser->name }}">
                        </a>
                        <div class="navbar-user-dropdown">
                            <div class="dropdown-header">
                                <strong>{{ $currentUser->name }}</strong>
                                <span>{{ $currentUser->email }}</span>
                            </div>
                            <a href="{{ route('customer.profile') }}"><i class="fas fa-user-edit"></i> My Profile</a>
                            <a href="{{ route('customer.orders.index') }}"><i class="fas fa-shopping-bag"></i> My Orders</a>
                            <a href="{{ route('customer.ebooks.library') }}"><i class="fas fa-tablet-alt"></i> My Library</a>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit"><i class="fas fa-sign-out-alt"></i> Logout</button>
                            </form>
                        </div>
                    </div>

                    <a href="#" class="cart-link" title="Cart" onclick="Cart.toggle(); return false;">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="cart-badge" id="cartCount">0</span>
                    </a>
                @else
                    <a href="#" class="nav-link" onclick="openLoginModal(); return false;">Sign In</a>
                    <a href="#" class="btn btn-accent btn-register" onclick="openLoginModal(); setTimeout(() => switchToRegister(new Event('click')), 200); return false;">Register</a>
                @endauth

                <button class="mobile-toggle" onclick="document.querySelector('.navbar-links').classList.toggle('show')">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>

    {{-- Content --}}
    @yield('content')

    {{-- Footer --}}
    <footer class="customer-footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-col">
                    <h4>Bookshop</h4>
                    <p>Your premium online bookstore. Quality books, fast delivery, trusted service.</p>
                </div>
                <div class="footer-col">
                    <h4>Categories</h4>
                    @foreach (\App\Models\Category::where('status', 'active')->limit(5)->get() as $cat)
                        <a href="#">{{ $cat->name }}</a>
                    @endforeach
                </div>
                <div class="footer-col">
                    <h4>Quick Links</h4>
                    <a href="#">About Us</a>
                    <a href="#">Contact</a>
                    <a href="#">Privacy Policy</a>
                </div>
                <div class="footer-col">
                    <h4>Follow Us</h4>
                    <div style="display: flex; gap: 12px; font-size: 18px;">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                &copy; {{ date('Y') }} Bookshop. All rights reserved.
            </div>
        </div>
    </footer>

    {{-- Cart Drawer --}}
    @include('customer.cart.drawer')

    {{-- Login Modal --}}
    @include('customer.auth.login-modal')

    {{-- Scripts --}}
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/customer/cart.js') }}"></script>
    @stack('scripts')
</body>
</html>