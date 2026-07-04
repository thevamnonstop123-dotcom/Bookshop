@extends('layouts.admin')

@section('title', 'Customer Details — Bookshop Admin')
@section('page_title', 'Customer Details')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/form.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/customer-detail.css') }}">
@endpush

@section('content')

    <a href="{{ route('admin.customers.index') }}" class="admin-form-back">
        <i class="fas fa-arrow-left"></i> Back to Customers
    </a>

    @if (session('success'))
        <div class="admin-alert admin-alert-success">
            <i class="fas fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Top Grid --}}
    <div class="customer-detail-grid">

        {{-- Personal Information --}}
        <div class="customer-detail-card">
            <div class="customer-detail-card-header">
                <div class="customer-detail-card-icon customer-detail-icon-personal">
                    <i class="fas fa-user"></i>
                </div>
                <div>
                    <h3 class="customer-detail-card-title">Personal Information</h3>
                    <p class="customer-detail-card-subtitle">Account details and status</p>
                </div>
            </div>

            <div class="customer-detail-body">
                <div class="customer-detail-row">
                    <span class="customer-detail-label">Name</span>
                    <span class="customer-detail-value">{{ $customer->name }}</span>
                </div>
                <div class="customer-detail-row">
                    <span class="customer-detail-label">Email</span>
                    <span class="customer-detail-value">{{ $customer->email }}</span>
                </div>
                <div class="customer-detail-row">
                    <span class="customer-detail-label">Phone</span>
                    <span class="customer-detail-value">{{ $customer->phone ?? '—' }}</span>
                </div>
                <div class="customer-detail-row">
                    <span class="customer-detail-label">Gender</span>
                    <span class="customer-detail-value">{{ ucfirst($customer->gender) }}</span>
                </div>
                <div class="customer-detail-row">
                    <span class="customer-detail-label">Date of Birth</span>
                    <span class="customer-detail-value">{{ $customer->dob->format('d M Y') }}</span>
                </div>
                <div class="customer-detail-row">
                    <span class="customer-detail-label">Joined</span>
                    <span class="customer-detail-value">{{ $customer->created_at->format('d M Y') }}</span>
                </div>
                <div class="customer-detail-row">
                    <span class="customer-detail-label">Status</span>
                    <span class="customer-detail-value">
                        <span class="admin-badge admin-badge-{{ $customer->status === 'active' ? 'success' : ($customer->status === 'banned' ? 'danger' : 'warning') }}">
                            <i class="fas fa-{{ $customer->status === 'active' ? 'circle-check' : ($customer->status === 'banned' ? 'circle-xmark' : 'circle-pause') }}"></i>
                            {{ ucfirst($customer->status) }}
                        </span>
                    </span>
                </div>
            </div>

            {{-- Status Actions --}}
            <div class="customer-detail-actions">
                <form action="{{ route('admin.customers.status', $customer) }}" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="active">
                    <button type="submit" class="customer-status-btn customer-status-btn-active"
                            {{ $customer->status === 'active' ? 'disabled' : '' }}>
                        <i class="fas fa-check"></i> Activate
                    </button>
                </form>
                <form action="{{ route('admin.customers.status', $customer) }}" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="inactive">
                    <button type="submit" class="customer-status-btn customer-status-btn-inactive"
                            {{ $customer->status === 'inactive' ? 'disabled' : '' }}>
                        <i class="fas fa-pause"></i> Deactivate
                    </button>
                </form>
                <form action="{{ route('admin.customers.status', $customer) }}" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="banned">
                    <button type="submit" class="customer-status-btn customer-status-btn-ban"
                            {{ $customer->status === 'banned' ? 'disabled' : '' }}
                            onclick="return confirm('Ban this customer? They will not be able to log in.')">
                        <i class="fas fa-ban"></i> Ban
                    </button>
                </form>
            </div>
        </div>

        {{-- Saved Addresses --}}
        <div class="customer-detail-card">
            <div class="customer-detail-card-header">
                <div class="customer-detail-card-icon customer-detail-icon-address">
                    <i class="fas fa-location-dot"></i>
                </div>
                <div>
                    <h3 class="customer-detail-card-title">Saved Addresses</h3>
                    <p class="customer-detail-card-subtitle">{{ $customer->addresses->count() }} {{ Str::plural('address', $customer->addresses->count()) }}</p>
                </div>
            </div>

            <div class="customer-detail-body">
                @forelse ($customer->addresses as $address)
                    <div class="customer-address-item">
                        <div class="customer-address-header">
                            <span class="customer-address-name">
                                <i class="fas fa-user"></i> {{ $address->receiver_name }}
                            </span>
                            @if ($address->is_default)
                                <span class="customer-address-default-tag">
                                    <i class="fas fa-star"></i> Default
                                </span>
                            @endif
                        </div>
                        <div class="customer-address-phone">
                            <i class="fas fa-phone"></i> {{ $address->phone_number }}
                        </div>
                        <div class="customer-address-line">
                            <i class="fas fa-location-dot"></i> {{ $address->address_line }}
                        </div>
                    </div>
                @empty
                    <div class="customer-detail-empty">
                        <i class="fas fa-map-pin"></i>
                        <p>No addresses saved yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Order History --}}
    <div class="customer-detail-card">
        <div class="customer-detail-card-header">
            <div class="customer-detail-card-icon customer-detail-icon-orders">
                <i class="fas fa-receipt"></i>
            </div>
            <div>
                <h3 class="customer-detail-card-title">Order History</h3>
                <p class="customer-detail-card-subtitle">{{ $customer->orders->count() }} {{ Str::plural('order', $customer->orders->count()) }} placed</p>
            </div>
        </div>

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order Number</th>
                        <th>Total</th>
                        <th>Items</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customer->orders as $order)
                        <tr>
                            <td>
                                <a href="{{ route('admin.orders.show', $order) }}" class="customer-order-link">
                                    #{{ $order->order_number }}
                                </a>
                            </td>
                            <td class="admin-table-price-regular">{{ number_format($order->total_amount) }} MMK</td>
                            <td>{{ $order->items->count() }}</td>
                            <td>
                                <span class="admin-badge admin-badge-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : 'warning') }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="admin-table-date">{{ $order->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <div class="customer-detail-empty">
                                    <i class="fas fa-receipt"></i>
                                    <p>No orders placed yet.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <a href="{{ route('admin.customers.index') }}" class="admin-btn admin-btn-ghost" style="margin-top: 20px;">
        <i class="fas fa-arrow-left"></i> Back to Customers
    </a>

@endsection