<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = $request->user()
            ->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('notifications.index', [
            'notifications' => $notifications,
        ]);
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
        $notification->update(['read_at' => now()]);
        return back()->with('success', 'Notificação marcada como lida.');
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()
            ->notifications()
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
            
        return back()->with('success', 'Todas as notificações foram marcadas como lidas.');
    }
}

