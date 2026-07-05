<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\DashboardService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService
    ) {}

    /**
     * Display the admin dashboard.
     */
    public function index(Request $request)
    {
        $period = $request->query('period', 'week');
        
        if (!in_array($period, ['week', 'month', 'year'], true)) {
            $period = 'week';
        }

        $stats = $this->dashboardService->getStats($period);
        $chartData = $this->dashboardService->getChartData($period);
        $recentOrders = $this->dashboardService->getRecentOrders();
        $topSellingBooks = $this->dashboardService->getTopSellingBooks();

        return view('admin.dashboard', compact(
            'stats', 
            'chartData', 
            'recentOrders', 
            'topSellingBooks',
            'period'
        ));
    }

    /**
     * API endpoint for real-time dashboard data.
     */
    public function realtimeData(Request $request)
    {
        $period = $request->query('period', 'week');
        
        if (!in_array($period, ['week', 'month', 'year'], true)) {
            $period = 'week';
        }

        // Clear cache for real-time data
        $this->dashboardService->clearCache();
        
        return response()->json([
            'stats' => $this->dashboardService->getStats($period),
            'recentOrders' => $this->dashboardService->getRecentOrders(),
            'timestamp' => now()->toISOString(),
        ]);
    }
}
