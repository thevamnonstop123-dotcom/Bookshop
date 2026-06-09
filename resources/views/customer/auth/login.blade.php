@extends('layouts.customer')

@section('title', 'Login - Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/login.css') }}">
@endpush

@section('content')
<div class="customer-login-container">

    <div class="customer-login-card">

        <div class="customer-login-brand">
            <div class="brand-icon">
                <i class="fas fa-book-open"></i>
            </div>
            <h1>Book<span>shop</span></h1>
        </div>
        <h2 class="customer-login-title">Welcome Back</h2>
        <p class="customer-login-subtitle">Sign in to your account</p>

        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-circle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email"
                       class="form-control @error('email') is-invalid @enderror"
                       placeholder="you@example.com" value="{{ old('email') }}" required autofocus>
                @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Enter your password" required>
                    <button type="button" class="password-toggle" onclick="togglePass()" tabindex="-1">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
                @error('password') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>

            <div class="customer-login-extras">
                <label class="remember-me">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Remember me
                </label>
                <a href="{{ route('password.request') }}" class="forgot-password">Forgot password?</a>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-sign-in-alt"></i> Sign In
            </button>
        </form>

        <p class="customer-login-footer">
            Don't have an account? <a href="{{ route('register') }}">Create one</a>
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePass() {
        const pass = document.getElementById('password');
        const icon = document.getElementById('toggleIcon');
        if (pass.type === 'password') {
            pass.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            pass.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
</script>
@endpush