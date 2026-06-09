@extends('layouts.customer')

@section('title', 'Order #' . $order->order_number . ' - Bookshop')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer/orders.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
@endpush

@section('content')

<div class="orders-page">
    <div class="container">

        <a href="{{ route('customer.orders.index') }}" style="color: var(--color-text-muted); font-size: 14px; margin-bottom: 20px; display: inline-block;">
            <i class="fas fa-arrow-left"></i> Back to Orders
        </a>

        <div class="order-detail-header">
            <div>
                <h1 style="font-size: 24px; margin: 0;">Order #{{ $order->order_number }}</h1>
                <p style="color: var(--color-text-muted); font-size: 14px; margin-top: 4px;">
                    Placed on {{ $order->created_at->format('d M Y, h:i A') }}
                </p>
            </div>
            <span class="order-status status-{{ $order->status }}" style="height: fit-content;">
                @switch($order->status)
                    @case('pending') <i class="fas fa-clock"></i> @break
                    @case('processing') <i class="fas fa-spinner"></i> @break
                    @case('shipped') <i class="fas fa-truck"></i> @break
                    @case('delivered') <i class="fas fa-check-circle"></i> @break
                    @case('cancelled') <i class="fas fa-times-circle"></i> @break
                @endswitch
                {{ ucfirst($order->status) }}
            </span>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px;">

            {{-- Shipping Address --}}
            <div class="order-detail-card">
                <h3><i class="fas fa-map-marker-alt"></i> Shipping Address</h3>
                @if ($order->shippingAddress)
                    <div class="order-detail-row">
                        <span class="order-detail-label">Name:</span>
                        <span class="order-detail-value">{{ $order->shippingAddress->receiver_name }}</span>
                    </div>
                    <div class="order-detail-row">
                        <span class="order-detail-label">Phone:</span>
                        <span class="order-detail-value">{{ $order->shippingAddress->phone_number }}</span>
                    </div>
                    <div class="order-detail-row">
                        <span class="order-detail-label">Address:</span>
                        <span class="order-detail-value">{{ $order->shippingAddress->address_line }}</span>
                    </div>
                @endif
            </div>

            {{-- Payment Info --}}
            <div class="order-detail-card">
                <h3><i class="fas fa-credit-card"></i> Payment</h3>
                @if ($order->payment)
                    <div class="order-detail-row">
                        <span class="order-detail-label">Method:</span>
                        <span class="order-detail-value">{{ strtoupper($order->payment->payment_method) }}</span>
                    </div>
                    <div class="order-detail-row">
                        <span class="order-detail-label">Amount:</span>
                        <span class="order-detail-value">{{ number_format($order->payment->amount) }} MMK</span>
                    </div>
                    <div class="order-detail-row">
                        <span class="order-detail-label">Status:</span>
                        <span class="order-detail-value" style="color: #10b981;">{{ ucfirst($order->payment->status) }}</span>
                    </div>
                @endif
            </div>

        </div>

        {{-- Order Items --}}
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Book</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td class="font-semibold">{{ $item->book?->title ?? 'N/A' }}</td>
                            <td>{{ number_format($item->price) }} MMK</td>
                            <td>{{ $item->quantity }}</td>
                            <td class="font-semibold">{{ number_format($item->price * $item->quantity) }} MMK</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right font-semibold">Total:</td>
                        <td class="font-bold" style="font-size: 16px; color: var(--color-accent-dark);">
                            {{ number_format($order->total_amount) }} MMK
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>
</div>

@endsection