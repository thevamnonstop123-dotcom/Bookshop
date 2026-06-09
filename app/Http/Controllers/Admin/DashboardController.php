<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\DashboardService;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Show admin dashboard with real statistics.
     */
    public function index()
    {
        $stats = $this->dashboardService->getStats();
        $chartData = $this->dashboardService->getWeeklySales();

        return view('admin.dashboard', compact('stats', 'chartData'));
    }
}