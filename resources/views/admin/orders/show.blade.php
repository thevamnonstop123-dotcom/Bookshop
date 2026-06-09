@extends('layouts.admin')

@section('title', 'Order Details - Bookshop Admin')
@section('page_title', 'Order #' . $order->order_number)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
    <style>
        .detail-card {
            background: var(--color-white);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            padding: 24px;
            margin-bottom: 24px;
            border: 1px solid var(--color-border-light);
        }
        .detail-card h3 {
            font-size: var(--font-size-md);
            font-weight: var(--weight-semibold);
            margin-bottom: 16px;
            color: var(--color-text);
        }
        .detail-row {
            display: flex;
            gap: 16px;
            margin-bottom: 8px;
            font-size: var(--font-size-sm);
        }
        .detail-label {
            color: var(--color-text-muted);
            min-width: 120px;
            font-weight: var(--weight-medium);
        }
        .detail-value {
            color: var(--color-text);
            font-weight: var(--weight-semibold);
        }
        .status-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-top: 16px;
        }
    </style>
@endpush

@section('content')

    @if (session('success'))
        <div class="alert alert-success" style="margin-bottom: 20px;">
            <i class="fas fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">

        {{-- Order Info --}}
        <div class="detail-card">
            <h3><i class="fas fa-info-circle"></i> Order Information</h3>
            <div class="detail-row">
                <span class="detail-label">Order Number:</span>
                <span class="detail-value">{{ $order->order_number }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date:</span>
                <span class="detail-value">{{ $order->created_at->format('d M Y, h:i A') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value">
                    <span class="badge {{ $order->status === 'delivered' ? 'badge-success' : ($order->status === 'cancelled' ? 'badge-danger' : 'badge-info') }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Total:</span>
                <span class="detail-value" style="font-size: 18px; color: var(--color-accent-dark);">{{ number_format($order->total_amount) }} MMK</span>
            </div>

            {{-- Status Update Actions --}}
            <div class="status-actions">
                @foreach (['pending', 'processing', 'shipped', 'delivered', 'cancelled'] as $s)
                    <form action="{{ route('admin.orders.status', $order) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ $s }}">
                        <button type="submit" class="btn btn-sm {{ $order->status === $s ? 'btn-primary' : 'btn-outline' }}"
                                {{ $order->status === $s ? 'disabled' : '' }}>
                            {{ ucfirst($s) }}
                        </button>
                    </form>
                @endforeach
            </div>
        </div>

        {{-- Customer Info --}}
        <div class="detail-card">
            <h3><i class="fas fa-user"></i> Customer</h3>
            <div class="detail-row">
                <span class="detail-label">Name:</span>
                <span class="detail-value">{{ $order->customer?->name ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span class="detail-value">{{ $order->customer?->email ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Phone:</span>
                <span class="detail-value">{{ $order->customer?->phone ?? 'N/A' }}</span>
            </div>
        </div>

        {{-- Shipping Address --}}
        <div class="detail-card">
            <h3><i class="fas fa-truck"></i> Shipping Address</h3>
            @if ($order->shippingAddress)
                <div class="detail-row">
                    <span class="detail-label">Receiver:</span>
                    <span class="detail-value">{{ $order->shippingAddress->receiver_name }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Phone:</span>
                    <span class="detail-value">{{ $order->shippingAddress->phone_number }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Address:</span>
                    <span class="detail-value">{{ $order->shippingAddress->address_line }}</span>
                </div>
            @else
                <p style="color: var(--color-text-muted);">No shipping address.</p>
            @endif
        </div>

        {{-- Payment Info --}}
        <div class="detail-card">
            <h3><i class="fas fa-credit-card"></i> Payment</h3>
            @if ($order->payment)
                <div class="detail-row">
                    <span class="detail-label">Method:</span>
                    <span class="detail-value">{{ strtoupper($order->payment->payment_method) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Amount:</span>
                    <span class="detail-value">{{ number_format($order->payment->amount) }} MMK</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Status:</span>
                    <span class="detail-value">
                        <span class="badge {{ $order->payment->status === 'completed' ? 'badge-success' : 'badge-warning' }}">
                            {{ ucfirst($order->payment->status) }}
                        </span>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Reference:</span>
                    <span class="detail-value">{{ $order->payment->transaction_reference ?? 'N/A' }}</span>
                </div>
            @else
                <p style="color: var(--color-text-muted);">No payment recorded.</p>
            @endif
        </div>

    </div>

    {{-- Order Items --}}
    <div class="detail-card">
        <h3><i class="fas fa-list"></i> Order Items</h3>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Book</th>
                    <th>Price</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($order->items as $item)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="font-semibold">{{ $item->book?->title ?? 'N/A' }}</td>
                        <td>{{ number_format($item->price) }} MMK</td>
                        <td>{{ $item->quantity }}</td>
                        <td class="font-semibold">{{ number_format($item->subtotal()) }} MMK</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-right font-semibold">Total:</td>
                    <td class="font-bold" style="font-size: 16px; color: var(--color-accent-dark);">
                        {{ number_format($order->total_amount) }} MMK
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i> Back to Orders
    </a>

@endsection