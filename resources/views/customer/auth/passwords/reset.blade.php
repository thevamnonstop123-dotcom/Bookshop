@extends('layouts.customer')

@section('title', 'Reset Password - Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/login.css') }}">
@endpush

@section('content')
<div class="customer-login-container">
    <div class="customer-login-card">
        <div class="customer-login-brand">
            <div class="brand-icon"><i class="fas fa-book-open"></i></div>
            <h1>Book<span>shop</span></h1>
        </div>
        <h2 class="customer-login-title">Reset Password</h2>
        <p class="customer-login-subtitle">Enter your new password</p>

        @if ($errors->any())
            <div class="alert alert-danger"><i class="fas fa-circle-exclamation"></i> {{ $errors->first() }}</div>
        @endif

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ $email }}">

            <div class="form-group">
                <label class="form-label">New Password</label>
                <div class="password-wrapper">
                    <input type="password" name="password" class="form-control" placeholder="Minimum 8 characters" required>
                    <button type="button" class="password-toggle" onclick="togglePass()" tabindex="-1">
                        <i class="fas fa-eye" id="toggleIcon"></i>
                    </button>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Confirm Password</label>
                <input type="password" name="password_confirmation" class="form-control" placeholder="Re-enter password" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;">Reset Password</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePass() {
        const pass = document.querySelector('input[name="password"]');
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
