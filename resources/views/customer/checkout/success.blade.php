@extends('layouts.customer')

@section('title', 'Order Confirmed — Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/checkout.css') }}">
@endpush

@section('content')

<div class="success-overlay" id="successOverlay">
    <div class="success-modal">
        <div class="success-icon">
            <div class="success-icon-circle">
                <i class="fas fa-check"></i>
            </div>
        </div>

        <h1 class="success-title">Order Confirmed!</h1>
        <p class="success-message">Thank you for your purchase. Your order has been received and is being processed.</p>

        <div class="success-order-number">
            <span class="success-order-label">Order Number</span>
            <span class="success-order-value">#{{ $order->order_number }}</span>
        </div>

        <div class="success-details">
            <div class="success-detail-item">
                <i class="fas fa-coins"></i>
                <span>Total</span>
                <strong>{{ number_format($order->total_amount) }} MMK</strong>
            </div>
            <div class="success-detail-item">
                <i class="fas fa-credit-card"></i>
                <span>Payment</span>
                <strong>{{ strtoupper($order->payment->payment_method ?? 'N/A') }}</strong>
            </div>
        </div>

        <div class="success-actions">
            <a href="{{ route('customer.orders.index') }}" class="success-btn success-btn-outline">
                <i class="fas fa-receipt"></i> View Orders
            </a>
            <a href="{{ route('books.index') }}" class="success-btn success-btn-primary">
                <i class="fas fa-book-open"></i> Continue Shopping
            </a>
        </div>
    </div>
</div>

@endsection