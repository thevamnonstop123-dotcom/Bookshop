<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('recipient_type', 'App\Models\Staff')
            ->latest()
            ->limit(20)
            ->get()
            ->map(function ($n) {
                return [
                    'id' => $n->id,
                    'type' => $n->type,
                    'title' => $n->title,
                    'message' => $n->message,
                    'read_at' => $n->read_at,
                    'url' => $n->url,
                    'created_at' => $n->created_at->diffForHumans(),
                ];
            });

        $unreadCount = Notification::where('recipient_type', 'App\Models\Staff')
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function count()
    {
        $count = Notification::where('recipient_type', 'App\Models\Staff')
            ->whereNull('read_at')
            ->count();

        return response()->json(['count' => $count]);
    }

    public function markAllRead()
    {
        Notification::where('recipient_type', 'App\Models\Staff')
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }
}
