<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bookshop Admin')</title>

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    {{-- Design System & Layout --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/ai-assistant.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pagination.css') }}">

    @stack('styles')
</head>
<body>
    <a href="#main-content" class="skip-to-main">Skip to main content</a>

    @auth('staff')
        <div class="admin-layout">

            {{-- Mobile Overlay --}}
            <div class="admin-sidebar-overlay" id="sidebarOverlay"></div>

            {{-- Sidebar --}}
            @include('components.admin.sidebar')

            {{-- Main Area --}}
            <div class="admin-main" id="main-content">

                {{-- Topbar --}}
                @include('components.admin.topbar')
                <div class="admin-notification-panel" id="adminNotificationPanel"><div class="admin-notification-header"><strong>Notifications</strong><button onclick="markAllAdminNotificationsRead()">Mark all read</button></div><div class="admin-notification-list" id="adminNotificationList"><div class="admin-notification-empty">No notifications</div></div></div>

                {{-- Content --}}
                <main class="admin-content">
                    @if(session('success'))
                        <div class="admin-alert admin-alert-success">
                            <i class="fas fa-circle-check"></i> {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="admin-alert admin-alert-error">
                            <i class="fas fa-circle-exclamation"></i> {{ session('error') }}
                        </div>
                    @endif
                    @yield('content')
                </main>
            </div>
        </div>
    @else
        @yield('content')
    @endauth

    {{-- AI Assistant --}}
    @auth('staff')
    @if(auth('staff')->user()?->role?->can_manage_users)
        @include('admin.ai-assistant')
    @endif
@endauth

    {{-- Scripts --}}
    <script src="{{ asset('js/ajax.js') }}"></script>
    <script src="{{ asset('js/admin/layout.js') }}"></script>
@auth('staff')
    @if(auth('staff')->user()?->role?->can_manage_users)
        <script src="{{ asset('js/admin/ai-assistant.js') }}"></script>
    @endif
@endauth
    <script src="{{ asset('js/admin/notifications.js') }}"></script>
    @stack('scripts')
</body>
</html>