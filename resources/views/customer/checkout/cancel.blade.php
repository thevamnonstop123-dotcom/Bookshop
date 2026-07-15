@extends('layouts.customer')

@section('title', 'Payment Cancelled')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/checkout.css') }}">
@endpush

@section('content')

<div class="checkout-page">
    <div class="checkout-container">
        <div class="cancel-page">
            <div class="cancel-container">
                <div class="cancel-card">
                    <div class="cancel-icon">
                        <i class="fas fa-xmark"></i>
                    </div>
                    <h1 class="cancel-title">Payment Cancelled</h1>
                    <p class="cancel-message">Your payment was not processed. No charges have been made — you can try again whenever you are ready.</p>
                    <div class="cancel-actions">
                        <a href="{{ route('checkout.index') }}" class="cancel-btn cancel-btn-primary">
                            <i class="fas fa-rotate-left"></i> Try Again
                        </a>
                        <a href="{{ route('books.index') }}" class="cancel-btn cancel-btn-ghost">
                            <img src="{{ asset('mylogo.png') }}" alt="Bookshop" class="brand-logo"> Browse Books
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
