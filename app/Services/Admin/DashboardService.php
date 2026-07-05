<?php

namespace App\Services\Admin;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Book;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getStats(string $period = 'week'): array
    {
        $dateRange = $this->getDateRange($period);

        $totalSales = Payment::where('status', 'completed')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->sum('amount');

        $totalOrders = Order::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count();
        
        // Actionable Metric: Immediately flags fulfillment backlog
        $pendingOrders = Order::where('status', 'pending')->count();

        $newCustomers = Customer::whereBetween('created_at', [$dateRange['start'], $dateRange['end']])->count();
        
        // Relies on configuration instead of hardcoded magic numbers
        $lowStockThreshold = config('shop.low_stock_threshold', 5);
        $lowStock = Book::where('stock_quantity', '<=', $lowStockThreshold)
            ->where('stock_quantity', '>', 0)
            ->count();
            
        $outOfStock = Book::where('stock_quantity', '<=', 0)->count();

        return [
            'total_sales' => $totalSales,
            'total_orders' => $totalOrders,
            'pending_orders' => $pendingOrders,
            'new_customers' => $newCustomers,
            'low_stock' => $lowStock,
            'out_of_stock' => $outOfStock,
            'period' => $period,
            'period_label' => $dateRange['label'],
        ];
    }

    public function getChartData(string $period = 'week'): array
    {
        $dateRange = $this->getDateRange($period);
        
        // 1. Fetch grouped data from DB
        // We group by a format string so the DB does the heavy lifting
        $format = ($period === 'year') ? '%Y-%m' : '%Y-%m-%d';
        $sales = Payment::select(
                DB::raw("DATE_FORMAT(created_at, '$format') as date_group"), 
                DB::raw('SUM(amount) as total')
            )
            ->where('status', 'completed')
            ->whereBetween('created_at', [$dateRange['start'], $dateRange['end']])
            ->groupBy('date_group')
            ->pluck('total', 'date_group');

        $data = [];
        $current = $dateRange['start']->copy();
        $end = $dateRange['end']->copy();
        $max = 1;

        // 2. Dynamic Interval Loop
        while ($current->lte($end)) {
            $key = ($period === 'year') ? $current->format('Y-m') : $current->format('Y-m-d');
            $total = (float) ($sales->get($key, 0));
            
            if ($total > $max) $max = $total;

            $data[] = [
                'label' => ($period === 'year') ? $current->format('M') : $current->format('D'),
                'total' => $total,
            ];

            // INCREMENT LOGIC: Change unit based on period
            if ($period === 'year') {
                $current->addMonth();
            } else {
                $current->addDay();
            }
        }

        return array_map(fn($item) => [...$item, 'height' => ($item['total'] / $max) * 100], $data);
    }

    public function getRecentOrders(int $limit = 5): array
    {
        // Memory safety: strict column selection to prevent hydrating unnecessary data
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
    }

    /**
     * Actionable Metric: Identifies fast-moving inventory.
     * Assumes an `order_items` table exists with `book_id` and `quantity`.
     */
    public function getTopSellingBooks(int $limit = 5): array
    {
        return Book::select('books.id', 'books.title', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->join('order_items', 'books.id', '=', 'order_items.book_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', 'completed')
            ->groupBy('books.id', 'books.title')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    private function getDateRange(string $period): array
    {
        $now = Carbon::now();

        return match ($period) {
            'month' => [
                'start' => $now->copy()->startOfMonth(),
                'end' => $now,
                'label' => 'This Month',
            ],
            'year' => [
                'start' => $now->copy()->startOfYear(),
                'end' => $now,
                'label' => 'This Year',
            ],
            default => [
                'start' => $now->copy()->startOfWeek(),
                'end' => $now,
                'label' => 'This Week',
            ],
        };
    }
}