@extends('layouts.customer')

@section('title', 'Reset Password — Bookshop')

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

            <div class="auth-icon-circle auth-icon-circle-success">
                <i class="fas fa-lock"></i>
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
                    <div class="auth-input-wrapper">
                        <i class="fas fa-lock auth-input-icon"></i>
                        <input type="password" id="password" name="password"
                               class="auth-input @error('password') auth-input-error @enderror"
                               placeholder="Minimum 8 characters" required minlength="8" autocomplete="new-password">
                        <button type="button" class="auth-password-toggle" onclick="togglePassword('password')" tabindex="-1" aria-label="Toggle password visibility">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    @error('password') <span class="auth-error-text">{{ $message }}</span> @enderror
                </div>

                <div class="auth-form-group">
                    <label for="password_confirmation" class="auth-label">Confirm Password</label>
                    <div class="auth-input-wrapper">
                        <i class="fas fa-lock auth-input-icon"></i>
                        <input type="password" id="password_confirmation" name="password_confirmation"
                               class="auth-input" placeholder="Re-enter password"
                               required minlength="8" autocomplete="new-password">
                    </div>
                </div>

                <button type="submit" class="auth-submit-btn auth-submit-accent">
                    <i class="fas fa-check"></i> Reset Password
                </button>
            </form>

        </div>

    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/customer/auth.js') }}"></script>
@endpush