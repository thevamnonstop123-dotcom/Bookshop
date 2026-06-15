<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Bookshop')</title>

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- Design System --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    {{-- Auth Styles --}}
    <link rel="stylesheet" href="{{ asset('css/customer/auth.css') }}">

    @stack('styles')
</head>
<body class="auth-body">
    <div class="auth-page">
        @yield('content')
    </div>

    {{-- Core Scripts --}}
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="{{ asset('js/customer/auth.js') }}"></script>
    @stack('scripts')
</body>
</html>