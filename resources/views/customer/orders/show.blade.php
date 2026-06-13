@extends('layouts.customer')

@section('title', 'Order #' . $order->order_number . ' — Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/orders.css') }}">
@endpush

@section('content')

<div class="order-detail-page">
    <div class="container">

        {{-- Back Link --}}
        <a href="{{ route('customer.orders.index') }}" class="order-detail-back">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>

        {{-- Header --}}
        <div class="order-detail-header">
            <div class="order-detail-header-left">
                <h1 class="order-detail-title">Order #{{ $order->order_number }}</h1>
                <p class="order-detail-date">
                    <i class="fas fa-calendar"></i>
                    Placed on {{ $order->created_at->format('d M Y') }} at {{ $order->created_at->format('h:i A') }}
                </p>
            </div>
            <span class="order-status-badge order-status-{{ $order->status }} order-status-large">
                @switch($order->status)
                    @case('pending')
                        <i class="fas fa-clock"></i>
                        @break
                    @case('processing')
                        <i class="fas fa-spinner fa-spin"></i>
                        @break
                    @case('shipped')
                        <i class="fas fa-truck-fast"></i>
                        @break
                    @case('delivered')
                        <i class="fas fa-circle-check"></i>
                        @break
                    @case('cancelled')
                        <i class="fas fa-circle-xmark"></i>
                        @break
                    @default
                        <i class="fas fa-circle"></i>
                @endswitch
                {{ ucfirst($order->status) }}
            </span>
        </div>

        {{-- Info Grid --}}
        <div class="order-detail-grid">
            {{-- Shipping Address --}}
            <div class="order-detail-card">
                <div class="order-detail-card-header">
                    <div class="order-detail-card-icon order-detail-icon-shipping">
                        <i class="fas fa-location-dot"></i>
                    </div>
                    <h3 class="order-detail-card-title">Shipping Address</h3>
                </div>
                @if ($order->shippingAddress)
                    <div class="order-detail-address">
                        <div class="order-detail-address-name">{{ $order->shippingAddress->receiver_name }}</div>
                        <div class="order-detail-address-phone">{{ $order->shippingAddress->phone_number }}</div>
                        <div class="order-detail-address-line">{{ $order->shippingAddress->address_line }}</div>
                    </div>
                @else
                    <p class="order-detail-missing">No shipping address recorded.</p>
                @endif
            </div>

            {{-- Payment Info --}}
            <div class="order-detail-card">
                <div class="order-detail-card-header">
                    <div class="order-detail-card-icon order-detail-icon-payment">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h3 class="order-detail-card-title">Payment</h3>
                </div>
                @if ($order->payment)
                    <div class="order-detail-meta-list">
                        <div class="order-detail-meta-item">
                            <span class="order-detail-meta-label">Method</span>
                            <span class="order-detail-meta-value">{{ strtoupper($order->payment->payment_method) }}</span>
                        </div>
                        <div class="order-detail-meta-item">
                            <span class="order-detail-meta-label">Amount</span>
                            <span class="order-detail-meta-value">{{ number_format($order->payment->amount) }} MMK</span>
                        </div>
                        <div class="order-detail-meta-item">
                            <span class="order-detail-meta-label">Status</span>
                            <span class="order-detail-meta-value order-detail-payment-status">{{ ucfirst($order->payment->status) }}</span>
                        </div>
                    </div>
                @else
                    <p class="order-detail-missing">No payment information available.</p>
                @endif
            </div>
        </div>

        {{-- Items Table --}}
        <div class="order-detail-card">
            <div class="order-detail-card-header">
                <div class="order-detail-card-icon order-detail-icon-items">
                    <i class="fas fa-box"></i>
                </div>
                <h3 class="order-detail-card-title">Order Items</h3>
                <span class="order-detail-card-count">{{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}</span>
            </div>

            <div class="order-items-table-wrapper">
                <table class="order-items-table">
                    <thead>
                        <tr>
                            <th>Book</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th class="order-items-table-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($order->items as $item)
                            <tr>
                                <td class="order-items-table-book">
                                    @if($item->book)
                                        <div class="order-item-book-info">
                                            <img src="{{ $item->book->image && $item->book->image !== 'default.png' ? asset('storage/' . $item->book->image) : 'https://placehold.co/40x52/F1F5F9/94A3B8?text=Book' }}"
                                                 alt="{{ $item->book->title }}" class="order-item-book-image">
                                            <span>{{ $item->book->title }}</span>
                                        </div>
                                    @else
                                        <span class="order-detail-missing">Book no longer available</span>
                                    @endif
                                </td>
                                <td>{{ number_format($item->price) }} MMK</td>
                                <td>{{ $item->quantity }}</td>
                                <td class="order-items-table-right order-items-table-subtotal">
                                    {{ number_format($item->price * $item->quantity) }} MMK
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3" class="order-items-table-total-label">Total</td>
                            <td class="order-items-table-right order-items-table-total-amount">
                                {{ number_format($order->total_amount) }} MMK
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

    </div>
</div>

@endsection