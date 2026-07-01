<aside class="admin-sidebar" id="adminSidebar" aria-label="Main navigation">

    {{-- Collapse Toggle (Tablet) --}}
    <button class="admin-sidebar-toggle" id="sidebarCollapseToggle" aria-label="Toggle sidebar">
        <i class="fas fa-chevron-left"></i>
    </button>

    {{-- Brand --}}
    <div class="admin-sidebar-brand">
        <a href="{{ route('admin.dashboard') }}">
            <div class="admin-sidebar-brand-icon">
                <i class="fas fa-shield-halved"></i>
            </div>
            <span class="admin-sidebar-brand-text">Book<span class="admin-sidebar-brand-accent">shop</span></span>
        </a>
    </div>

    {{-- Navigation --}}
    <nav class="admin-sidebar-nav">

        {{-- Main Menu --}}
        <div class="admin-sidebar-section-label">Main Menu</div>
        <ul class="admin-sidebar-menu">
            <li>
                <a href="{{ route('admin.dashboard') }}" class="admin-sidebar-link {{ request()->routeIs('admin.dashboard') ? 'admin-sidebar-link-active' : '' }}">
                    <i class="fas fa-th-large"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            @if (Auth::guard('staff')->user()->canManageBooks())
                <li>
                    <a href="{{ route('admin.banners.index') }}" class="admin-sidebar-link {{ request()->routeIs('admin.banners.*') ? 'admin-sidebar-link-active' : '' }}">
                        <i class="fas fa-image"></i>
                        <span>Banners</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.categories.index') }}" class="admin-sidebar-link {{ request()->routeIs('admin.categories.*') ? 'admin-sidebar-link-active' : '' }}">
                        <i class="fas fa-layer-group"></i>
                        <span>Categories</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.authors.index') }}" class="admin-sidebar-link {{ request()->routeIs('admin.authors.*') ? 'admin-sidebar-link-active' : '' }}">
                        <i class="fas fa-feather"></i>
                        <span>Authors</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.genres.index') }}" class="admin-sidebar-link {{ request()->routeIs('admin.genres.*') ? 'admin-sidebar-link-active' : '' }}">
                        <i class="fas fa-tags"></i>
                        <span>Genres</span>
                    </a>
                </li>

                <li>
                    <a href="{{ route('admin.countries.index') }}" class="admin-sidebar-link {{ request()->routeIs('admin.countries.*') ? 'admin-sidebar-link-active' : '' }}">
                        <i class="fas fa-globe-asia"></i>
                        <span>Countries</span>
                    </a>
                </li>
                
                <li>
                    <a href="{{ route('admin.books.index') }}" class="admin-sidebar-link {{ request()->routeIs('admin.books.*') ? 'admin-sidebar-link-active' : '' }}">
                        <i class="fas fa-book-open"></i>
                        <span>Books</span>
                    </a>
                </li>
            @endif
        </ul>

        {{-- Management --}}
        @if (Auth::guard('staff')->user()->canManageUsers() || Auth::guard('staff')->user()->canManageOrders())
            @php
                $managementOpen = request()->routeIs('admin.customers.*') 
                    || request()->routeIs('admin.orders.*') 
                    || request()->routeIs('admin.payments.*') 
                    || request()->routeIs('admin.promotions.*')
                    || request()->routeIs('admin.reviews.*');
            @endphp

            <div class="admin-sidebar-section-label">Management</div>

            <ul class="admin-sidebar-menu">
                <li class="admin-sidebar-parent">
                    <button class="admin-sidebar-link admin-sidebar-parent-toggle" aria-expanded="{{ $managementOpen ? 'true' : 'false' }}">
                        <i class="fas fa-briefcase"></i>
                        <span>Management</span>
                        <i class="fas fa-chevron-down admin-sidebar-dropdown-icon {{ $managementOpen ? 'open' : '' }}"></i>
                    </button>

                    <ul class="admin-sidebar-submenu {{ $managementOpen ? 'open' : '' }}">
                        @if (Auth::guard('staff')->user()->canManageUsers())
                            <li>
                                <a href="{{ route('admin.customers.index') }}" class="admin-sidebar-link {{ request()->routeIs('admin.customers.*') ? 'admin-sidebar-link-active' : '' }}">
                                    <i class="fas fa-users"></i>
                                    <span>Customers</span>
                                </a>
                            </li>
                        @endif

                        @if (Auth::guard('staff')->user()->canManageOrders())
                            <li>
                                <a href="{{ route('admin.orders.index') }}" class="admin-sidebar-link {{ request()->routeIs('admin.orders.*') ? 'admin-sidebar-link-active' : '' }}">
                                    <i class="fas fa-receipt"></i>
                                    <span>Orders</span>
                                </a>
                            </li>
                        @endif

                        @if (Auth::guard('staff')->user()->canViewReports())
                            <li>
                                <a href="{{ route('admin.payments.index') }}" class="admin-sidebar-link {{ request()->routeIs('admin.payments.*') ? 'admin-sidebar-link-active' : '' }}">
                                    <i class="fas fa-credit-card"></i>
                                    <span>Payments</span>
                                </a>
                            </li>
                        @endif

                        @if (Auth::guard('staff')->user()->canManageUsers())
                            <li>
                                <a href="{{ route('admin.promotions.index') }}" class="admin-sidebar-link {{ request()->routeIs('admin.promotions.*') ? 'admin-sidebar-link-active' : '' }}">
                                    <i class="fas fa-bullhorn"></i>
                                    <span>Promotions</span>
                                </a>
                            </li>
                        @endif

                        <li>
                            <a href="{{ route('admin.reviews.index') }}" class="admin-sidebar-link {{ request()->routeIs('admin.reviews.*') ? 'admin-sidebar-link-active' : '' }}">
                                <i class="fas fa-star"></i>
                                <span>Reviews</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        @endif

        {{-- System --}}
        @if (Auth::guard('staff')->user()->canManageUsers())
            @php
                $systemOpen = request()->routeIs('admin.staff.*') || request()->routeIs('admin.roles.*');
            @endphp

            <div class="admin-sidebar-section-label">System</div>

            <ul class="admin-sidebar-menu">
                <li class="admin-sidebar-parent">
                    <button class="admin-sidebar-link admin-sidebar-parent-toggle" aria-expanded="{{ $systemOpen ? 'true' : 'false' }}">
                        <i class="fas fa-cogs"></i>
                        <span>System</span>
                        <i class="fas fa-chevron-down admin-sidebar-dropdown-icon {{ $systemOpen ? 'open' : '' }}"></i>
                    </button>

                    <ul class="admin-sidebar-submenu {{ $systemOpen ? 'open' : '' }}">
                        <li>
                            <a href="{{ route('admin.staff.index') }}" class="admin-sidebar-link {{ request()->routeIs('admin.staff.*') ? 'admin-sidebar-link-active' : '' }}">
                                <i class="fas fa-user-shield"></i>
                                <span>Staff</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('admin.roles.index') }}" class="admin-sidebar-link {{ request()->routeIs('admin.roles.*') ? 'admin-sidebar-link-active' : '' }}">
                                <i class="fas fa-key"></i>
                                <span>Roles</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        @endif

    </nav>

    {{-- Footer --}}
    <div class="admin-sidebar-footer">
        <i class="fas fa-circle admin-footer-status-dot"></i>
        &copy; {{ date('Y') }} Bookshop v1.0
    </div>
</aside>