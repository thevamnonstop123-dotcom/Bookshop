@extends('layouts.auth')

@section('title', 'Create Account — Bookshop')

@section('content')
<div class="auth-page-container auth-page-container-single">
    <div class="auth-card">

        <a href="{{ route('customer.home') }}" class="auth-brand">
        <div class="auth-brand auth-brand-center">
            <img src="{{ asset('mylogo.png') }}" alt="Bookshop" class="auth-logo-large">
        </div>
        <p class="auth-subtitle">Join our community of readers</p>

        @if ($errors->any())
            <div class="auth-alert auth-alert-error">
                <i class="fas fa-circle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" class="auth-form">
            @csrf

            <div class="auth-form-group">
                <label for="name" class="auth-label">Full Name</label>
                <div class="auth-input-wrapper">
                    <i class="fas fa-user auth-input-icon"></i>
                    <input type="text" id="name" name="name"
                           class="auth-input @error('name') auth-input-error @enderror"
                           placeholder="Your full name" value="{{ old('name') }}" required autofocus>
                </div>
                @error('name') <span class="auth-error-text">{{ $message }}</span> @enderror
            </div>

            <div class="auth-form-group">
                <label for="email" class="auth-label">Email Address</label>
                <div class="auth-input-wrapper">
                    <i class="fas fa-envelope auth-input-icon"></i>
                    <input type="email" id="email" name="email"
                           class="auth-input @error('email') auth-input-error @enderror"
                           placeholder="you@example.com" value="{{ old('email') }}" required>
                </div>
                @error('email') <span class="auth-error-text">{{ $message }}</span> @enderror
            </div>

            {{-- PASSWORD FIELD WITH STRENGTH METER --}}
            <div class="auth-form-group">
                <label for="password" class="auth-label">Password</label>
                <div class="auth-input-wrapper">
                    <i class="fas fa-lock auth-input-icon"></i>
                    <input type="password" id="password" name="password"
                           class="auth-input @error('password') auth-input-error @enderror"
                           placeholder="Minimum 8 characters" required minlength="8">
                    <button type="button" class="auth-password-toggle" onclick="togglePassword('password')">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                {{-- Password strength meter container --}}
                <div class="password-strength" id="passwordStrength"></div>
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
                <i class="fas fa-user-plus"></i> Create Account
            </button>
        </form>

        <p class="auth-switch-text">
            Already have an account?
            <a href="{{ route('login') }}" class="auth-switch-link">Sign in</a>
        </p>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toggle password visibility
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

    // Password strength meter
    document.addEventListener('DOMContentLoaded', function() {
        const passwordInput = document.getElementById('password');
        const strengthContainer = document.getElementById('passwordStrength');
        
        if (!passwordInput || !strengthContainer) return;
        
        function updateStrength() {
            const value = passwordInput.value;
            const hasMinLength = value.length >= 8;
            const hasLetter = /[a-zA-Z]/.test(value);
            const hasNumber = /[0-9]/.test(value);
            
            let strengthText = '';
            let strengthColor = '';
            let width = '0%';
            
            if (value.length === 0) {
                strengthContainer.innerHTML = '';
                return;
            }
            
            if (!hasMinLength) {
                strengthText = '✗ Weak (need 8+ characters)';
                strengthColor = '#EF4444';
                width = '33%';
            } else if (hasMinLength && (!hasLetter || !hasNumber)) {
                strengthText = '⚠ Medium (add letters & numbers)';
                strengthColor = '#F59E0B';
                width = '66%';
            } else if (hasMinLength && hasLetter && hasNumber) {
                strengthText = '✓ Strong password!';
                strengthColor = '#10B981';
                width = '100%';
            }
            
            strengthContainer.innerHTML = `
                <div class="strength-bar">
                    <div class="strength-fill" style="width: ${width}; background: ${strengthColor};"></div>
                </div>
                <span class="strength-text" style="color: ${strengthColor};">${strengthText}</span>
            `;
        }
        
        passwordInput.addEventListener('input', updateStrength);
    });
</script>
@endpush