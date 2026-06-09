@extends('layouts.admin')

@section('title', 'Customer Details - Bookshop Admin')
@section('page_title', 'Customer Details')

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
            gap: 20px;
            margin-bottom: 10px;
            font-size: var(--font-size-sm);
        }
        .detail-label {
            color: var(--color-text-muted);
            min-width: 100px;
            font-weight: var(--weight-medium);
        }
        .detail-value {
            color: var(--color-text);
            font-weight: var(--weight-semibold);
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

        {{-- Customer Info --}}
        <div class="detail-card">
            <h3><i class="fas fa-user"></i> Personal Information</h3>
            <div class="detail-row">
                <span class="detail-label">Name:</span>
                <span class="detail-value">{{ $customer->name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span class="detail-value">{{ $customer->email }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Phone:</span>
                <span class="detail-value">{{ $customer->phone }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Gender:</span>
                <span class="detail-value">{{ ucfirst($customer->gender) }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">DOB:</span>
                <span class="detail-value">{{ $customer->dob->format('d M Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Status:</span>
                <span class="detail-value">
                    <span class="badge {{ $customer->status === 'active' ? 'badge-success' : ($customer->status === 'banned' ? 'badge-danger' : 'badge-warning') }}">
                        {{ ucfirst($customer->status) }}
                    </span>
                </span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Joined:</span>
                <span class="detail-value">{{ $customer->created_at->format('d M Y') }}</span>
            </div>

            {{-- Status Actions --}}
            <div style="margin-top: 16px; display: flex; gap: 8px;">
                <form action="{{ route('admin.customers.status', $customer) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="active">
                    <button type="submit" class="btn btn-success btn-sm" {{ $customer->status === 'active' ? 'disabled' : '' }}>
                        <i class="fas fa-check"></i> Activate
                    </button>
                </form>
                <form action="{{ route('admin.customers.status', $customer) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="inactive">
                    <button type="submit" class="btn btn-warning btn-sm" {{ $customer->status === 'inactive' ? 'disabled' : '' }}>
                        <i class="fas fa-pause"></i> Deactivate
                    </button>
                </form>
                <form action="{{ route('admin.customers.status', $customer) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="banned">
                    <button type="submit" class="btn btn-danger btn-sm" {{ $customer->status === 'banned' ? 'disabled' : '' }}
                            onclick="return confirm('Ban this customer? They will not be able to login.')">
                        <i class="fas fa-ban"></i> Ban
                    </button>
                </form>
            </div>
        </div>

        {{-- Addresses --}}
        <div class="detail-card">
            <h3><i class="fas fa-map-marker-alt"></i> Saved Addresses</h3>
            @forelse ($customer->addresses as $address)
                <div style="padding: 12px; background: var(--color-surface); border-radius: var(--radius-sm); margin-bottom: 8px;">
                    <div class="font-semibold">{{ $address->receiver_name }}</div>
                    <div style="color: var(--color-text-secondary); font-size: 13px;">{{ $address->phone_number }}</div>
                    <div style="color: var(--color-text-secondary); font-size: 13px;">{{ $address->address_line }}</div>
                    @if ($address->is_default)
                        <span class="badge badge-success" style="margin-top: 6px;">Default</span>
                    @endif
                </div>
            @empty
                <p style="color: var(--color-text-muted);">No addresses saved.</p>
            @endforelse
        </div>

    </div>

    {{-- Order History --}}
    <div class="detail-card">
        <h3><i class="fas fa-shopping-bag"></i> Recent Orders</h3>
        <table>
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($customer->orders as $order)
                    <tr>
                        <td class="font-semibold">{{ $order->order_number }}</td>
                        <td>{{ number_format($order->total_amount) }} MMK</td>
                        <td>
                            <span class="badge {{ $order->status === 'delivered' ? 'badge-success' : ($order->status === 'cancelled' ? 'badge-danger' : 'badge-warning') }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td>{{ $order->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding: 20px; color: var(--color-text-muted); text-align: center;">
                            No orders yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline">
        <i class="fas fa-arrow-left"></i> Back to Customers
    </a>

@endsection