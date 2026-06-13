@extends('layouts.admin')

@section('title', 'Orders — Bookshop Admin')
@section('page_title', 'Order Management')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
@endpush

@section('content')

    @if (session('success'))
        <div class="admin-alert admin-alert-success">
            <i class="fas fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Filter Tabs --}}
    <div class="order-filter-tabs">
        <a href="{{ route('admin.orders.index') }}" class="order-filter-tab {{ !$status ? 'order-filter-tab-active' : '' }}">
            <i class="fas fa-list"></i> All
            <span class="order-filter-count">{{ $counts['total'] }}</span>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="order-filter-tab {{ $status === 'pending' ? 'order-filter-tab-active' : '' }}">
            <i class="fas fa-clock"></i> Pending
            <span class="order-filter-count">{{ $counts['pending'] }}</span>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'processing']) }}" class="order-filter-tab {{ $status === 'processing' ? 'order-filter-tab-active' : '' }}">
            <i class="fas fa-spinner"></i> Processing
            <span class="order-filter-count">{{ $counts['processing'] }}</span>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'shipped']) }}" class="order-filter-tab {{ $status === 'shipped' ? 'order-filter-tab-active' : '' }}">
            <i class="fas fa-truck-fast"></i> Shipped
            <span class="order-filter-count">{{ $counts['shipped'] }}</span>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'delivered']) }}" class="order-filter-tab {{ $status === 'delivered' ? 'order-filter-tab-active' : '' }}">
            <i class="fas fa-circle-check"></i> Delivered
            <span class="order-filter-count">{{ $counts['delivered'] }}</span>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}" class="order-filter-tab {{ $status === 'cancelled' ? 'order-filter-tab-active' : '' }}">
            <i class="fas fa-circle-xmark"></i> Cancelled
            <span class="order-filter-count">{{ $counts['cancelled'] }}</span>
        </a>
    </div>

    {{-- Orders Table --}}
    <div class="admin-table-card">
        <div class="admin-table-header">
            <div class="admin-table-header-left">
                <h2 class="admin-table-title">
                    @if($status)
                        {{ ucfirst($status) }} Orders
                    @else
                        All Orders
                    @endif
                </h2>
                <span class="admin-table-count">{{ $orders->count() }} {{ Str::plural('order', $orders->count()) }}</span>
            </div>
        </div>

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order Number</th>
                        <th>Customer</th>
                        <th style="width: 130px;">Total</th>
                        <th style="width: 110px;">Payment</th>
                        <th style="width: 120px;">Status</th>
                        <th style="width: 110px;">Date</th>
                        <th style="width: 80px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>
                                <a href="{{ route('admin.orders.show', $order) }}" class="order-table-link">
                                    #{{ $order->order_number }}
                                </a>
                            </td>
                            <td>
                                <div class="admin-table-name">{{ $order->customer?->name ?? 'Guest' }}</div>
                                <div class="admin-table-meta">{{ $order->customer?->email ?? '—' }}</div>
                            </td>
                            <td>
                                <span class="admin-table-price-regular">{{ number_format($order->total_amount) }} MMK</span>
                            </td>
                            <td>
                                @if ($order->payment)
                                    <span class="admin-badge admin-badge-{{ $order->payment->status === 'completed' ? 'success' : 'warning' }}">
                                        {{ ucfirst($order->payment->status) }}
                                    </span>
                                @else
                                    <span class="admin-badge admin-badge-danger">Unpaid</span>
                                @endif
                            </td>
                            <td>
                                <span class="admin-badge admin-badge-{{ $order->status === 'delivered' ? 'success' : ($order->status === 'cancelled' ? 'danger' : ($order->status === 'shipped' ? 'info' : 'warning')) }}">
                                    <i class="fas fa-{{ $order->status === 'delivered' ? 'circle-check' : ($order->status === 'cancelled' ? 'circle-xmark' : ($order->status === 'shipped' ? 'truck-fast' : ($order->status === 'processing' ? 'spinner' : 'clock'))) }}"></i>
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td class="admin-table-date">{{ $order->created_at->format('d M Y') }}</td>
                            <td>
                                <a href="{{ route('admin.orders.show', $order) }}" class="admin-action-btn admin-action-view" title="View details">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="admin-table-empty">
                                    <div class="admin-table-empty-icon">
                                        <i class="fas fa-receipt"></i>
                                    </div>
                                    <h4>No orders found</h4>
                                    <p>{{ $status ? 'No ' . $status . ' orders at the moment.' : 'Orders will appear here once customers place them.' }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

@endsection