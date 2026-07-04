<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Notification;

class NotificationController extends Controller
{
    private function customerQuery($customerId)
    {
        return Notification::where(function ($q) use ($customerId) {
            $q->where(function ($sq) use ($customerId) {
                $sq->where('recipient_type', 'App\\Models\\Customer')
                   ->where('recipient_id', $customerId);
            })->orWhere('customer_id', $customerId);
        });
    }

    public function index()
    {
        $customerId = auth('customer')->id();
        
        $notifications = $this->customerQuery($customerId)
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'type' => $n->type,
                    'title' => $n->title,
                    'message' => $n->message,
                    'url' => $n->url,
                    'read_at' => $n->read_at,
                    'created_at' => $n->created_at->diffForHumans(),
                ];
            });

        $unreadCount = $this->customerQuery($customerId)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function markRead($id)
    {
        Notification::findOrFail($id)->markAsRead();
        return response()->json(['success' => true]);
    }

    public function markAllRead()
    {
        $customerId = auth('customer')->id();
        
        $this->customerQuery($customerId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}
