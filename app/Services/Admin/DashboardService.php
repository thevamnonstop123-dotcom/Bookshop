<?php

namespace App\Services\Admin;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Book;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    private int $cacheTTL;

    public function __construct()
    {
        $this->cacheTTL = config('shop.dashboard_cache_ttl', 300);
    }

    /**
     * Get comprehensive dashboard statistics with trend analysis.
     */
    public function getStats(string $period = 'week'): array
    {
        $cacheKey = "dashboard_stats_{$period}";
        
        $cached = Cache::remember($cacheKey, $this->cacheTTL, function () use ($period) {
            $dateRange = $this->getDateRange($period);
            $previousRange = $this->getPreviousPeriodRange($period);

            $currentMetrics = $this->calculateMetrics($dateRange);
            $previousMetrics = $this->calculateMetrics($previousRange);
            $trends = $this->calculateTrends($currentMetrics, $previousMetrics);

            return [
                'total_sales' => $currentMetrics['total_sales'],
                'total_orders' => $currentMetrics['total_orders'],
                'new_customers' => $currentMetrics['new_customers'],
                'low_stock' => $currentMetrics['low_stock'],
                'out_of_stock' => $currentMetrics['out_of_stock'],
                'average_order_value' => $currentMetrics['average_order_value'],
                'total_customers' => $currentMetrics['total_customers'],
                'trends' => $trends,
            ];
        });

        // Pending orders - ALWAYS REAL-TIME (no cache)
        $pendingOrders = Order::where('status', 'pending')->count();

        // Today's data - short cache (60 seconds)
        $todayData = $this->getTodayData();

        return array_merge($cached, [
            'pending_orders' => $pendingOrders,
            'today_sales' => $todayData['sales'],
            'today_orders' => $todayData['orders'],
            'today_avg_order' => $todayData['avg_order'],
            'period' => $period,
            'period_label' => $this->getDateRange($period)['label'],
        ]);
    }

    /**
     * Get today's data with short cache.
     */
    private function getTodayData(): array
    {
        return Cache::remember('dashboard_today_data', 60, function () {
            $todaySales = (float) Payment::where('status', 'completed')
                ->whereDate('created_at', Carbon::today())
                ->sum('amount');
                
            $todayOrders = Order::whereDate('created_at', Carbon::today())->count();
            $todayAvgOrder = $todayOrders > 0 ? $todaySales / $todayOrders : 0;

            return [
                'sales' => $todaySales,
                'orders' => $todayOrders,
                'avg_order' => round($todayAvgOrder, 2),
            ];
        });
    }

    /**
     * Calculate metrics for a given date range.
     */
    private function calculateMetrics(array $dateRange): array
    {
        $totalSales = (float) Payment::where('status', 'completed')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->sum('amount');

        $totalOrders = Order::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count();
        
        $newCustomers = Customer::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count();
        
        $totalCustomers = Customer::count();
        
        $lowStockThreshold = config('shop.low_stock_threshold', 5);
        $lowStock = Book::where('stock_quantity', '<=', $lowStockThreshold)
            ->where('stock_quantity', '>', 0)
            ->count();
            
        $outOfStock = Book::where('stock_quantity', '<=', 0)->count();

        $averageOrderValue = $totalOrders > 0 ? $totalSales / $totalOrders : 0;

        return [
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'new_customers' => $newCustomers,
            'low_stock' => $lowStock,
            'out_of_stock' => $outOfStock,
            'average_order_value' => $averageOrderValue,
            'total_customers' => $totalCustomers,
        ];
    }

    /**
     * Calculate trend percentages.
     */
    private function calculateTrends(array $current, array $previous): array
    {
        $trends = [];
        $metricsToTrack = ['total_sales', 'total_orders', 'new_customers'];
        
        foreach ($metricsToTrack as $metric) {
            $currentValue = $current[$metric];
            $previousValue = $previous[$metric];
            
            if ($previousValue > 0) {
                $percentageChange = (($currentValue - $previousValue) / $previousValue) * 100;
                $trends[$metric] = [
                    'percentage' => round($percentageChange, 1),
                    'direction' => $percentageChange >= 0 ? 'up' : 'down',
                    'is_positive' => $percentageChange >= 0,
                ];
            } else {
                $trends[$metric] = [
                    'percentage' => $currentValue > 0 ? 100 : 0,
                    'direction' => $currentValue > 0 ? 'up' : 'neutral',
                    'is_positive' => $currentValue > 0,
                ];
            }
        }
        
        return $trends;
    }

    /**
     * Get enhanced chart data.
     */
    public function getChartData(string $period = 'week'): array
    {
        $cacheKey = "dashboard_chart_{$period}";
        
        return Cache::remember($cacheKey, $this->cacheTTL, function () use ($period) {
            $dateRange = $this->getDateRange($period);
            $format = ($period === 'year') ? '%Y-%m' : '%Y-%m-%d';
            
            $sales = Payment::select(
                    DB::raw("DATE_FORMAT(created_at, '$format') as date_group"), 
                    DB::raw('SUM(amount) as total')
                )
                ->where('status', 'completed')
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->groupBy('date_group')
                ->pluck('total', 'date_group');

            $orders = Order::select(
                    DB::raw("DATE_FORMAT(created_at, '$format') as date_group"), 
                    DB::raw('COUNT(*) as total')
                )
                ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
                ->groupBy('date_group')
                ->pluck('total', 'date_group');

            $categories = [];
            $salesData = [];
            $ordersData = [];

            $current = $dateRange['start']->copy();
            $end = $dateRange['end']->copy();

            while ($current->lte($end)) {
                $key = ($period === 'year') ? $current->format('Y-m') : $current->format('Y-m-d');
                $label = ($period === 'year') ? $current->format('M Y') : $current->format('M d');
                
                $categories[] = $label;
                $salesData[] = (float) ($sales->get($key, 0));
                $ordersData[] = (int) ($orders->get($key, 0));

                if ($period === 'year') {
                    $current->addMonth();
                } else {
                    $current->addDay();
                }
            }

            return [
                'categories' => $categories,
                'series' => [
                    ['name' => 'Sales (MMK)', 'data' => $salesData],
                    ['name' => 'Orders', 'data' => $ordersData],
                ],
            ];
        });
    }

    /**
     * Get recent orders.
     */
    public function getRecentOrders(int $limit = 5): array
    {
        return Cache::remember("dashboard_recent_orders_{$limit}", 30, function () use ($limit) {
            return Order::with('customer:id,name')
                ->select(['id', 'customer_id', 'order_number', 'total_amount', 'status', 'created_at'])
                ->latest()
                ->limit($limit)
                ->get()
                ->map(function ($order) {
                    return [
                        'id' => $order->id,
                        'order_number' => $order->order_number,
                        'customer_name' => $order->customer->name ?? 'Unknown',
                        'total' => $order->total_amount,
                        'status' => $order->status,
                        'created_at' => $order->created_at->diffForHumans(),
                    ];
                })
                ->toArray();
        });
    }

    /**
     * Get top selling books.
     */
    public function getTopSellingBooks(int $limit = 5): array
    {
        return Cache::remember("dashboard_top_books_{$limit}", 300, function () use ($limit) {
            return Book::select(
                    'books.id', 'books.title', 'books.image', 'books.price',
                    DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_sold'),
                    DB::raw('COALESCE(SUM(order_items.quantity * order_items.price), 0) as total_revenue')
                )
                ->leftJoin('order_items', 'books.id', '=', 'order_items.book_id')
                ->leftJoin('orders', function ($join) {
                    $join->on('orders.id', '=', 'order_items.order_id')
                         ->where('orders.status', 'completed');
                })
                ->groupBy('books.id', 'books.title', 'books.image', 'books.price')
                ->orderByDesc('total_sold')
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }

    /**
     * Get date range.
     */
    private function getDateRange(string $period): array
    {
        $now = Carbon::now();

        return match ($period) {
            'month' => ['start' => $now->copy()->startOfMonth(), 'end' => $now, 'label' => 'This Month'],
            'year' => ['start' => $now->copy()->startOfYear(), 'end' => $now, 'label' => 'This Year'],
            default => ['start' => $now->copy()->startOfWeek(), 'end' => $now, 'label' => 'This Week'],
        };
    }

    private function getPreviousPeriodRange(string $period): array
    {
        $now = Carbon::now();
        return match ($period) {
            'month' => ['start' => $now->copy()->subMonth()->startOfMonth(), 'end' => $now->copy()->subMonth()->endOfMonth()],
            'year' => ['start' => $now->copy()->subYear()->startOfYear(), 'end' => $now->copy()->subYear()->endOfYear()],
            default => ['start' => $now->copy()->subWeek()->startOfWeek(), 'end' => $now->copy()->subWeek()->endOfWeek()],
        };
    }

    public function clearCache(): void
    {
        foreach (['week', 'month', 'year'] as $period) {
            Cache::forget("dashboard_stats_{$period}");
            Cache::forget("dashboard_chart_{$period}");
        }
        foreach ([5, 10] as $limit) {
            Cache::forget("dashboard_recent_orders_{$limit}");
            Cache::forget("dashboard_top_books_{$limit}");
        }
        Cache::forget('dashboard_today_data');
    }
}
