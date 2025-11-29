<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notification;

class NotificationController extends Controller
{
    /**
     * Display a listing of the notifications for the authenticated user.
     */
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('notifications.index', compact('notifications'));
    }
    
    /**
     * Mark a notification as read.
     */
    public function markAsRead(Notification $notification)
    {
        // Verify the notification belongs to the authenticated user
        if ($notification->user_id != Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $notification->update(['is_read' => true]);
        
        return response()->json(['success' => true]);
    }
    
    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->update(['is_read' => true]);
            
        return response()->json(['success' => true]);
    }
    
    /**
     * Get unread notifications count for the authenticated user.
     */
    public function unreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();
            
        return response()->json(['count' => $count]);
    }
}