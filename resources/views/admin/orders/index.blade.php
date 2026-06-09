@extends('layouts.admin')

@section('title', 'Orders - Bookshop Admin')
@section('page_title', 'Order Management')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
    <style>
        .filter-tabs {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .filter-tab {
            padding: 8px 16px;
            border-radius: var(--radius-full);
            font-size: var(--font-size-sm);
            font-weight: var(--weight-medium);
            text-decoration: none;
            transition: var(--transition-fast);
            background: var(--color-white);
            color: var(--color-text-secondary);
            border: 1.5px solid var(--color-border);
        }
        .filter-tab:hover, .filter-tab.active {
            background: var(--color-primary);
            color: var(--color-white);
            border-color: var(--color-primary);
        }
        .filter-tab .count {
            margin-left: 4px;
            opacity: 0.7;
        }
    </style>
@endpush

@section('content')

    @if (session('success'))
        <div class="alert alert-success" style="margin-bottom: 20px;">
            <i class="fas fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Filter Tabs --}}
    <div class="filter-tabs">
        <a href="{{ route('admin.orders.index') }}" class="filter-tab {{ !$status ? 'active' : '' }}">
            All <span class="count">({{ $counts['total'] }})</span>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="filter-tab {{ $status === 'pending' ? 'active' : '' }}">
            Pending <span class="count">({{ $counts['pending'] }})</span>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'processing']) }}" class="filter-tab {{ $status === 'processing' ? 'active' : '' }}">
            Processing <span class="count">({{ $counts['processing'] }})</span>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'shipped']) }}" class="filter-tab {{ $status === 'shipped' ? 'active' : '' }}">
            Shipped <span class="count">({{ $counts['shipped'] }})</span>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'delivered']) }}" class="filter-tab {{ $status === 'delivered' ? 'active' : '' }}">
            Delivered <span class="count">({{ $counts['delivered'] }})</span>
        </a>
        <a href="{{ route('admin.orders.index', ['status' => 'cancelled']) }}" class="filter-tab {{ $status === 'cancelled' ? 'active' : '' }}">
            Cancelled <span class="count">({{ $counts['cancelled'] }})</span>
        </a>
    </div>

    {{-- Orders Table --}}
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Customer</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr>
                        <td class="font-semibold">{{ $order->order_number }}</td>
                        <td>{{ $order->customer?->name ?? 'N/A' }}</td>
                        <td>{{ number_format($order->total_amount) }} MMK</td>
                        <td>
                            @if ($order->payment)
                                <span class="badge {{ $order->payment->status === 'completed' ? 'badge-success' : 'badge-warning' }}">
                                    {{ ucfirst($order->payment->status) }}
                                </span>
                            @else
                                <span class="badge badge-danger">Unpaid</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $order->status === 'delivered' ? 'badge-success' : ($order->status === 'cancelled' ? 'badge-danger' : 'badge-info') }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td>{{ $order->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-outline btn-sm">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center" style="padding: 40px; color: var(--color-text-muted);">
                            <i class="fas fa-shopping-bag" style="font-size: 40px; display: block; margin-bottom: 10px;"></i>
                            No orders found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection