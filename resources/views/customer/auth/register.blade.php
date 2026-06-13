@extends('layouts.customer')

@section('title', 'Create Account — Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/auth.css') }}">
@endpush

@section('content')
<div class="auth-page">
    <div class="auth-page-container">

        <div class="auth-card">

            <a href="{{ route('customer.home') }}" class="auth-brand">
                <div class="auth-brand-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <span class="auth-brand-text">Book<span class="auth-brand-accent">shop</span></span>
            </a>

            <h1 class="auth-title">Create Account</h1>
            <p class="auth-subtitle">Join our community of passionate readers</p>

            @if ($errors->any())
                <div class="auth-alert auth-alert-error">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" class="auth-form" id="registerForm">
                @csrf

                <div class="auth-form-group">
                    <label for="name" class="auth-label">Full Name</label>
                    <div class="auth-input-wrapper">
                        <i class="fas fa-user auth-input-icon"></i>
                        <input type="text" id="name" name="name"
                               class="auth-input @error('name') auth-input-error @enderror"
                               placeholder="Your full name" value="{{ old('name') }}" required autofocus autocomplete="name">
                    </div>
                    @error('name') <span class="auth-error-text">{{ $message }}</span> @enderror
                </div>

                <div class="auth-form-group">
                    <label for="email" class="auth-label">Email Address</label>
                    <div class="auth-input-wrapper">
                        <i class="fas fa-envelope auth-input-icon"></i>
                        <input type="email" id="email" name="email"
                               class="auth-input @error('email') auth-input-error @enderror"
                               placeholder="you@example.com" value="{{ old('email') }}" required autocomplete="email">
                    </div>
                    @error('email') <span class="auth-error-text">{{ $message }}</span> @enderror
                </div>

                <div class="auth-form-group">
                    <label for="phone" class="auth-label">Phone Number</label>
                    <div class="auth-input-wrapper">
                        <i class="fas fa-phone auth-input-icon"></i>
                        <input type="tel" id="phone" name="phone"
                               class="auth-input @error('phone') auth-input-error @enderror"
                               placeholder="09123456789" value="{{ old('phone') }}"
                               required maxlength="11" inputmode="numeric" autocomplete="tel"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)">
                    </div>
                    <span class="auth-hint">Must be 11 digits, starting with 09</span>
                    @error('phone') <span class="auth-error-text">{{ $message }}</span> @enderror
                </div>

                <div class="auth-form-row">
                    <div class="auth-form-group">
                        <label for="gender" class="auth-label">Gender</label>
                        <select id="gender" name="gender" class="auth-input auth-select @error('gender') auth-input-error @enderror" required>
                            <option value="">Select</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        </select>
                        @error('gender') <span class="auth-error-text">{{ $message }}</span> @enderror
                    </div>
                    <div class="auth-form-group">
                        <label for="dob" class="auth-label">Date of Birth</label>
                        <input type="date" id="dob" name="dob"
                               class="auth-input @error('dob') auth-input-error @enderror"
                               value="{{ old('dob') }}" required>
                        @error('dob') <span class="auth-error-text">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="auth-form-group">
                    <label for="password" class="auth-label">Password</label>
                    <div class="auth-input-wrapper">
                        <i class="fas fa-lock auth-input-icon"></i>
                        <input type="password" id="password" name="password"
                               class="auth-input @error('password') auth-input-error @enderror"
                               placeholder="Minimum 8 characters" required minlength="8" autocomplete="new-password"
                               oninput="checkPasswordStrength()">
                        <button type="button" class="auth-password-toggle" onclick="togglePassword('password')" tabindex="-1" aria-label="Toggle password visibility">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="auth-password-strength" id="passwordStrength">
                        <div class="auth-strength-bar">
                            <div class="auth-strength-fill" id="strengthFill"></div>
                        </div>
                        <span class="auth-strength-text" id="strengthText">Enter at least 8 characters</span>
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
                        <button type="button" class="auth-password-toggle" onclick="togglePassword('password_confirmation')" tabindex="-1" aria-label="Toggle password visibility">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="auth-submit-btn auth-submit-primary">
                    <i class="fas fa-user-plus"></i> Create Account
                </button>
            </form>

            <p class="auth-switch-text">
                Already have an account?
                <a href="{{ route('login') }}" class="auth-switch-link">Sign in</a>
            </p>
        </div>

        {{-- Side Visual --}}
        <div class="auth-visual">
            <div class="auth-visual-content">
                <div class="auth-visual-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <h2>Join Thousands of Readers</h2>
                <p>Create your account to get personalized recommendations, track orders, and build your digital library.</p>
            </div>
            <div class="auth-visual-pattern"></div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/customer/auth.js') }}"></script>
@endpush