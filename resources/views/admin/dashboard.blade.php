@extends('layouts.admin')

@section('title', 'Dashboard — Bookshop Admin')
@section('page_title', 'Dashboard')

@push('styles')
    {{-- Ensure your CSS file includes the updated variables and fluid grid rules --}}
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
@endpush

@section('content')

    @if (session('success'))
        <div class="dashboard-alert dashboard-alert-success">
            <i class="fas fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Top Row: Context & Navigation --}}
    <div class="dashboard-top-row">
        <div class="dashboard-welcome">
            <h2>Welcome back, <span>{{ Auth::guard('staff')->user()->name }}</span></h2>
            <p>{{ now()->format('l, d F Y') }}</p>
        </div>
        
        <nav class="dashboard-period-tabs" aria-label="Dashboard time period">
            <a href="?period=week" class="dashboard-period-tab {{ $period === 'week' ? 'active' : '' }}">Week</a>
            <a href="?period=month" class="dashboard-period-tab {{ $period === 'month' ? 'active' : '' }}">Month</a>
            <a href="?period=year" class="dashboard-period-tab {{ $period === 'year' ? 'active' : '' }}">Year</a>
        </nav>
    </div>

    {{-- Data Layer 1: Core Metrics (Fluid Grid) --}}
    <div class="dashboard-stats-grid">
        
        <div class="dashboard-stat-card">
            <div class="dashboard-stat-icon" style="background-color:#dbeafe; color:#2563eb;">
                <i class="fas fa-coins"></i>
            </div>
            <div class="dashboard-stat-content">
                <span class="dashboard-stat-value">{{ number_format($stats['total_sales']) }} <small>MMK</small></span>
                <span class="dashboard-stat-label">Total Sales · {{ $stats['period_label'] }}</span>
            </div>
        </div>

        <div class="dashboard-stat-card {{ $stats['pending_orders'] > 0 ? 'alert-border' : '' }}">
            <div class="dashboard-stat-icon" style="background-color:#fee2e2; color:#dc2626;">
                <i class="fas fa-box-open"></i>
            </div>
            <div class="dashboard-stat-content">
                <span class="dashboard-stat-value" style="color: {{ $stats['pending_orders'] > 0 ? '#dc2626' : 'inherit' }}">
                    {{ number_format($stats['pending_orders']) }}
                </span>
                <span class="dashboard-stat-label">Pending Orders</span>
            </div>
        </div>

        <div class="dashboard-stat-card">
            <div class="dashboard-stat-icon" style="background-color:#dcfce7; color:#16a34a;">
                <i class="fas fa-receipt"></i>
            </div>
            <div class="dashboard-stat-content">
                <span class="dashboard-stat-value">{{ number_format($stats['total_orders']) }}</span>
                <span class="dashboard-stat-label">Orders · {{ $stats['period_label'] }}</span>
            </div>
        </div>

        <div class="dashboard-stat-card">
            <div class="dashboard-stat-icon" style="background-color:#f3e8ff; color:#7c3aed;">
                <i class="fas fa-users"></i>
            </div>
            <div class="dashboard-stat-content">
                <span class="dashboard-stat-value">{{ number_format($stats['new_customers']) }}</span>
                <span class="dashboard-stat-label">New Customers · {{ $stats['period_label'] }}</span>
            </div>
        </div>

        <div class="dashboard-stat-card">
            <div class="dashboard-stat-icon" style="background-color:#fef3c7; color:#d97706;">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
            <div class="dashboard-stat-content">
                <span class="dashboard-stat-value">{{ $stats['low_stock'] }} <small>/ {{ $stats['out_of_stock'] }}</small></span>
                <span class="dashboard-stat-label">Low / Out of Stock</span>
            </div>
        </div>
    </div>

    {{-- Data Layer 2: Visualizations & Lists --}}
    <div class="dashboard-bottom-layout">
        
        {{-- Chart Container (JS Engine target) --}}
        <div class="dashboard-chart-card">
            <div class="dashboard-card-header">
                <h3>Sales Overview</h3>
                <span class="dashboard-card-badge">{{ $stats['period_label'] }}</span>
            </div>
            <div id="salesChart" style="min-height: 300px; width: 100%;">
                {{-- ApexCharts injects canvas here --}}
            </div>
        </div>

        {{-- Split Grid for Data Lists --}}
        <div class="dashboard-split-grid">
            
            {{-- Panel: Recent Orders --}}
            <div class="dashboard-data-card">
                <div class="dashboard-card-header">
                    <h3>Recent Orders</h3>
                    <a href="{{ route('admin.orders.index') }}" class="dashboard-view-all">View All &rarr;</a>
                </div>
                <div class="dashboard-list">
                    @forelse($recentOrders as $order)
                        <a href="{{ route('admin.orders.show', $order['id']) }}" class="dashboard-list-item">
                            <div class="dashboard-item-left">
                                <span class="dashboard-item-primary">{{ $order['order_number'] }}</span>
                                <span class="dashboard-item-secondary">{{ $order['customer_name'] }}</span>
                            </div>
                            <div class="dashboard-item-right">
                                <span class="dashboard-item-bold">{{ number_format($order['total']) }} MMK</span>
                                <span class="dashboard-status-badge dashboard-status-{{ strtolower($order['status']) }}">
                                    {{ ucfirst($order['status']) }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="dashboard-empty-state">No recent orders found.</div>
                    @endforelse
                </div>
            </div>

            {{-- Panel: Top Selling Books --}}
            <div class="dashboard-data-card">
                <div class="dashboard-card-header">
                    <h3>Top Selling Books</h3>
                    <a href="{{ route('admin.books.index') }}" class="dashboard-view-all">Inventory &rarr;</a>
                </div>
                <div class="dashboard-list">
                    @forelse($topSellingBooks ?? [] as $book)
                        <a href="{{ route('admin.books.edit', $book['id']) }}" class="dashboard-list-item">
                            <div class="dashboard-item-left">
                                <span class="dashboard-item-primary">{{ Str::limit($book['title'], 40) }}</span>
                            </div>
                            <div class="dashboard-item-right">
                                <span class="dashboard-item-bold">{{ number_format($book['total_sold']) }}</span>
                                <span class="dashboard-item-secondary">copies</span>
                            </div>
                        </a>
                    @empty
                        <div class="dashboard-empty-state">No sales data available.</div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>

    {{-- Layer 3: System Actions --}}
    <div class="dashboard-actions">
        <a href="{{ route('admin.orders.index') }}" class="dashboard-action-btn"><i class="fas fa-receipt"></i> Orders</a>
        <a href="{{ route('admin.books.create') }}" class="dashboard-action-btn"><i class="fas fa-plus"></i> Add Book</a>
        <a href="{{ route('admin.customers.index') }}" class="dashboard-action-btn"><i class="fas fa-users"></i> Customers</a>
        <a href="{{ route('admin.books.index') }}" class="dashboard-action-btn"><i class="fas fa-book"></i> Books</a>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const rawData = @json($chartData);
        
        // Guard clause in case $chartData is empty
        if (!rawData || rawData.length === 0) return;

        const options = {
            series: [{
                name: 'Sales (MMK)',
                data: rawData.map(item => item.total)
            }],
            chart: {
                type: 'bar',
                height: 300,
                toolbar: { show: false },
                fontFamily: 'inherit'
            },
            plotOptions: {
                bar: {
                    borderRadius: 4,
                    columnWidth: '50%',
                }
            },
            colors: ['#2563eb'],
            dataLabels: { enabled: false },
            xaxis: {
                categories: rawData.map(item => item.label),
                labels: { style: { colors: '#64748b', fontSize: '12px' } },
                axisBorder: { show: false },
                axisTicks: { show: false }
            },
            yaxis: {
                labels: {
                    formatter: (val) => val.toLocaleString() + " MMK",
                    style: { colors: '#64748b', fontSize: '12px' }
                }
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4,
                yaxis: { lines: { show: true } }
            }
        };

        const chart = new ApexCharts(document.querySelector("#salesChart"), options);
        chart.render();
    });
</script>
@endpush