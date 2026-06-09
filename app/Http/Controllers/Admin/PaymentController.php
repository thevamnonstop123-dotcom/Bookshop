<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Display all payments with optional filters.
     */
    public function index(Request $request)
    {
        $status = $request->get('status');
        $method = $request->get('method');
        $payments = $this->paymentService->getAll($status, $method);
        $stats = $this->paymentService->getStats();

        return view('admin.payments.index', compact('payments', 'stats', 'status', 'method'));
    }
}