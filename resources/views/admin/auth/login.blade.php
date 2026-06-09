@extends('layouts.admin')

@section('title', 'Admin Login - Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/login.css') }}">
@endpush

@section('content')
<div class="admin-login-container">

    <div class="admin-login-card">

        {{-- Brand --}}
        <div class="admin-login-brand">
            <div class="brand-icon">
                <i class="fas fa-book-open"></i>
            </div>
            <h1>Book<span>shop</span></h1>
        </div>
        <p class="admin-login-subtitle">Staff Administration Panel</p>

        {{-- Error --}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <i class="fas fa-circle-exclamation"></i> {{ $errors->first() }}
            </div>
        @endif

        {{-- Form --}}
        <form action="{{ route('admin.login') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="email" class="form-label">Email Address</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="Enter your email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                >
                @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="Enter your password"
                    required
                >
                @error('password')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>

            <div class="admin-login-extras">
                <label class="remember-me">
                    <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                    Remember me
                </label>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-lock"></i> Sign In
            </button>
        </form>
    </div>

</div>
@endsection