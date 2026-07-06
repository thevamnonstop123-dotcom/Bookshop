<?php

namespace App\Jobs;

use App\Models\Customer;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendPromotionEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Customer $customer;
    protected string $subject;
    protected string $message;

    public function __construct(Customer $customer, string $subject, string $message)
    {
        $this->customer = $customer;
        $this->subject = $subject;
        $this->message = $message;
    }

    public function handle(): void
    {
        Mail::send('mail.promotion', [
            'name' => $this->customer->name,
            'subject' => $this->subject,
            'body' => $this->message,
            'badge' => 'Special Offer',
            'ctaLink' => url('/books'),
            'ctaText' => 'Browse Books',
            'unsubscribeLink' => null,
        ], function ($mail) {
            $mail->to($this->customer->email, $this->customer->name)
                 ->subject($this->subject);
        });

        Notification::create([
            'customer_id' => $this->customer->id,
            'recipient_type' => 'App\Models\Customer',
            'recipient_id' => $this->customer->id,
            'type' => 'promotion',
            'title' => $this->subject,
            'message' => $this->message,
        ]);
    }
}
