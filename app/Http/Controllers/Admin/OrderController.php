<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use App\Models\Notification;
use App\Models\Order;
use App\Services\Admin\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display all orders with optional status filter.
     */
    public function index(Request $request)
    {
        $status = $request->get('status');
        $orders = $this->orderService->getAll($status);
        $counts = $this->orderService->getCounts();

        return view('admin.orders.index', compact('orders', 'counts', 'status'));
    }

    /**
     * Show order details.
     */
    public function show(Order $order)
    {
        $order = $this->orderService->getDetail($order);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
        ]);

        $this->orderService->updateStatus($order, $request->status);
        \App\Models\Notification::create([
            "recipient_type" => "App\\Models\\Customer",
            "recipient_id" => $order->customer_id,
            "type" => "order_status",
            "title" => "Order #" . $order->order_number . " is " . ucfirst($request->status),
            "message" => "Your order has been " . ucfirst($request->status) . ".",
            "notifiable_type" => "App\\Models\\Order",
            "notifiable_id" => $order->id,
        ]);
        // Notify customer about status change

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', 'Order status updated to ' . ucfirst($request->status) . '.');
    }
}