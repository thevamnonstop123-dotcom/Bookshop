@extends('layouts.customer')

@section('title', 'Order Confirmed — Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/checkout.css') }}">
@endpush

@section('content')

<div class="success-page-wrapper">
    {{-- Modal Overlay --}}
    <div class="success-modal-overlay">
        <div class="success-modal-container">
            
            {{-- Brand --}}
            <div class="success-modal-brand">
                <div class="success-modal-brand-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <span class="success-modal-brand-text">Book<span class="success-modal-brand-accent">shop</span></span>
            </div>

            {{-- Success Icon --}}
            <div class="success-check-wrapper">
                <div class="success-check-circle">
                    <i class="fas fa-check"></i>
                </div>
            </div>

            {{-- Title --}}
            <h3 class="success-modal-title">Order Confirmed!</h3>
            <p class="success-modal-subtitle">Thank you for your purchase. Your order has been received and is being processed.</p>

            {{-- Order Number Badge --}}
            <div class="success-order-badge">
                <span class="success-order-label">Order Number</span>
                <span class="success-order-value">#{{ $order->order_number }}</span>
            </div>

            {{-- Details Grid --}}
            <div class="success-details-row">
                <div class="success-detail-block">
                    <i class="fas fa-coins"></i>
                    <span class="success-detail-label">Total</span>
                    <strong>{{ number_format($order->total_amount) }} MMK</strong>
                </div>
                <div class="success-detail-block">
                    <i class="fas fa-credit-card"></i>
                    <span class="success-detail-label">Payment</span>
                    <strong>{{ strtoupper($order->payment->payment_method ?? 'N/A') }}</strong>
                </div>
                <div class="success-detail-block">
                    <i class="fas fa-box"></i>
                    <span class="success-detail-label">Status</span>
                    <strong>{{ ucfirst($order->status) }}</strong>
                </div>
            </div>

            {{-- Actions --}}
            <div class="success-actions">
                <a href="{{ route('customer.orders.show', $order->id) }}" class="success-btn success-btn-primary">
                    <i class="fas fa-receipt"></i> View Order
                </a>
                <a href="{{ route('books.index') }}" class="success-btn success-btn-outline">
                    <i class="fas fa-book-open"></i> Continue Shopping
                </a>
            </div>

            {{-- Help Link --}}
            <p class="success-help">
                Need help? <a href="#">Contact Support</a>
            </p>
        </div>
    </div>
</div>

@endsection
