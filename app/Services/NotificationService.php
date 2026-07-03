<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Model;

class NotificationService
{
    /**
     * Create a notification for staff with specific roles.
     *
     * @param array $roles - Role names that should receive this (e.g., ['Super Admin', 'Order Manager'])
     * @param string $type - e.g., 'new_order', 'low_stock', 'out_of_stock', 'new_review', 'new_customer', 'order_shipped', 'order_delivered'
     * @param string $title
     * @param string $message
     * @param Model|null $notifiable - The related model (Order, Book, Review, etc.)
     */
    public static function send(array $roles, string $type, string $title, string $message, ?Model $notifiable = null): void
    {
        $staffIds = Staff::whereHas('role', function ($q) use ($roles) {
            $q->whereIn('name', $roles);
        })->pluck('id');

        foreach ($staffIds as $staffId) {
            Notification::create([
                'recipient_type' => Staff::class,
                'recipient_id' => $staffId,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'notifiable_type' => $notifiable ? get_class($notifiable) : null,
                'notifiable_id' => $notifiable ? $notifiable->id : null,
            ]);
        }
    }

    /**
     * Role presets for different notification types.
     */
    public static function orderRoles(): array
    {
        return ['Super Admin', 'Order Manager'];
    }

    public static function bookRoles(): array
    {
        return ['Super Admin', 'Content Manager'];
    }

    public static function reviewRoles(): array
    {
        return ['Super Admin', 'Content Manager'];
    }

    public static function customerRoles(): array
    {
        return ['Super Admin'];
    }
}
