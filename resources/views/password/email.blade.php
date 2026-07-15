@extends('layouts.auth')

@section('title', 'Forgot Password — Bookshop')

@section('content')
<div class="auth-page-container auth-page-container-single">
    <div class="auth-card">

        <div class="auth-brand auth-brand-center">
            <img src="{{ asset('mylogo.png') }}" alt="Bookshop" class="auth-logo-large">
        </div>
        <h1 class="auth-title">Forgot Password</h1>
        <p class="auth-subtitle">Enter your email address and we'll send you a reset link.</p>

        @if (session('status'))
            <div class="auth-alert auth-alert-success">
                <i class="fas fa-circle-check"></i> {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="auth-alert auth-alert-error">
                <i class="fas fa-circle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('password.email') }}" method="POST" class="auth-form">
            @csrf

            <div class="auth-form-group">
                <label for="email" class="auth-label">Email Address</label>
                <div class="auth-input-wrapper">
                    <input type="email" id="email" name="email"
                           class="auth-form-input @error('email') auth-input-error @enderror"
                           placeholder="you@example.com" value="{{ old('email') }}" required autofocus>
                </div>
                @error('email') <span class="auth-error-text">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="auth-submit-btn auth-submit-primary">
                <i class="fas fa-paper-plane"></i> Send Reset Link
            </button>
        </form>

        <p class="auth-switch-text">
            <a href="{{ route('login') }}" class="auth-switch-link">
                <i class="fas fa-arrow-left"></i> Back to Sign In
            </a>
        </p>
    </div>
</div>
@endsection