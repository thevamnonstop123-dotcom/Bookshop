@extends('layouts.customer')

@section('title', 'Order Confirmed — Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/checkout.css') }}">
@endpush

@section('content')

<div class="success-page">
    <div class="success-card">
        <a href="{{ route('books.index') }}" class="success-close-btn">
            <i class="fas fa-times"></i>
        </a>

        <div class="success-icon-wrap">
            <div class="success-icon-circle">
                <i class="fas fa-check"></i>
            </div>
        </div>

        <h3 class="success-title">Order Confirmed</h3>
        <p class="success-subtitle">Thank you for your purchase. Your order is being processed.</p>

        <div class="success-order-badge">
            <span class="success-order-label">Order Number</span>
            <span class="success-order-value">#{{ $order->order_number }}</span>
            <button class="success-copy-btn" onclick="copyOrderNumber('{{ $order->order_number }}')" title="Copy">
                <i class="fas fa-copy"></i>
            </button>
        </div>

        <div class="success-details-grid">
            <div class="success-detail-item">
                <span class="success-detail-label">Total</span>
                <strong>{{ number_format($order->total_amount) }} MMK</strong>
            </div>
            <div class="success-detail-item">
                <span class="success-detail-label">Payment</span>
                <strong>{{ strtoupper($order->payment->payment_method ?? 'N/A') }}</strong>
            </div>
            <div class="success-detail-item">
                <span class="success-detail-label">Status</span>
                <strong class="status-{{ $order->status }}">{{ ucfirst($order->status) }}</strong>
            </div>
        </div>

        <div class="success-actions">
            <a href="{{ route('customer.orders.show', $order->id) }}" class="success-btn success-btn-primary">
                <i class="fas fa-receipt"></i> View Order
            </a>
            <a href="{{ route('books.index') }}" class="success-btn success-btn-secondary">
                <i class="fas fa-book-open"></i> Continue Shopping
            </a>
        </div>

        <p class="success-help">
            <i class="fas fa-headset"></i> Need help? <a href="">Contact Support</a>
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
            setTimeout(function() { btn.innerHTML = original; }, 2000);
        });
    }
</script>
@endpush
