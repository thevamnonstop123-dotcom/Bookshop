@extends('layouts.auth')

@section('title', 'Reset Password — Bookshop')

@section('content')
<div class="auth-page-container auth-page-container-single">
    <div class="auth-card">

        <div class="auth-brand auth-brand-center">
            <img src="{{ asset('mylogo.png') }}" alt="Bookshop" class="auth-logo-large">
        </div>
        <h1 class="auth-title">Reset Password</h1>
        <p class="auth-subtitle">Create a new password for your account</p>

        @if ($errors->any())
            <div class="auth-alert auth-alert-error">
                <i class="fas fa-circle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('password.update') }}" method="POST" class="auth-form">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="auth-form-group">
                <label for="password" class="auth-label">New Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password"
                           class="auth-form-input @error('password') auth-input-error @enderror"
                           placeholder="Minimum 8 characters" required minlength="8" autocomplete="new-password">
                    <button type="button" class="password-toggle" onclick="toggleResetPassword('password')" tabindex="-1">
                        <i class="fas fa-eye" id="toggleIconPassword"></i>
                    </button>
                </div>
                @error('password') <span class="auth-error-text">{{ $message }}</span> @enderror
            </div>

            <div class="auth-form-group">
                <label for="password_confirmation" class="auth-label">Confirm Password</label>
                <div class="password-wrapper">
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           class="auth-form-input" placeholder="Re-enter password" required minlength="8" autocomplete="new-password">
                    <button type="button" class="password-toggle" onclick="toggleResetPassword('password_confirmation')" tabindex="-1">
                        <i class="fas fa-eye" id="toggleIconConfirmation"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="auth-submit-btn auth-submit-primary">
                <i class="fas fa-check"></i> Reset Password
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