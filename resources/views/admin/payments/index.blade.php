@extends('layouts.admin')

@section('title', 'Payments - Bookshop Admin')
@section('page_title', 'Payment Management')

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
        .stats-mini {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }
        .stat-mini-card {
            background: var(--color-white);
            padding: 18px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--color-border-light);
        }
        .stat-mini-card .label {
            font-size: var(--font-size-xs);
            color: var(--color-text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .stat-mini-card .value {
            font-size: 20px;
            font-weight: var(--weight-bold);
            color: var(--color-text);
        }
        @media (max-width: 768px) {
            .stats-mini {
                grid-template-columns: repeat(2, 1fr);
            }
        }
    </style>
@endpush

@section('content')

    {{-- Stats --}}
    <div class="stats-mini">
        <div class="stat-mini-card">
            <div class="label">Total Revenue</div>
            <div class="value" style="color: #10b981;">{{ number_format($stats['total_amount']) }} MMK</div>
        </div>
        <div class="stat-mini-card">
            <div class="label">Completed</div>
            <div class="value">{{ $stats['completed_count'] }}</div>
        </div>
        <div class="stat-mini-card">
            <div class="label">Pending</div>
            <div class="value" style="color: #f59e0b;">{{ $stats['pending_count'] }}</div>
        </div>
        <div class="stat-mini-card">
            <div class="label">Failed</div>
            <div class="value" style="color: #ef4444;">{{ $stats['failed_count'] }}</div>
        </div>
    </div>

    {{-- Status Filters --}}
    <div class="filter-tabs">
        <a href="{{ route('admin.payments.index') }}" class="filter-tab {{ !$status ? 'active' : '' }}">All</a>
        <a href="{{ route('admin.payments.index', ['status' => 'completed']) }}" class="filter-tab {{ $status === 'completed' ? 'active' : '' }}">Completed</a>
        <a href="{{ route('admin.payments.index', ['status' => 'pending']) }}" class="filter-tab {{ $status === 'pending' ? 'active' : '' }}">Pending</a>
        <a href="{{ route('admin.payments.index', ['status' => 'failed']) }}" class="filter-tab {{ $status === 'failed' ? 'active' : '' }}">Failed</a>
        <a href="{{ route('admin.payments.index', ['status' => 'refunded']) }}" class="filter-tab {{ $status === 'refunded' ? 'active' : '' }}">Refunded</a>
    </div>

    {{-- Method Filters --}}
    <div class="filter-tabs">
        <a href="{{ route('admin.payments.index', ['status' => $status]) }}" class="filter-tab {{ !$method ? 'active' : '' }}">All Methods</a>
        <a href="{{ route('admin.payments.index', ['status' => $status, 'method' => 'stripe']) }}" class="filter-tab {{ $method === 'stripe' ? 'active' : '' }}">Stripe</a>
        <a href="{{ route('admin.payments.index', ['status' => $status, 'method' => 'kpay']) }}" class="filter-tab {{ $method === 'kpay' ? 'active' : '' }}">KPay</a>
        <a href="{{ route('admin.payments.index', ['status' => $status, 'method' => 'wave']) }}" class="filter-tab {{ $method === 'wave' ? 'active' : '' }}">Wave</a>
        <a href="{{ route('admin.payments.index', ['status' => $status, 'method' => 'cod']) }}" class="filter-tab {{ $method === 'cod' ? 'active' : '' }}">COD</a>
    </div>

    {{-- Table --}}
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Order</th>
                    <th>Customer</th>
                    <th>Method</th>
                    <th>Amount</th>
                    <th>Reference</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td class="font-semibold">{{ $payment->order?->order_number ?? 'N/A' }}</td>
                        <td>{{ $payment->order?->customer?->name ?? 'N/A' }}</td>
                        <td>
                            <span class="badge badge-info">{{ strtoupper($payment->payment_method) }}</span>
                        </td>
                        <td>{{ number_format($payment->amount) }} MMK</td>
                        <td style="font-size: 12px;">{{ $payment->transaction_reference ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $payment->status === 'completed' ? 'badge-success' : ($payment->status === 'failed' ? 'badge-danger' : ($payment->status === 'refunded' ? 'badge-warning' : 'badge-info')) }}">
                                {{ ucfirst($payment->status) }}
                            </span>
                        </td>
                        <td>{{ $payment->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center" style="padding: 40px; color: var(--color-text-muted);">
                            <i class="fas fa-credit-card" style="font-size: 40px; display: block; margin-bottom: 10px;"></i>
                            No payments found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection