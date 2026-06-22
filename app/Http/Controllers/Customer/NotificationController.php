<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Notification;

class NotificationController extends Controller
{
    /**
     * Get unread notifications for the logged-in customer.
     */
    public function index()
    {
        $notifications = Notification::where('customer_id', auth('customer')->id())
            ->latest()
            ->limit(10)
            ->get();

        $unreadCount = Notification::where('customer_id', auth('customer')->id())
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
        $notification = Notification::where('customer_id', auth('customer')->id())
            ->findOrFail($id);
        
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead()
    {
        Notification::where('customer_id', auth('customer')->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}