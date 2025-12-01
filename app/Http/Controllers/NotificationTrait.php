<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notification as NotificationModel;

trait NotificationTrait
{
    /**
     * Send notification to admins
     */
    protected function notifyAdmins($type, $message, $relatedId = null, $relatedType = null)
    {
        // Get all admin users (you might need to adjust this based on how admins are identified)
        $admins = User::where('email', 'madhuram.motors@gmail.com')->get(); // Adjust this condition as needed
        
        foreach ($admins as $admin) {
            NotificationModel::create([
                'user_id' => $admin->id,
                'type' => $type,
                'related_id' => $relatedId,
                'related_type' => $relatedType,
                'message' => $message,
                'is_read' => false
            ]);
        }
    }
    
    /**
     * Send notification to a specific user
     */
    protected function notifyUser($userId, $type, $message, $relatedId = null, $relatedType = null)
    {
        NotificationModel::create([
            'user_id' => $userId,
            'type' => $type,
            'related_id' => $relatedId,
            'related_type' => $relatedType,
            'message' => $message,
            'is_read' => false
        ]);
    }
}