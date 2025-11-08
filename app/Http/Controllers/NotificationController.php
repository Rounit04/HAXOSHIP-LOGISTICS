<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    /**
     * Get all notifications (AJAX)
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = Notification::orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        $unreadCount = Notification::where('read', false)->count();

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id): JsonResponse
    {
        $notification = Notification::findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'unread_count' => Notification::where('read', false)->count(),
        ]);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead(): JsonResponse
    {
        Notification::where('read', false)->update([
            'read' => true,
            'read_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }

    /**
     * Get unread count (for polling)
     */
    public function getUnreadCount(): JsonResponse
    {
        $count = Notification::where('read', false)->count();
        
        return response()->json([
            'unread_count' => $count,
        ]);
    }
}
