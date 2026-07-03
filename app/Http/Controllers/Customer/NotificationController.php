<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Notification;

class NotificationController extends Controller
{
    /**
     * Get notifications for the logged-in customer.
     */
    public function index()
    {
        $customerId = auth('customer')->id();
        
        $notifications = Notification::where(function ($q) use ($customerId) {
                $q->where('recipient_type', 'App\\Models\\Customer')
                  ->where('recipient_id', $customerId);
            })
            ->orWhere('customer_id', $customerId) // backward compatibility
            ->latest()
            ->limit(10)
            ->get();

        $unreadCount = Notification::where(function ($q) use ($customerId) {
                $q->where('recipient_type', 'App\\Models\\Customer')
                  ->where('recipient_id', $customerId);
            })
            ->orWhere('customer_id', $customerId)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markRead($id)
    {
        $notification = Notification::findOrFail($id);
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead()
    {
        $customerId = auth('customer')->id();
        
        Notification::where(function ($q) use ($customerId) {
                $q->where('recipient_type', 'App\\Models\\Customer')
                  ->where('recipient_id', $customerId);
            })
            ->orWhere('customer_id', $customerId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}
