@extends('layouts.customer')

@section('title', 'Sign In — Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/auth.css') }}">
@endpush

@section('content')
<div class="auth-page">
    <div class="auth-page-container">

        <div class="auth-card">

            {{-- Brand --}}
            <a href="{{ route('customer.home') }}" class="auth-brand">
                <div class="auth-brand-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <span class="auth-brand-text">Book<span class="auth-brand-accent">shop</span></span>
            </a>

            <h1 class="auth-title">Welcome Back</h1>
            <p class="auth-subtitle">Sign in to your account to continue</p>

            {{-- Session Messages --}}
            @if (session('success'))
                <div class="auth-alert auth-alert-success">
                    <i class="fas fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="auth-alert auth-alert-error">
                    <i class="fas fa-circle-exclamation"></i> {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="auth-form">
                @csrf

                <div class="auth-form-group">
                    <label for="email" class="auth-label">Email Address</label>
                    <div class="auth-input-wrapper">
                        <i class="fas fa-envelope auth-input-icon"></i>
                        <input type="email" id="email" name="email"
                               class="auth-input @error('email') auth-input-error @enderror"
                               placeholder="you@example.com" value="{{ old('email') }}" required autofocus autocomplete="email">
                    </div>
                    @error('email') <span class="auth-error-text">{{ $message }}</span> @enderror
                </div>

                <div class="auth-form-group">
                    <label for="password" class="auth-label">Password</label>
                    <div class="auth-input-wrapper">
                        <i class="fas fa-lock auth-input-icon"></i>
                        <input type="password" id="password" name="password"
                               class="auth-input @error('password') auth-input-error @enderror"
                               placeholder="Enter your password" required autocomplete="current-password">
                        <button type="button" class="auth-password-toggle" onclick="togglePassword('password')" tabindex="-1" aria-label="Toggle password visibility">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password') <span class="auth-error-text">{{ $message }}</span> @enderror
                </div>

                <div class="auth-extras">
                    <label class="auth-remember">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span class="auth-remember-checkmark"></span>
                        Remember me
                    </label>
                    <a href="{{ route('password.request') }}" class="auth-forgot">Forgot password?</a>
                </div>

                <button type="submit" class="auth-submit-btn auth-submit-primary">
                    <i class="fas fa-arrow-right-to-bracket"></i> Sign In
                </button>
            </form>

            <p class="auth-switch-text">
                Don't have an account?
                <a href="{{ route('register') }}" class="auth-switch-link">Create one</a>
            </p>
        </div>

        {{-- Side Visual --}}
        <div class="auth-visual">
            <div class="auth-visual-content">
                <div class="auth-visual-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <h2>Your Next Great Read Awaits</h2>
                <p>Sign in to access your library, track orders, and discover curated books tailored just for you.</p>
            </div>
            <div class="auth-visual-pattern"></div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/customer/auth.js') }}"></script>
@endpush