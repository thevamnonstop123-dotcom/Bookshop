<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bookshop Admin')</title>

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css">

    {{-- Design System & Layout --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/ai-assistant.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pagination.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/ai-assistant.css?v=' . time()) }}">

    @stack('styles')
</head>
<body>

    @auth('staff')
        <div class="admin-layout">

            {{-- Mobile Overlay --}}
            <div class="admin-sidebar-overlay" id="sidebarOverlay"></div>

            {{-- Sidebar --}}
            @include('components.admin.sidebar')

            {{-- Main Area --}}
            <div class="admin-main">

                {{-- Topbar --}}
                @include('components.admin.topbar')

                {{-- Content --}}
                <main class="admin-content">
                    @yield('content')
                </main>
            </div>
        </div>
    @else
        @yield('content')
    @endauth

    {{-- AI Assistant --}}
    @include('admin.ai-assistant')

    {{-- Scripts --}}
    <script src="{{ asset('js/admin/layout.js') }}"></script>
    <script src="{{ asset('js/admin/ai-assistant.js') }}"></script>
    <script src="{{ asset('js/admin/ai-assistant.js?v=' . time()) }}"></script>
    @stack('scripts')
</body>
</html>