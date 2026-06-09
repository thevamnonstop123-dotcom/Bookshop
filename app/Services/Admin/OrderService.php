<?php

namespace App\Services\Admin;

use App\Models\Order;

class OrderService
{
    /**
     * Get all orders with customer and payment info.
     */
    public function getAll(?string $status = null)
    {
        return Order::with(['customer', 'payment'])
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->latest()
            ->get();
    }

    /**
     * Get order details with all relationships.
     */
    public function getDetail(Order $order): Order
    {
        return $order->load([
            'customer',
            'items.book',
            'shippingAddress',
            'payment',
        ]);
    }

    /**
     * Update order status.
     */
    public function updateStatus(Order $order, string $status): Order
    {
        $order->update(['status' => $status]);

        return $order;
    }

    /**
     * Get order counts by status for dashboard.
     */
    public function getCounts(): array
    {
        return [
            'total'      => Order::count(),
            'pending'    => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped'    => Order::where('status', 'shipped')->count(),
            'delivered'  => Order::where('status', 'delivered')->count(),
            'cancelled'  => Order::where('status', 'cancelled')->count(),
        ];
    }
}