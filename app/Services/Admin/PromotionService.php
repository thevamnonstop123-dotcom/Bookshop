<?php

namespace App\Services\Admin;

use App\Models\Customer;
use App\Models\PromotionEmail;
use Illuminate\Support\Facades\Mail;

class PromotionService
{
    /**
     * Get all sent promotions.
     */
    public function getAll()
    {
        return PromotionEmail::with('sentBy')->latest()->get();
    }

    /**
     * Send promotion email to all active customers.
     * Now dispatches in background via queue.
     */
    public function send(string $subject, string $message, int $staffId): PromotionEmail
    {
        $customers = Customer::where('status', 'active')->get();
        $count = $customers->count();

        // Dispatch email jobs to queue (background)
        foreach ($customers as $customer) {
            \App\Jobs\SendPromotionEmail::dispatch($customer, $subject, $message);
        }

        return PromotionEmail::create([
            'subject'          => $subject,
            'message'          => $message,
            'sent_by'          => $staffId,
            'recipients_count' => $count,
            'sent_at'          => now(),
        ]);
    }
}