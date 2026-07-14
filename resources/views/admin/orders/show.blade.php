@extends('layouts.admin')

@section('title', 'Order #' . $order->order_number . ' — Bookshop Admin')
@section('page_title', 'Order #' . $order->order_number)
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/form.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/order-detail.css') }}">
@section('content')

    <a href="{{ route('admin.orders.index') }}" class="admin-form-back">
        <i class="fas fa-arrow-left"></i> Back to Orders
    </a>

    {{-- Top Grid --}}
    <div class="order-detail-grid">

        {{-- Order Information --}}
        <div class="order-detail-card">
            <div class="order-detail-card-header">
                <div class="order-detail-card-icon order-detail-icon-info">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div>
                    <h3 class="order-detail-card-title">Order Information</h3>
                    <p class="order-detail-card-subtitle">Placed {{ $order->created_at->diffForHumans() }}</p>
                </div>
            </div>

            <div class="order-detail-body">
                <div class="order-detail-row">
                    <span class="order-detail-label">Order Number</span>
                    <span class="order-detail-value">#{{ $order->order_number }}</span>
                </div>
                <div class="order-detail-row">
                    <span class="order-detail-label">Date Placed</span>
                    <span class="order-detail-value">{{ $order->created_at->format('d M Y, h:i A') }}</span>
                </div>
                <div class="order-detail-row">
                    <span class="order-detail-label">Status</span>
                    <span class="order-detail-value">
                        <span class="admin-badge admin-badge-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : ($order->status === 'shipped' ? 'info' : 'warning')) }} admin-badge-lg">
                            <i class="fas fa-{{ $order->status === 'delivered' ? 'circle-check' : ($order->status === 'cancelled' ? 'circle-xmark' : ($order->status === 'shipped' ? 'truck-fast' : ($order->status === 'processing' ? 'spinner' : 'clock'))) }}"></i>
                            {{ ucfirst($order->status) }}
                        </span>
                    </span>
                </div>
                <div class="order-detail-row">
                    <span class="order-detail-label">Total</span>
                    <span class="order-detail-value order-detail-total">{{ number_format($order->total_amount) }} MMK</span>
                </div>
            </div>

            {{-- Status Update Actions --}}
            <div class="order-detail-actions">
                <span class="order-detail-actions-label">Update Status:</span>
                <div class="order-detail-actions-btns">
                    @foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $s)
                        <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="{{ $s }}">
                            <button type="submit"
                                    class="order-status-btn order-status-btn-{{ $s }} {{ $order->status === $s ? 'order-status-btn-current' : '' }}"
                                    {{ $order->status === $s ? 'disabled' : '' }}>
                                @switch($s)
                                    @case('pending') <i class="fas fa-clock"></i> @break
                                    @case('processing') <i class="fas fa-spinner"></i> @break
                                    @case('shipped') <i class="fas fa-truck-fast"></i> @break
                                    @case('delivered') <i class="fas fa-circle-check"></i> @break
                                    @case('cancelled') <i class="fas fa-circle-xmark"></i> @break
                                @endswitch
                                {{ ucfirst($s) }}
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Customer Information --}}
        <div class="order-detail-card">
            <div class="order-detail-card-header">
                <div class="order-detail-card-icon order-detail-icon-customer">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <h3 class="order-detail-card-title">Customer</h3>
                    <p class="order-detail-card-subtitle">
                        @if($order->customer)
                            <a href="{{ route('admin.customers.show', $order->customer) }}" class="order-detail-link">View profile</a>
                        @endif
                    </p>
                </div>
            </div>

            <div class="order-detail-body">
                <div class="order-detail-row">
                    <span class="order-detail-label">Name</span>
                    <span class="order-detail-value">{{ $order->customer?->name ?? 'Guest' }}</span>
                </div>
                <div class="order-detail-row">
                    <span class="order-detail-label">Email</span>
                    <span class="order-detail-value">{{ $order->customer?->email ?? '—' }}</span>
                </div>
                <div class="order-detail-row">
                    <span class="order-detail-label">Phone</span>
                    <span class="order-detail-value">{{ $order->customer?->phone ?? '—' }}</span>
                </div>
            </div>
        </div>

        {{-- Shipping Address --}}
        <div class="order-detail-card">
            <div class="order-detail-card-header">
                <div class="order-detail-card-icon order-detail-icon-shipping">
                    <i class="fas fa-truck"></i>
                </div>
                <div>
                    <h3 class="order-detail-card-title">Shipping Address</h3>
                </div>
            </div>

            <div class="order-detail-body">
                @if ($order->shippingAddress)
                    <div class="order-detail-row">
                        <span class="order-detail-label">Receiver</span>
                        <span class="order-detail-value">{{ $order->shippingAddress->receiver_name }}</span>
                    </div>
                    <div class="order-detail-row">
                        <span class="order-detail-label">Phone</span>
                        <span class="order-detail-value">{{ $order->shippingAddress->phone_number }}</span>
                    </div>
                    <div class="order-detail-row">
                        <span class="order-detail-label">Address</span>
                        <span class="order-detail-value">{{ $order->shippingAddress->address_line }}</span>
                    </div>
                @else
                    <div class="customer-detail-empty">
                        <i class="fas fa-map-pin"></i>
                        <p>No shipping address recorded.</p>
                    </div>
                @endif
            </div>
        </div>

        {{-- Payment Information --}}
        <div class="order-detail-card">
            <div class="order-detail-card-header">
                <div class="order-detail-card-icon order-detail-icon-payment">
                    <i class="fas fa-credit-card"></i>
                </div>
                <div>
                    <h3 class="order-detail-card-title">Payment</h3>
                </div>
            </div>

            <div class="order-detail-body">
                @if ($order->payment)
                    <div class="order-detail-row">
                        <span class="order-detail-label">Method</span>
                        <span class="order-detail-value">{{ strtoupper($order->payment->payment_method) }}</span>
                    </div>
                    <div class="order-detail-row">
                        <span class="order-detail-label">Amount</span>
                        <span class="order-detail-value">{{ number_format($order->payment->amount) }} MMK</span>
                    </div>
                    <div class="order-detail-row">
                        <span class="order-detail-label">Status</span>
                        <span class="order-detail-value">
                            <span class="admin-badge admin-badge-{{ $order->payment->status === 'completed' ? 'success' : 'warning' }}">
                                {{ ucfirst($order->payment->status) }}
                            </span>
                        </span>
                    </div>
                    <div class="order-detail-row">
                        <span class="order-detail-label">Reference</span>
                        <span class="order-detail-value order-detail-mono">{{ $order->payment->transaction_reference ?? 'N/A' }}</span>
                    </div>
                @else
                    <div class="customer-detail-empty">
                        <i class="fas fa-credit-card"></i>
                        <p>No payment recorded yet.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- Order Items --}}
    <div class="order-detail-card">
        <div class="order-detail-card-header">
            <div class="order-detail-card-icon order-detail-icon-items">
                <i class="fas fa-box"></i>
            </div>
            <div>
                <h3 class="order-detail-card-title">Order Items</h3>
                <p class="order-detail-card-subtitle">{{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}</p>
            </div>
        </div>

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Book</th>
                        <th style="width: 130px;">Price</th>
                        <th style="width: 80px;">Qty</th>
                        <th style="width: 140px;">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td class="admin-table-index">{{ $loop->iteration }}</td>
                            <td>
                                <div class="admin-table-name">{{ $item->book?->title ?? 'Book no longer available' }}</div>
                                @if($item->book?->isEbook())
                                    <span class="admin-table-ebook-tag">
                                        <i class="fas fa-bolt"></i> E-Book
                                    </span>
                                @endif
                            </td>
                            <td class="admin-table-price-regular">{{ number_format($item->price) }} MMK</td>
                            <td class="admin-table-number">{{ $item->quantity }}</td>
                            <td class="admin-table-price-regular admin-table-price-bold">
                                {{ number_format($item->subtotal()) }} MMK
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="order-items-total-label">Total</td>
                        <td class="order-items-total-amount">{{ number_format($order->total_amount) }} MMK</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <a href="{{ route('admin.orders.index') }}" class="admin-form-back" style="margin-top:20px">
        <i class="fas fa-arrow-left"></i> Back to Orders
    </a>

@endsection