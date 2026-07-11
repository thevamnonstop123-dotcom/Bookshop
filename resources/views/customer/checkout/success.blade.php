@extends('layouts.customer')

@section('title', 'Order Confirmed — Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/checkout.css') }}">
@endpush

@section('content')

<div class="success-page">
    {{-- Background with subtle pattern --}}
    <div class="success-bg-pattern"></div>
    
    {{-- Success Card --}}
    <div class="success-card">
        {{-- Close/Back button --}}
        <a href="{{ route('books.index') }}" class="success-close-btn">
            <i class="fas fa-times"></i>
        </a>

        {{-- Brand --}}
        <div class="success-brand">
            <div class="success-brand-icon">
                <i class="fas fa-book-open"></i>
            </div>
            <span class="success-brand-text">Book<span class="success-brand-accent">shop</span></span>
        </div>

        {{-- Success Icon with animation --}}
        <div class="success-icon-wrapper">
            <div class="success-icon-circle">
                <i class="fas fa-check"></i>
            </div>
            {{-- Confetti particles --}}
            <div class="success-confetti">
                <span class="confetti-piece" style="--x:10%;--delay:0.2s;--color:#f59e0b;"></span>
                <span class="confetti-piece" style="--x:30%;--delay:0.6s;--color:#ef4444;"></span>
                <span class="confetti-piece" style="--x:50%;--delay:0.4s;--color:#3b82f6;"></span>
                <span class="confetti-piece" style="--x:70%;--delay:0.8s;--color:#10b981;"></span>
                <span class="confetti-piece" style="--x:90%;--delay:0.3s;--color:#8b5cf6;"></span>
            </div>
        </div>

        {{-- Title --}}
        <h3 class="success-title">Order Confirmed!</h3>
        <p class="success-subtitle">Thank you for your purchase. Your order has been received and is being processed.</p>

        {{-- Order Number --}}
        <div class="success-order-badge">
            <span class="success-order-label">Order Number</span>
            <span class="success-order-value">#{{ $order->order_number }}</span>
            <button class="success-copy-btn" onclick="copyOrderNumber('{{ $order->order_number }}')" title="Copy order number">
                <i class="fas fa-copy"></i>
            </button>
        </div>

        {{-- Details Grid --}}
        <div class="success-details-grid">
            <div class="success-detail-item">
                <div class="success-detail-icon total">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="success-detail-info">
                    <span class="success-detail-label">Total</span>
                    <strong>{{ number_format($order->total_amount) }} MMK</strong>
                </div>
            </div>
            <div class="success-detail-item">
                <div class="success-detail-icon payment">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div class="success-detail-info">
                    <span class="success-detail-label">Payment</span>
                    <strong>{{ strtoupper($order->payment->payment_method ?? 'N/A') }}</strong>
                </div>
            </div>
            <div class="success-detail-item">
                <div class="success-detail-icon status">
                    <i class="fas fa-box"></i>
                </div>
                <div class="success-detail-info">
                    <span class="success-detail-label">Status</span>
                    <strong class="status-{{ $order->status }}">{{ ucfirst($order->status) }}</strong>
                </div>
            </div>
        </div>

        {{-- Divider with decorative dots --}}
        <div class="success-divider">
            <span class="divider-dot"></span>
            <span class="divider-dot"></span>
            <span class="divider-dot"></span>
        </div>

        {{-- Actions --}}
        <div class="success-actions">
            <a href="{{ route('customer.orders.show', $order->id) }}" class="success-btn success-btn-primary">
                <i class="fas fa-receipt"></i> View Order
            </a>
            <a href="{{ route('books.index') }}" class="success-btn success-btn-secondary">
                <i class="fas fa-book-open"></i> Continue Shopping
            </a>
        </div>

        {{-- Help --}}
        <p class="success-help">
            <i class="fas fa-headset"></i> Need help? 
            <a href="">Contact Support</a>
        </p>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function copyOrderNumber(orderNumber) {
        navigator.clipboard.writeText('#' + orderNumber).then(function() {
            const btn = document.querySelector('.success-copy-btn');
            const original = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check" style="color:#16a34a;"></i>';
            setTimeout(function() {
                btn.innerHTML = original;
            }, 2000);
        });
    }
</script>
@endpush