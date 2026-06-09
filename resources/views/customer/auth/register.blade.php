@extends('layouts.customer')

@section('title', 'Register - Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/register.css') }}">
@endpush

@section('content')
<div class="customer-register-container">

    <div class="customer-register-card">

        {{-- Brand --}}
        <div class="customer-register-brand">
            <div class="brand-icon">
                <i class="fas fa-book-open"></i>
            </div>
            <h1>Book<span>shop</span></h1>
        </div>
        <h2 class="customer-register-title">Create Account</h2>
        <p class="customer-register-subtitle">Join our community of readers</p>

        {{-- Errors --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('register') }}" method="POST" id="registerForm">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">Full Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    class="form-control @error('name') is-invalid @enderror"
                    placeholder="Enter your full name"
                    value="{{ old('name') }}"
                    required
                    autofocus
                >
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="you@example.com"
                    value="{{ old('email') }}"
                    required
                >
                @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="phone" class="form-label">Phone Number</label>
                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    class="form-control @error('phone') is-invalid @enderror"
                    placeholder="09123456789"
                    value="{{ old('phone') }}"
                    required
                    maxlength="11"
                    pattern="09[0-9]{9}"
                    inputmode="numeric"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)"
                >
                <span class="phone-hint">Must be exactly 11 digits, starting with 09</span>
                @error('phone')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="gender" class="form-label">Gender</label>
                <select id="gender" name="gender" class="form-control @error('gender') is-invalid @enderror" required>
                    <option value="">Select Gender</option>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                </select>
                @error('gender')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="dob" class="form-label">Date of Birth</label>
                <input
                    type="date"
                    id="dob"
                    name="dob"
                    class="form-control @error('dob') is-invalid @enderror"
                    value="{{ old('dob') }}"
                    required
                >
                @error('dob')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <div class="password-wrapper">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control @error('password') is-invalid @enderror"
                        placeholder="Minimum 8 characters"
                        required
                        minlength="8"
                        onkeyup="checkPasswordStrength()"
                        oninput="checkPasswordStrength()"
                    >
                    <button type="button" class="password-toggle" onclick="togglePassword('password')" tabindex="-1">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
                <div class="password-strength">
                    <div class="strength-bar">
                        <div class="strength-fill" id="strengthFill"></div>
                    </div>
                    <span class="password-hint" id="strengthText"></span>
                </div>
                @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <div class="password-wrapper">
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        class="form-control"
                        placeholder="Re-enter your password"
                        required
                        minlength="8"
                    >
                    <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')" tabindex="-1">
                        <i class="fas fa-eye"></i>
                    </button>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-user-plus"></i> Create Account
            </button>

        </form>

        <p class="customer-register-footer">
            Already have an account? <a href="{{ route('login') }}">Sign in</a>
        </p>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = field.nextElementSibling.querySelector('i');
        field.type = field.type === 'password' ? 'text' : 'password';
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    }

    function checkPasswordStrength() {
        const password = document.getElementById('password').value;
        const fill = document.getElementById('strengthFill');
        const text = document.getElementById('strengthText');

        let strength = 0;
        let color = '#ef4444';
        let message = 'Weak — minimum 8 characters';

        if (password.length >= 8) strength++;
        if (/[a-zA-Z]/.test(password) && /[0-9]/.test(password)) strength++;
        if (/[^a-zA-Z0-9]/.test(password)) strength++;

        switch (strength) {
            case 0:
                color = '#ef4444';
                message = 'Weak — minimum 8 characters';
                fill.style.width = '25%';
                break;
            case 1:
                color = '#ef4444';
                message = 'Weak — add numbers and letters';
                fill.style.width = '33%';
                break;
            case 2:
                color = '#f59e0b';
                message = 'Medium — add symbols for strength';
                fill.style.width = '66%';
                break;
            case 3:
                color = '#10b981';
                message = 'Strong password!';
                fill.style.width = '100%';
                break;
        }

        fill.style.background = color;
        text.textContent = message;
        text.style.color = color;
    }
</script>
@endpush