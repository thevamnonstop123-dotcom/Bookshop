@extends('layouts.auth')

@section('title', 'Sign In — Bookshop')

@section('content')
<div class="auth-page-container auth-page-container-single">
    <div class="auth-card">

        <a href="{{ route('customer.home') }}" class="auth-brand">
            <div class="auth-brand-icon">
                <img src="{{ asset('mylogo.png') }}" alt="Bookshop" class="brand-logo">
            </div>
            <span class="auth-brand-text">Book<span class="auth-brand-accent">shop</span></span>
        </a>

        <h1 class="auth-title">Welcome Back</h1>
        <p class="auth-subtitle">Sign in to your account</p>

        @if (session('success'))
            <div class="auth-alert auth-alert-success">
                <i class="fas fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div id="loginError" class="alert alert-danger" style="display:none;">
                <i class="fas fa-circle-exclamation"></i> <span></span>
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
                           placeholder="you@example.com" value="{{ old('email') }}" required autofocus>
                </div>
                @error('email') <span class="auth-error-text">{{ $message }}</span> @enderror
            </div>

            <div class="auth-form-group">
                <label for="password" class="auth-label">Password</label>
                <div class="auth-input-wrapper">
                    <i class="fas fa-lock auth-input-icon"></i>
                    <input type="password" id="password" name="password"
                           class="auth-input @error('password') auth-input-error @enderror"
                           placeholder="Enter your password" required>
                    <button type="button" class="auth-password-toggle" onclick="togglePassword('password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                @error('password') <span class="auth-error-text">{{ $message }}</span> @enderror
            </div>

            <div class="auth-form-extras">
                <label class="auth-remember">
                    <input type="checkbox" name="remember"> Remember me
                </label>
                <a href="{{ route('password.request') }}" class="auth-forgot">Forgot password?</a>
            </div>

            <button type="submit" class="auth-submit-btn auth-submit-primary">
                <i class="fas fa-arrow-right-to-bracket"></i> Sign In
            </button>
        </form>

        <div class="auth-divider">
            <span class="auth-divider-line"></span>
            <span class="auth-divider-text">OR</span>
            <span class="auth-divider-line"></span>
        </div>

        <div class="social-buttons">
            <p class="social-label">Join with your favorite account</p>
            <div class="social-buttons-grid">
                @if (Route::has('login.google'))
                    <a href="{{ route('login.google') }}" class="social-btn social-btn-google">
                        <svg width="18" height="18" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                            <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                            <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                            <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                        </svg>
                        Google
                    </a>
                @endif

                @if (Route::has('login.facebook'))
                    <a href="{{ route('login.facebook') }}" class="social-btn social-btn-facebook">
                        <i class="fab fa-facebook-f"></i>
                        Facebook
                    </a>
                @endif
            </div>
        </div>

        <p class="auth-switch-text">
            Don't have an account?
            <a href="{{ route('register') }}" class="auth-switch-link">Create one</a>
        </p>
</div>
</div>
@endsection

@push('scripts')
<script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const wrapper = field.closest('.auth-input-wrapper');
        const button = wrapper?.querySelector('.auth-password-toggle');
        const icon = button?.querySelector('i');
        
        if (field && icon) {
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    }
</script>
@endpush