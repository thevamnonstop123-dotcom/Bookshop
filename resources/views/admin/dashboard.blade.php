@extends('layouts.admin')

@section('title', 'Dashboard — Bookshop Admin')
@section('page_title', 'Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
@endpush

@section('content')

    {{-- Success Message --}}
    @if (session('success'))
        <div class="dashboard-alert dashboard-alert-success">
            <i class="fas fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Welcome Banner --}}
    <div class="dashboard-banner">
        <div class="dashboard-banner-content">
            <div class="dashboard-banner-text">
                <h2 class="dashboard-banner-greeting">
                    Welcome back, <span>{{ Auth::guard('staff')->user()->name }}</span>
                </h2>
                <p class="dashboard-banner-subtitle">Here is what is happening with your store today.</p>
            </div>
            <div class="dashboard-banner-date">
                <i class="fas fa-calendar"></i>
                {{ now()->format('l, d F Y') }}
            </div>
        </div>
        <div class="dashboard-banner-pattern"></div>
    </div>

    {{-- Stats Grid --}}
    <div class="dashboard-stats-grid">

        <div class="dashboard-stat-card dashboard-stat-sales">
            <div class="dashboard-stat-body">
                <div class="dashboard-stat-icon dashboard-stat-icon-sales">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="dashboard-stat-info">
                    <span class="dashboard-stat-label">Total Sales</span>
                    <span class="dashboard-stat-value">{{ number_format($stats['total_sales']) }} <small>MMK</small></span>
                </div>
            </div>
            <div class="dashboard-stat-footer">
                <span class="dashboard-stat-meta">All time revenue</span>
            </div>
        </div>

        <div class="dashboard-stat-card dashboard-stat-orders">
            <div class="dashboard-stat-body">
                <div class="dashboard-stat-icon dashboard-stat-icon-orders">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="dashboard-stat-info">
                    <span class="dashboard-stat-label">Total Orders</span>
                    <span class="dashboard-stat-value">{{ number_format($stats['total_orders']) }}</span>
                </div>
            </div>
            <div class="dashboard-stat-footer">
                <span class="dashboard-stat-meta">All time orders</span>
            </div>
        </div>

        <div class="dashboard-stat-card dashboard-stat-customers">
            <div class="dashboard-stat-body">
                <div class="dashboard-stat-icon dashboard-stat-icon-customers">
                    <i class="fas fa-users"></i>
                </div>
                <div class="dashboard-stat-info">
                    <span class="dashboard-stat-label">Total Customers</span>
                    <span class="dashboard-stat-value">{{ number_format($stats['total_customers']) }}</span>
                </div>
            </div>
            <div class="dashboard-stat-footer">
                <span class="dashboard-stat-meta">Registered accounts</span>
            </div>
        </div>

        <div class="dashboard-stat-card dashboard-stat-stock">
            <div class="dashboard-stat-body">
                <div class="dashboard-stat-icon dashboard-stat-icon-stock">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
                <div class="dashboard-stat-info">
                    <span class="dashboard-stat-label">Low Stock</span>
                    <span class="dashboard-stat-value">{{ $stats['low_stock'] }}</span>
                </div>
            </div>
            <div class="dashboard-stat-footer">
                <span class="dashboard-stat-meta">Below 5 units</span>
            </div>
        </div>

    </div>

    {{-- Quick Actions Card Wrapper --}}
    <div class="dashboard-quick-actions-card">
        <h3 class="dashboard-quick-actions-title">Quick Utilities</h3>
        <div class="dashboard-quick-actions">
            <a href="{{ route('admin.orders.index') }}" class="dashboard-quick-action">
                <i class="fas fa-receipt"></i> View Orders
            </a>
            <a href="{{ route('admin.books.create') }}" class="dashboard-quick-action">
                <i class="fas fa-plus"></i> Add Book
            </a>
            <a href="{{ route('admin.customers.index') }}" class="dashboard-quick-action">
                <i class="fas fa-users"></i> Customers
            </a>
            <a href="{{ route('admin.books.index') }}" class="dashboard-quick-action">
                <i class="fas fa-book"></i> All Books
            </a>
        </div>
    </div>

    {{-- Chart Section --}}
    <div class="dashboard-chart-card">
        <div class="dashboard-chart-header">
            <div>
                <h3 class="dashboard-chart-title">Sales Overview</h3>
                <p class="dashboard-chart-subtitle">This week's performance</p>
            </div>
            <span class="dashboard-chart-badge">
                <i class="fas fa-chart-bar"></i> Last 7 Days
            </span>
        </div>

        <div class="dashboard-bar-chart">
            @foreach ($chartData as $day)
                <div class="dashboard-bar-item">
                    <div class="dashboard-bar-tooltip">{{ number_format($day['total']) > 0 ? number_format($day['total']) . ' MMK' : 'No sales' }}</div>
                    <div class="dashboard-bar-wrapper">
                        <div class="dashboard-bar-fill {{ in_array($day['day'], ['Sat', 'Sun']) ? 'dashboard-bar-weekend' : '' }}"
                             style="height: {{ $day['height'] }}%;">
                        </div>
                    </div>
                    <span class="dashboard-bar-label {{ in_array($day['day'], ['Sat', 'Sun']) ? 'dashboard-bar-label-weekend' : '' }}">
                        {{ $day['day'] }}
                    </span>
                </div>
            @endforeach
        </div>
    </div>

@endsection