@extends('layouts.customer')

@section('title', 'Forgot Password - Bookshop')

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
        <h2 class="customer-login-title">Forgot Password</h2>
        <p class="customer-login-subtitle">Enter your email to reset your password</p>

        @if (session('success'))
            <div class="alert alert-success"><i class="fas fa-circle-check"></i> {{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger"><i class="fas fa-circle-exclamation"></i> {{ $errors->first() }}</div>
        @endif

        <form action="{{ route('password.email') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                       placeholder="you@example.com" value="{{ old('email') }}" required autofocus>
                @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;">Send Reset Link</button>
        </form>

        <p class="customer-login-footer" style="margin-top:20px;">
            <a href="{{ route('login') }}"><i class="fas fa-arrow-left"></i> Back to Login</a>
        </p>
    </div>
</div>
@endsection
