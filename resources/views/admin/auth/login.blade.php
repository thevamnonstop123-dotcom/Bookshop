@extends('layouts.admin')

@section('title', 'Staff Sign In — Bookshop Admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/login.css') }}">
@endpush

@section('content')
<div class="admin-login-page">
    <div class="admin-login-container">

        <div class="admin-login-card">

            {{-- Brand --}}
            <div class="admin-login-brand">
                <div class="admin-login-brand-icon">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <span class="admin-login-brand-text">Book<span class="admin-login-brand-accent">shop</span></span>
            </div>

            <h1 class="admin-login-title">Staff Portal</h1>
            <p class="admin-login-subtitle">Sign in to access the administration panel</p>

            {{-- Error --}}
            @if ($errors->any())
                <div class="admin-login-alert admin-login-alert-error">
                    <i class="fas fa-circle-exclamation"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            {{-- Form --}}
            <form action="{{ route('admin.login') }}" method="POST" class="admin-login-form">
                @csrf

                <div class="admin-login-form-group">
                    <label for="email" class="admin-login-label">Email Address</label>
                    <div class="admin-login-input-wrapper">
                        <i class="fas fa-envelope admin-login-input-icon"></i>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="admin-login-input @error('email') admin-login-input-error @enderror"
                            placeholder="staff@bookshop.com"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="email"
                        >
                    </div>
                    @error('email')
                        <span class="admin-login-error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="admin-login-form-group">
                    <label for="password" class="admin-login-label">Password</label>
                    <div class="admin-login-input-wrapper">
                        <i class="fas fa-lock admin-login-input-icon"></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="admin-login-input @error('password') admin-login-input-error @enderror"
                            placeholder="Enter your password"
                            required
                            autocomplete="current-password"
                        >
                        <button type="button" class="admin-login-password-toggle" onclick="toggleAdminPassword()" tabindex="-1" aria-label="Toggle password visibility">
                            <i class="fas fa-eye" id="adminToggleIcon"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="admin-login-error-text">{{ $message }}</span>
                    @enderror
                </div>

                <div class="admin-login-extras">
                    <label class="admin-login-remember">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        <span class="admin-login-remember-checkmark"></span>
                        Remember me
                    </label>
                </div>

                <button type="submit" class="admin-login-submit-btn">
                    <i class="fas fa-arrow-right-to-bracket"></i> Sign In
                </button>
            </form>
        </div>

        {{-- Footer --}}
        <p class="admin-login-footer">
            <i class="fas fa-lock"></i> Secure staff access only
        </p>

    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleAdminPassword() {
        const field = document.getElementById('password');
        const icon = document.getElementById('adminToggleIcon');
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
</script>
@endpush