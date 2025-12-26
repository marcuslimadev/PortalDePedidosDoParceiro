<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return response()->json($notifications);
    }

    public function unread(Request $request)
    {
        $count = $request->user()
            ->notifications()
            ->unread()
            ->count();
            
        return response()->json(['count' => $count]);
    }

    public function markAsRead(Notification $notification)
    {
        $notification->markAsRead();
        return response()->json(['success' => true]);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()
            ->notifications()
            ->unread()
            ->update(['read_at' => now()]);
            
        return response()->json(['success' => true]);
    }
}

