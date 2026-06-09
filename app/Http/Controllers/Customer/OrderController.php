<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Services\Customer\OrderService;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Show customer's orders.
     */
    public function index()
    {
        $orders = $this->orderService->getOrders(auth('customer')->id());

        return view('customer.orders.index', compact('orders'));
    }

    /**
     * Show order detail.
     */
    public function show($orderId)
    {
        $order = $this->orderService->getDetail($orderId, auth('customer')->id());

        return view('customer.orders.show', compact('order'));
    }
}