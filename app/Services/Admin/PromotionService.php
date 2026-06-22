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
     */
    public function send(string $subject, string $message, int $staffId): PromotionEmail
    {
        $customers = Customer::where('status', 'active')->get();
        $count = 0;

        foreach ($customers as $customer) {
            try {
                Mail::send('admin.promotions.email-template', [
                    'name' => $customer->name,
                    'subject' => $subject,
                    'content' => $message,
                ], function ($mail) use ($customer, $subject) {
                    $mail->to($customer->email, $customer->name)
                         ->subject($subject);
                });
                $count++;
            } catch (\Exception $e) {
                // Skip failed emails
            }

            // Create notification for this customer
            \App\Models\Notification::create([
                'customer_id' => $customer->id,
                'type' => 'promotion',
                'title' => $subject,
                'message' => $message,
            ]);
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