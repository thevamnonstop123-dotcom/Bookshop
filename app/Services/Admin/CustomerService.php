<?php

namespace App\Services\Admin;

use App\Models\Customer;

class CustomerService
{
    /**
     * Get all customers with order count.
     */
    public function getAll()
    {
        return Customer::withCount('orders')
            ->latest()
            ->get();
    }

    /**
     * Get customer details with addresses and orders.
     */
    public function getDetail(Customer $customer): Customer
    {
        return $customer->load(['addresses', 'orders' => function ($query) {
            $query->latest()->limit(10);
        }]);
    }

    /**
     * Update customer status (active/inactive/banned).
     */
    public function updateStatus(Customer $customer, string $status): Customer
    {
        $customer->update(['status' => $status]);

        return $customer;
    }
}