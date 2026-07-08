<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bookshop')</title>

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    {{-- Design System Variables & Base Glass Reset --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    {{-- Layout Styles --}}
    <link rel="stylesheet" href="{{ asset('css/customer/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/cart.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/auth.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/footer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/pagination.css') }}">
    <link rel="stylesheet" href="{{ asset('css/customer/author.css') }}">
    @stack('styles')
</head>
<body>

    <div class="app-glass-container">
        {{-- Navigation --}}
        @include('components.customer.navbar')

        {{-- Page Content --}}
        <main class="main-content">
            @yield('content')
        </main>

        {{-- Footer --}}
        @include('components.customer.footer')

    </div>
    
    {{-- Bottom Navigation (Mobile) --}}
    @include('components.customer.bottom-nav')

    {{-- Mobile/Categories Sidebar --}}
    @include('components.customer.sidebar')

    {{-- Cart Drawer --}}
    @include('customer.cart.drawer')

    {{-- Login/Register Modal --}}
    @include('customer.auth.login-modal')


    {{-- Core Scripts --}}
    <script src="{{ asset('js/ajax.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/customer/wishlist.js') }}"></script>
    <script src="{{ asset('js/customer/auth.js') }}"></script>
    <script src="{{ asset('js/customer/navbar.js') }}"></script>
    <script src="{{ asset('js/customer/cart.js') }}"></script>
     <script src="{{ asset('js/ux-engine.js') }}"></script>
    @stack('scripts')
</body>
</html> 
