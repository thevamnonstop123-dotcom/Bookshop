<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bookshop')</title>

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.1/css/all.min.css">

    {{-- Design System --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    {{-- Layout Styles --}}
    <link rel="stylesheet" href="{{ asset('css/customer/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/cart.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/auth.css') }}">

    @stack('styles')
</head>
<body>

    {{-- Navigation --}}
    @include('components.customer.navbar')

    {{-- Mobile Sidebar --}}
    @include('components.customer.sidebar')

    {{-- Page Content --}}
    <main class="main-content">
        @yield('content')
    </main>

    {{-- Footer --}}
    @include('components.customer.footer')

    {{-- Cart Drawer --}}
    @include('customer.cart.drawer')

    {{-- Login/Register Modal --}}
    @include('customer.auth.login-modal')

    {{-- Core Scripts --}}
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/customer/auth.js') }}"></script>
    <script src="{{ asset('js/customer/navbar.js') }}"></script>
    <script src="{{ asset('js/customer/cart.js') }}"></script>
    @stack('scripts')
</body>
</html>