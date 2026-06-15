@extends('layouts.auth')

@section('title', 'Reset Password — Bookshop')

@section('content')
<div class="auth-page-container auth-page-container-single">
    <div class="auth-card">

        <a href="{{ route('customer.home') }}" class="auth-brand">
            <div class="auth-brand-icon">
                <i class="fas fa-book-open"></i>
            </div>
            <span class="auth-brand-text">Book<span class="auth-brand-accent">shop</span></span>
        </a>

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
                           placeholder="Minimum 8 characters" required minlength="8">
                    <button type="button" class="auth-password-toggle" onclick="togglePassword('password')">
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
                           class="auth-input" placeholder="Re-enter password" required minlength="8">
                    <button type="button" class="auth-password-toggle" onclick="togglePassword('password_confirmation')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="auth-submit-btn auth-submit-accent">
                <i class="fas fa-check"></i> Reset Password
            </button>
        </form>

        {{-- Back to Sign In link (opens modal on homepage) --}}
       <p class="auth-switch-text" style="margin-top: 24px; padding-top: 16px; border-top: 1px solid #E2E8F0;">
            <a href="{{ route('login') }}" class="auth-switch-link">
                <i class="fas fa-arrow-left"></i> Back to Sign In
            </a>
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