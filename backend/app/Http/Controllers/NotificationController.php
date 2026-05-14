<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = AppNotification::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        $unreadCount = AppNotification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'data' => $notifications->map(fn (AppNotification $n) => [
                'id' => (string) $n->id,
                'title' => $n->title,
                'message' => $n->message,
                'type' => $n->type,
                'read' => $n->read_at !== null,
                'createdAt' => $n->created_at?->toISOString(),
            ]),
            'meta' => [
                'total' => $notifications->total(),
                'unread' => $unreadCount,
                'page' => $notifications->currentPage(),
                'perPage' => $notifications->perPage(),
            ],
        ]);
    }

    public function markRead(AppNotification $notification): JsonResponse
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->update(['read_at' => now()]);

        return response()->json(['message' => 'Notification marked as read']);
    }

    public function markAllRead(Request $request): JsonResponse
    {
        AppNotification::where('user_id', $request->user()->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['message' => 'All notifications marked as read']);
    }
}
