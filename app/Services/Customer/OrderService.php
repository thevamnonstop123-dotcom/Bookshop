<?php

namespace App\Services\Customer;

use App\Models\Order;

class OrderService
{
    /**
     * Get customer's orders with payment info.
     */
    public function getOrders(int $customerId)
    {
        return Order::with(['payment', 'items.book'])
            ->where('customer_id', $customerId)
            ->latest()
            ->paginate(10);
    }

    /**
     * Get order detail.
     */
    public function getDetail(int $orderId, int $customerId): Order
    {
        return Order::with(['items.book', 'shippingAddress', 'payment'])
            ->where('id', $orderId)
            ->where('customer_id', $customerId)
            ->firstOrFail();
    }
}