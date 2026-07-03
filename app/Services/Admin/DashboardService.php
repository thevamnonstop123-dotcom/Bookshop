<?php

namespace App\Services\Admin;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Book;
use App\Models\Payment;
use Carbon\Carbon;

class DashboardService
{
    /**
     * Get dashboard statistics.
     */
    public function getStats(): array
    {
        return [
            'total_sales'    => Payment::where('status', 'completed')->sum('amount'),
            'total_orders'   => Order::count(),
            'total_customers'=> Customer::count(),
            'low_stock'      => Book::where('stock_quantity', '<=', 5)->where('stock_quantity', '>', 0)->count(),
        ];
    }

    /**
     * Get weekly sales data for chart.
     */
    public function getWeeklySales(): array
    {
        $data = [];
        $startOfWeek = Carbon::now()->startOfWeek();

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);
            $dayName = $date->format('D');

            $total = Payment::where('status', 'completed')
                ->whereDate('created_at', $date)
                ->sum('amount');

            $data[] = [
                'day'   => $dayName,
                'total' => $total,
            ];
        }

        // Find max for percentage height
        $max = max(array_column($data, 'total')) ?: 1;

        foreach ($data as &$item) {
            $item['height'] = ($item['total'] / $max) * 100;
        }

        return $data;
    }
}