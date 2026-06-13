@extends('layouts.customer')

@section('title', 'Forgot Password — Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/auth.css') }}">
@endpush

@section('content')
<div class="auth-page">
    <div class="auth-page-container auth-page-container-single">

        <div class="auth-card">

            <a href="{{ route('customer.home') }}" class="auth-brand">
                <div class="auth-brand-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <span class="auth-brand-text">Book<span class="auth-brand-accent">shop</span></span>
            </a>

            <div class="auth-icon-circle auth-icon-circle-help">
                <i class="fas fa-key"></i>
            </div>

            <h1 class="auth-title">Forgot Password</h1>
            <p class="auth-subtitle">Enter your email address and we will send you a reset link</p>

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

            <form action="{{ route('password.email') }}" method="POST" class="auth-form">
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
</div>
@endsection