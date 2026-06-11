<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bookshop Admin')</title>

    {{-- Styles --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/layout.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @stack('styles')
</head>
<body>

    @auth('staff')
        <div class="admin-layout">

            {{-- Overlay for mobile --}}
            <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

            {{-- Sidebar --}}
            <aside class="admin-sidebar" id="adminSidebar">

                {{-- Toggle button for tablet --}}
                <div class="sidebar-toggle" onclick="document.getElementById('adminSidebar').classList.toggle('open')">
                    <i class="fas fa-bars"></i>
                </div>

                <div class="sidebar-brand">
                    <a href="{{ route('admin.dashboard') }}">
                        <div class="brand-icon">
                            <i class="fas fa-book-open"></i>
                        </div>
                        Book<span>shop</span>
                    </a>
                </div>

                <div class="sidebar-divider">
                    <span>Main Menu</span>
                </div>

                <nav class="sidebar-nav">
                    <ul>
                        <li>
                            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                                <i class="fas fa-th-large"></i> <span>Dashboard</span>
                            </a>
                        </li>

                        @if (Auth::guard('staff')->user()->canManageBooks())
                            <li>
                                <a href="{{ route('admin.banners.index') }}" class="{{ request()->routeIs('admin.banners.*') ? 'active' : '' }}">
                                    <i class="fas fa-image"></i> <span>Banners</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                                    <i class="fas fa-layer-group"></i> <span>Categories</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.authors.index') }}" class="{{ request()->routeIs('admin.authors.*') ? 'active' : '' }}">
                                    <i class="fas fa-feather-alt"></i> <span>Authors</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.books.index') }}" class="{{ request()->routeIs('admin.books.*') ? 'active' : '' }}">
                                    <i class="fas fa-book"></i> <span>Books</span>
                                </a>
                            </li>
                        @endif
                    </ul>

                    @if (Auth::guard('staff')->user()->canManageUsers() || Auth::guard('staff')->user()->canManageOrders())
                        <div class="sidebar-divider" style="margin-top: 16px;">
                            <span>Management</span>
                        </div>
                    @endif

                    <ul>
                        @if (Auth::guard('staff')->user()->canManageUsers())
                            <li>
                                <a href="{{ route('admin.customers.index') }}" class="{{ request()->routeIs('admin.customers.*') ? 'active' : '' }}">
                                    <i class="fas fa-users"></i> <span>Customers</span>
                                </a>
                            </li>
                        @endif

                        @if (Auth::guard('staff')->user()->canManageOrders())
                            <li>
                                <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                                    <i class="fas fa-shopping-bag"></i> <span>Orders</span>
                                </a>
                            </li>
                        @endif

                        @if (Auth::guard('staff')->user()->canViewReports())
                            <li>
                                <a href="{{ route('admin.payments.index') }}" class="{{ request()->routeIs('admin.payments.*') ? 'active' : '' }}">
                                    <i class="fas fa-credit-card"></i> <span>Payments</span>
                                </a>
                            </li>
                        @endif

                        @if (Auth::guard('staff')->user()->canManageUsers())
                            <li>
                                <a href="{{ route('admin.promotions.index') }}" class="{{ request()->routeIs('admin.promotions.*') ? 'active' : '' }}">
                                    <i class="fas fa-bullhorn"></i> <span>Promotions</span>
                                </a>
                            </li>
                        @endif
                    </ul>

                    @if (Auth::guard('staff')->user()->canManageUsers())
                        <div class="sidebar-divider" style="margin-top: 16px;">
                            <span>System</span>
                        </div>

                        <ul>
                            <li>
                                <a href="{{ route('admin.staff.index') }}" class="{{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
                                    <i class="fas fa-user-shield"></i> <span>Staff</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('admin.roles.index') }}" class="{{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                                    <i class="fas fa-key"></i> <span>Roles</span>
                                </a>
                            </li>
                        </ul>
                    @endif
                </nav>

                <div class="sidebar-footer">
                    &copy; {{ date('Y') }} Bookshop v1.0
                </div>
            </aside>

            {{-- Main --}}
            <div class="admin-main">
                <header class="admin-topbar">
                    <div class="topbar-hamburger">
                        <button onclick="openSidebar()">
                            <i class="fas fa-bars"></i>
                        </button>
                    </div>
                    <div class="topbar-left">
                        <h2>@yield('page_title', 'Dashboard')</h2>
                    </div>
                    <div class="topbar-right">
                        <div class="topbar-user">
                            @php
                                $staffUser = Auth::guard('staff')->user();
                                $staffAvatar = ($staffUser->image && $staffUser->image !== 'default.png')
                                    ? asset('storage/' . $staffUser->image)
                                    : null;
                            @endphp

                            @if ($staffAvatar)
                                <img src="{{ $staffAvatar }}" alt="{{ $staffUser->name }}"
                                    style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover;">
                            @else
                                <div class="user-avatar">
                                    {{ strtoupper(substr($staffUser->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="user-info">
                                <div class="user-name">{{ $staffUser->name }}</div>
                                <div class="user-role">{{ $staffUser->role->name ?? 'Staff' }}</div>
                            </div>
                        </div>
                        <form action="{{ route('admin.logout') }}" method="POST" style="margin: 0;">
                            @csrf
                            <button type="submit" class="btn-logout">
                                <i class="fas fa-arrow-right-from-bracket"></i> Logout
                            </button>
                        </form>
                    </div>
                </header>

                <main class="admin-content">
                    @yield('content')
                </main>
            </div>
        </div>
    @else
        @yield('content')
    @endauth

    @stack('scripts')

    {{-- Sidebar Toggle Script --}}
    <script>
        function openSidebar() {
            document.getElementById('adminSidebar').classList.add('open');
            document.getElementById('sidebarOverlay').classList.add('show');
        }
        function closeSidebar() {
            document.getElementById('adminSidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('show');
        }
    </script>
        {{-- AI Assistant --}}
    @include('admin.ai-assistant')

    <link rel="stylesheet" href="{{ asset('css/admin/ai-assistant.css') }}">
    <script src="{{ asset('js/admin/ai-assistant.js') }}"></script>
</body>
</html>