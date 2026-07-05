@extends('layouts.admin')

@section('title', 'Dashboard — Bookshop Admin')
@section('page_title', 'Dashboard')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/dashboard.css') }}">
@endpush

@section('content')

    @if (session('success'))
        <div class="dashboard-alert dashboard-alert-success">
            <i class="fas fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="dashboard-alert dashboard-alert-error">
            <i class="fas fa-circle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Top Bar: Quick Stats + Period Filter --}}
    <div class="dashboard-topbar">
        <div class="dashboard-stats-row">
            <div class="stat-pill">
                <span class="stat-pill-icon">💰</span>
                <div>
                    <span class="stat-pill-value">{{ number_format($stats['today_sales']) }} MMK</span>
                    <span class="stat-pill-label">Today's Sales</span>
                </div>
            </div>
            <div class="stat-pill">
                <span class="stat-pill-icon">📦</span>
                <div>
                    <span class="stat-pill-value">{{ $stats['today_orders'] }}</span>
                    <span class="stat-pill-label">Today's Orders</span>
                </div>
            </div>
            <div class="stat-pill">
                <span class="stat-pill-icon">📊</span>
                <div>
                    <span class="stat-pill-value">{{ number_format($stats['today_avg_order']) }} MMK</span>
                    <span class="stat-pill-label">Avg per Order</span>
                </div>
            </div>
        </div>
        
        <nav class="dashboard-period-tabs">
            <a href="?period=week" class="period-tab {{ $period === 'week' ? 'active' : '' }}">Week</a>
            <a href="?period=month" class="period-tab {{ $period === 'month' ? 'active' : '' }}">Month</a>
            <a href="?period=year" class="period-tab {{ $period === 'year' ? 'active' : '' }}">Year</a>
        </nav>
    </div>

    {{-- Metrics Cards --}}
    <div class="dashboard-metrics">
        <div class="metric-card {{ $stats['pending_orders'] > 0 ? 'alert' : '' }}">
            <div class="metric-row">
                <div class="metric-icon" style="background:#dbeafe; color:#2563eb;">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="metric-info">
                    <span class="metric-value">{{ number_format($stats['total_sales']) }} <small>MMK</small></span>
                    <span class="metric-label">Total Sales</span>
                </div>
                @if(isset($stats['trends']['total_sales']))
                    <span class="metric-trend {{ $stats['trends']['total_sales']['is_positive'] ? 'up' : 'down' }}">
                        {{ $stats['trends']['total_sales']['percentage'] }}%
                    </span>
                @endif
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-row">
                <div class="metric-icon" style="background:#dcfce7; color:#16a34a;">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="metric-info">
                    <span class="metric-value">{{ number_format($stats['total_orders']) }}</span>
                    <span class="metric-label">Total Orders</span>
                </div>
                @if(isset($stats['trends']['total_orders']))
                    <span class="metric-trend {{ $stats['trends']['total_orders']['is_positive'] ? 'up' : 'down' }}">
                        {{ $stats['trends']['total_orders']['percentage'] }}%
                    </span>
                @endif
            </div>
        </div>

        <div class="metric-card {{ $stats['pending_orders'] > 0 ? 'alert' : '' }}">
            <div class="metric-row">
                <div class="metric-icon" style="background:{{ $stats['pending_orders'] > 0 ? '#fee2e2' : '#f0fdf4' }}; color:{{ $stats['pending_orders'] > 0 ? '#dc2626' : '#16a34a' }};">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="metric-info">
                    <span class="metric-value" style="color:{{ $stats['pending_orders'] > 0 ? '#dc2626' : 'inherit' }}">{{ number_format($stats['pending_orders']) }}</span>
                    <span class="metric-label">Pending</span>
                </div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-row">
                <div class="metric-icon" style="background:#f3e8ff; color:#7c3aed;">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="metric-info">
                    <span class="metric-value">{{ number_format($stats['new_customers']) }}</span>
                    <span class="metric-label">New Customers</span>
                </div>
                @if(isset($stats['trends']['new_customers']))
                    <span class="metric-trend {{ $stats['trends']['new_customers']['is_positive'] ? 'up' : 'down' }}">
                        {{ $stats['trends']['new_customers']['percentage'] }}%
                    </span>
                @endif
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-row">
                <div class="metric-icon" style="background:#fef3c7; color:#d97706;">
                    <i class="fas fa-boxes"></i>
                </div>
                <div class="metric-info">
                    <span class="metric-value">{{ $stats['low_stock'] }} <small>low</small> / {{ $stats['out_of_stock'] }} <small>out</small></span>
                    <span class="metric-label">Stock</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart --}}
    <div class="dashboard-card chart-card">
        <div class="card-header">
            <h3>Sales & Orders Overview</h3>
            <span class="card-badge">{{ $stats['period_label'] }}</span>
        </div>
        <div id="salesChart"></div>
    </div>

    {{-- Recent Orders + Top Selling Side by Side --}}
    <div class="dashboard-split-row">
        
        {{-- Recent Orders --}}
        <div class="dashboard-card">
            <div class="card-header">
                <h3>Recent Orders</h3>
                <a href="{{ route('admin.orders.index') }}" class="card-link">View All →</a>
            </div>
            <div class="list-compact">
                @forelse($recentOrders as $order)
                    <a href="{{ route('admin.orders.show', $order['id']) }}" class="list-item">
                        <div class="list-left">
                            <span class="list-title">{{ $order['order_number'] }}</span>
                            <span class="list-sub">{{ $order['customer_name'] }} • {{ $order['created_at'] }}</span>
                        </div>
                        <div class="list-right">
                            <span class="list-amount">{{ number_format($order['total']) }} MMK</span>
                            <span class="status-badge status-{{ strtolower($order['status']) }}">{{ ucfirst($order['status']) }}</span>
                        </div>
                    </a>
                @empty
                    <div class="empty">No recent orders</div>
                @endforelse
            </div>
        </div>

        {{-- Top Selling --}}
        <div class="dashboard-card">
            <div class="card-header">
                <h3>Top Selling Books</h3>
                <a href="{{ route('admin.books.index') }}" class="card-link">View All →</a>
            </div>
            <div class="list-compact">
                @forelse($topSellingBooks as $book)
                    <a href="{{ route('admin.books.edit', $book['id']) }}" class="list-item">
                        <div class="list-left">
                            <span class="list-title">{{ Str::limit($book['title'], 30) }}</span>
                            <span class="list-sub">{{ number_format($book['total_revenue'] ?? ($book['total_sold'] * $book['price'])) }} MMK revenue</span>
                        </div>
                        <div class="list-right">
                            <span class="list-amount">{{ number_format($book['total_sold']) }}</span>
                            <span class="list-label">sold</span>
                        </div>
                    </a>
                @empty
                    <div class="empty">No sales data</div>
                @endforelse
            </div>
        </div>

    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const chartData = @json($chartData);
    
    if (!chartData || !chartData.series || chartData.series.length === 0) {
        document.querySelector('#salesChart').innerHTML = '<div class="empty">No data available</div>';
        return;
    }

    const options = {
        series: chartData.series,
        chart: {
            type: 'area',
            height: 300,
            toolbar: { show: false },
            fontFamily: 'inherit'
        },
        colors: ['#2563eb', '#10b981'],
        stroke: { curve: 'smooth', width: [2, 2], dashArray: [0, 4] },
        fill: {
            type: ['gradient', 'solid'],
            gradient: { shade: 'light', type: 'vertical', opacityFrom: 0.3, opacityTo: 0 }
        },
        dataLabels: { enabled: false },
        markers: {
            size: [4, 0],
            colors: ['#2563eb'],
            strokeColors: '#fff',
            strokeWidth: 2,
            hover: { size: 6 }
        },
        xaxis: {
            categories: chartData.categories,
            labels: { style: { colors: '#64748b', fontSize: '11px' } },
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: [
            {
                labels: {
                    formatter: (val) => val >= 1000 ? (val/1000).toFixed(0) + 'K' : val,
                    style: { colors: '#64748b', fontSize: '11px' }
                }
            },
            {
                opposite: true,
                labels: { style: { colors: '#64748b', fontSize: '11px' } }
            }
        ],
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            fontSize: '12px',
            fontWeight: 600,
            itemMargin: { horizontal: 12 }
        },
        grid: { borderColor: '#f1f5f9', strokeDashArray: 3 },
        tooltip: {
            shared: true,
            theme: 'light',
            y: [
                { formatter: (val) => val.toLocaleString() + ' MMK' },
                { formatter: (val) => val + ' orders' }
            ]
        }
    };

    new ApexCharts(document.querySelector("#salesChart"), options).render();
});
</script>
@endpush
