@extends('layouts.admin')

@section('title', 'Dashboard - Bookshop Admin')
@section('page_title', 'Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
@endpush

@section('content')

    @if (session('success'))
        <div class="alert alert-success" style="margin-bottom: 24px;">
            <i class="fas fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="stats-grid">

        <div class="stat-card sales">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
            <div class="stat-label">Total Sales</div>
            <div class="stat-value">{{ number_format($stats['total_sales']) }} <span style="font-size:16px;font-weight:500;">MMK</span></div>
            <div class="stat-sub">All time revenue</div>
        </div>

        <div class="stat-card orders">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-shopping-bag"></i>
                </div>
            </div>
            <div class="stat-label">Total Orders</div>
            <div class="stat-value">{{ $stats['total_orders'] }}</div>
            <div class="stat-sub">All time orders</div>
        </div>

        <div class="stat-card customers">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="stat-label">Total Customers</div>
            <div class="stat-value">{{ $stats['total_customers'] }}</div>
            <div class="stat-sub">Registered accounts</div>
        </div>

        <div class="stat-card low-stock">
            <div class="stat-header">
                <div class="stat-icon">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
            </div>
            <div class="stat-label">Low Stock Books</div>
            <div class="stat-value">{{ $stats['low_stock'] }}</div>
            <div class="stat-sub">Below 5 units</div>
        </div>

    </div>

    {{-- Chart --}}
    <div class="chart-section">
        <div class="chart-header">
            <h3>Sales Overview — This Week</h3>
        </div>

        <div class="bar-chart">
            @foreach ($chartData as $day)
                <div class="bar-item">
                    <span class="bar-value">{{ number_format($day['total']) > 0 ? number_format($day['total']) : 0 }}</span>
                    <div class="bar-fill {{ in_array($day['day'], ['Sat', 'Sun']) ? 'accent' : '' }}"
                         style="height: {{ $day['height'] }}%;"></div>
                    <span class="bar-label">{{ $day['day'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

@endsection