@extends('layouts.customer')

@section('title', 'Order Confirmed - Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/checkout.css') }}">
@endpush

@section('content')

<div class="success-modal-overlay" onclick="this.remove()">
    <div class="success-modal" onclick="event.stopPropagation()">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i>
        </div>
        <h2>Order Confirmed!</h2>
        <p>Thank you for your purchase.</p>
        <div class="order-number">#{{ $order->order_number }}</div>
        <p style="font-size: 14px;">Total: <strong>{{ number_format($order->total_amount) }} MMK</strong></p>
        <div style="display: flex; gap: 10px; justify-content: center; margin-top: 20px;">
            <a href="{{ route('customer.orders.index') }}" class="btn btn-outline">
                <i class="fas fa-shopping-bag"></i> View Orders
            </a>
            <a href="{{ route('books.index') }}" class="btn btn-accent">
                <i class="fas fa-book"></i> Continue Shopping
            </a>
        </div>
    </div>
</div>

<script>
    setTimeout(() => {
        document.querySelector('.success-modal-overlay')?.remove();
        window.location.href = '{{ route('books.index') }}';
    }, 8000);
</script>

@endsection