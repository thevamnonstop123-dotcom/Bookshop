<header class="admin-topbar mod-segmented">
    <div class="admin-topbar-left">
        <button class="admin-topbar-hamburger" id="mobileHamburger" aria-label="Open menu">
            <i class="fas fa-bars"></i>
        </button>
        <h1 class="admin-topbar-title">@yield('page_title', 'Dashboard')</h1>
    </div>

    <div class="admin-topbar-right">
        {{-- Enclosed Profile Card Segment --}}
        <div class="admin-topbar-profile-card">
            @php
                $staffUser = Auth::guard('staff')->user();
                $staffAvatar = ($staffUser->image && $staffUser->image !== 'default.png')
                    ? asset('storage/' . $staffUser->image)
                    : null;
            @endphp

            @if ($staffAvatar)
                <img src="{{ $staffAvatar }}" alt="{{ $staffUser->name }}" class="admin-topbar-card-avatar-img">
            @else
                <div class="admin-topbar-card-avatar">
                    {{ strtoupper(substr($staffUser->name, 0, 1)) }}
                </div>
            @endif

            <div class="admin-topbar-card-meta">
                <span class="admin-topbar-card-title">{{ $staffUser->name }}</span>
                <span class="admin-topbar-card-badge">{{ $staffUser->role->name ?? 'Staff' }}</span>
            </div>
        </div>

        {{-- Notification Bell --}}
        <button class="admin-topbar-action-solid" id="adminNotificationBell" onclick="toggleAdminNotifications()" style="position:relative" aria-label="Notifications">
            <i class="fas fa-bell"></i>
            <span class="admin-notification-badge" id="adminNotificationBadge" style="display:none">0</span>
        </button>
        {{-- Solid High-Action Logout Button --}}
        <form action="{{ route('admin.logout') }}" method="POST" class="admin-topbar-logout-form" data-tooltip="Sign Out">
            @csrf
            <button type="submit" class="admin-topbar-action-solid" aria-label="Sign Out">
                <i class="fas fa-power-off"></i>
            </button>
        </form>
    </div>
</header>