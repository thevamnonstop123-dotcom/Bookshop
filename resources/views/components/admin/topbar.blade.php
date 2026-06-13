<header class="admin-topbar">
    <div class="admin-topbar-left">
        {{-- Mobile Hamburger --}}
        <button class="admin-topbar-hamburger" id="mobileHamburger" aria-label="Open menu">
            <i class="fas fa-bars"></i>
        </button>

        {{-- Page Title --}}
        <h1 class="admin-topbar-title">@yield('page_title', 'Dashboard')</h1>
    </div>

    <div class="admin-topbar-right">
        {{-- User Info --}}
        <div class="admin-topbar-user">
            @php
                $staffUser = Auth::guard('staff')->user();
                $staffAvatar = ($staffUser->image && $staffUser->image !== 'default.png')
                    ? asset('storage/' . $staffUser->image)
                    : null;
            @endphp

            @if ($staffAvatar)
                <img src="{{ $staffAvatar }}" alt="{{ $staffUser->name }}" class="admin-topbar-user-avatar-img">
            @else
                <div class="admin-topbar-user-avatar">
                    {{ strtoupper(substr($staffUser->name, 0, 1)) }}
                </div>
            @endif

            <div class="admin-topbar-user-info">
                <span class="admin-topbar-user-name">{{ $staffUser->name }}</span>
                <span class="admin-topbar-user-role">{{ $staffUser->role->name ?? 'Staff' }}</span>
            </div>
        </div>

        {{-- Logout --}}
        <form action="{{ route('admin.logout') }}" method="POST" class="admin-topbar-logout-form">
            @csrf
            <button type="submit" class="admin-topbar-logout-btn">
                <i class="fas fa-arrow-right-from-bracket"></i>
                <span>Logout</span>
            </button>
        </form>
    </div>
</header>