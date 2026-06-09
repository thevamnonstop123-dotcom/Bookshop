<?php

namespace App\Services\Admin;

use App\Models\Payment;

class PaymentService
{
    /**
     * Get all payments with order and customer info.
     */
    public function getAll(?string $status = null, ?string $method = null)
    {
        return Payment::with(['order.customer'])
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($method, function ($query, $method) {
                $query->where('payment_method', $method);
            })
            ->latest()
            ->get();
    }

    /**
     * Get payment statistics.
     */
    public function getStats(): array
    {
        return [
            'total_amount'    => Payment::where('status', 'completed')->sum('amount'),
            'total_count'     => Payment::count(),
            'completed_count' => Payment::where('status', 'completed')->count(),
            'pending_count'   => Payment::where('status', 'pending')->count(),
            'failed_count'    => Payment::where('status', 'failed')->count(),
            'refunded_count'  => Payment::where('status', 'refunded')->count(),
        ];
    }
}