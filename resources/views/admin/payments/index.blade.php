@extends('layouts.admin')

@section('title', 'Payments — Bookshop Admin')
@section('page_title', 'Payment Management')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/table.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin/payment.css') }}">
@endpush

@section('content')

    {{-- Stats Cards --}}
    <div class="payment-stats-grid">
        <div class="payment-stat-card payment-stat-revenue">
            <div class="payment-stat-icon">
                <i class="fas fa-coins"></i>
            </div>
            <div class="payment-stat-info">
                <span class="payment-stat-label">Total Revenue</span>
                <span class="payment-stat-value">{{ number_format($stats['total_amount']) }} MMK</span>
            </div>
        </div>
        <div class="payment-stat-card payment-stat-completed">
            <div class="payment-stat-icon">
                <i class="fas fa-circle-check"></i>
            </div>
            <div class="payment-stat-info">
                <span class="payment-stat-label">Completed</span>
                <span class="payment-stat-value">{{ $stats['completed_count'] }}</span>
            </div>
        </div>
        <div class="payment-stat-card payment-stat-pending">
            <div class="payment-stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="payment-stat-info">
                <span class="payment-stat-label">Pending</span>
                <span class="payment-stat-value">{{ $stats['pending_count'] }}</span>
            </div>
        </div>
        <div class="payment-stat-card payment-stat-failed">
            <div class="payment-stat-icon">
                <i class="fas fa-circle-xmark"></i>
            </div>
            <div class="payment-stat-info">
                <span class="payment-stat-label">Failed</span>
                <span class="payment-stat-value">{{ $stats['failed_count'] }}</span>
            </div>
        </div>
    </div>

    {{-- Status Filters --}}
    <div class="payment-filter-tabs">
        <a href="{{ route('admin.payments.index') }}" class="payment-filter-tab {{ !$status ? 'payment-filter-tab-active' : '' }}">
            <i class="fas fa-list"></i> All
        </a>
        <a href="{{ route('admin.payments.index', ['status' => 'completed']) }}" class="payment-filter-tab {{ $status === 'completed' ? 'payment-filter-tab-active' : '' }}">
            <i class="fas fa-circle-check"></i> Completed
        </a>
        <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}" class="payment-filter-tab {{ $status === 'pending' ? 'payment-filter-tab-active' : '' }}">
            <i class="fas fa-clock"></i> Pending
        </a>
        <a href="{{ route('admin.payments.index', ['status' => 'failed']) }}" class="payment-filter-tab {{ $status === 'failed' ? 'payment-filter-tab-active' : '' }}">
            <i class="fas fa-circle-xmark"></i> Failed
        </a>
        <a href="{{ route('admin.payments.index', ['status' => 'refunded']) }}" class="payment-filter-tab {{ $status === 'refunded' ? 'payment-filter-tab-active' : '' }}">
            <i class="fas fa-rotate-left"></i> Refunded
        </a>
    </div>

    {{-- Method Filters --}}
    <div class="payment-filter-tabs">
        <a href="{{ route('admin.payments.index', ['status' => $status]) }}" class="payment-filter-tab {{ !$method ? 'payment-filter-tab-active' : '' }}">
            <i class="fas fa-credit-card"></i> All Methods
        </a>
        <a href="{{ route('admin.payments.index', ['status' => $status, 'method' => 'stripe']) }}" class="payment-filter-tab {{ $method === 'stripe' ? 'payment-filter-tab-active' : '' }}">
            <i class="fas fa-credit-card"></i> Stripe
        </a>
        <a href="{{ route('admin.payments.index', ['status' => $status, 'method' => 'kpay']) }}" class="payment-filter-tab {{ $method === 'kpay' ? 'payment-filter-tab-active' : '' }}">
            <i class="fas fa-building-columns"></i> KPay
        </a>
        <a href="{{ route('admin.payments.index', ['status' => $status, 'method' => 'wave']) }}" class="payment-filter-tab {{ $method === 'wave' ? 'payment-filter-tab-active' : '' }}">
            <i class="fas fa-mobile-screen-button"></i> Wave
        </a>
        <a href="{{ route('admin.payments.index', ['status' => $status, 'method' => 'cod']) }}" class="payment-filter-tab {{ $method === 'cod' ? 'payment-filter-tab-active' : '' }}">
            <i class="fas fa-hand-holding-dollar"></i> COD
        </a>
    </div>

    {{-- Payments Table --}}
    <div class="admin-table-card">
        <div class="admin-table-header">
            <div class="admin-table-header-left">
                <h2 class="admin-table-title">
                    @if($status && $method)
                        {{ ucfirst($status) }} — {{ strtoupper($method) }}
                    @elseif($status)
                        {{ ucfirst($status) }} Payments
                    @elseif($method)
                        {{ strtoupper($method) }} Payments
                    @else
                        All Payments
                    @endif
                </h2>
                <span class="admin-table-count">{{ $payments->count() }} {{ Str::plural('payment', $payments->count()) }}</span>
            </div>
        </div>

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Order</th>
                        <th>Customer</th>
                        <th style="width: 100px;">Method</th>
                        <th style="width: 140px;">Amount</th>
                        <th>Reference</th>
                        <th style="width: 110px;">Status</th>
                        <th style="width: 110px;">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $payment)
                        <tr>
                            <td class="admin-table-index">{{ $loop->iteration }}</td>
                            <td>
                                @if($payment->order)
                                    <a href="{{ route('admin.orders.show', $payment->order) }}" class="order-table-link">
                                        #{{ $payment->order->order_number }}
                                    </a>
                                @else
                                    <span class="admin-table-meta">N/A</span>
                                @endif
                            </td>
                            <td>
                                <div class="admin-table-name">{{ $payment->order?->customer?->name ?? '—' }}</div>
                                <div class="admin-table-meta">{{ $payment->order?->customer?->email ?? '' }}</div>
                            </td>
                            <td>
                                <span class="payment-method-badge payment-method-badge-{{ $payment->payment_method }}">
                                    @switch($payment->payment_method)
                                        @case('stripe')
                                            <i class="fas fa-credit-card"></i> Stripe
                                            @break
                                        @case('kpay')
                                            <i class="fas fa-building-columns"></i> KPay
                                            @break
                                        @case('wave')
                                            <i class="fas fa-mobile-screen-button"></i> Wave
                                            @break
                                        @case('cod')
                                            <i class="fas fa-hand-holding-dollar"></i> COD
                                            @break
                                        @default
                                            <i class="fas fa-circle-dollar"></i> {{ strtoupper($payment->payment_method) }}
                                    @endswitch
                                </span>
                            </td>
                            <td>
                                <span class="admin-table-price-regular">{{ number_format($payment->amount) }} MMK</span>
                            </td>
                            <td>
                                <span class="payment-reference">{{ $payment->transaction_reference ?? '—' }}</span>
                            </td>
                            <td>
                                <span class="admin-badge admin-badge-{{ $payment->status === 'completed' ? 'success' : ($payment->status === 'failed' ? 'danger' : ($payment->status === 'refunded' ? 'warning' : 'info')) }}">
                                    <i class="fas fa-{{ $payment->status === 'completed' ? 'circle-check' : ($payment->status === 'failed' ? 'circle-xmark' : ($payment->status === 'refunded' ? 'rotate-left' : 'clock')) }}"></i>
                                    {{ ucfirst($payment->status) }}
                                </span>
                            </td>
                            <td class="admin-table-date">{{ $payment->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="admin-table-empty">
                                    <div class="admin-table-empty-icon">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                    <h4>No payments found</h4>
                                    <p>{{ $status || $method ? 'No payments match the selected filters.' : 'Payments will appear here once orders are placed.' }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

@endsection