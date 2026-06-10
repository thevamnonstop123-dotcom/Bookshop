<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\PromotionService;
use Illuminate\Http\Request;

class PromotionController extends Controller
{
    protected PromotionService $promotionService;

    public function __construct(PromotionService $promotionService)
    {
        $this->promotionService = $promotionService;
    }

    /**
     * Show form and history.
     */
    public function index()
    {
        $promotions = $this->promotionService->getAll();
        $activeCustomersCount = \App\Models\Customer::where('status','active')->count();

        return view('admin.promotions.index', compact('promotions', 'activeCustomersCount'));
    }

    /**
     * Send promotion email.
     */
    public function send(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:200',
            'message' => 'required|string|max:5000',
        ]);

        $this->promotionService->send(
            $request->subject,
            $request->message,
            auth('staff')->id()
        );

        return back()->with('success', 'Promotion email sent to all active customers!');
    }
}