<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * Get the unread notifications for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function unreadNotifications ()
    {
        return response()->json([
            'fetchedData' => auth()->user()->unreadNotifications,
        ]);
    }

    /**
     * Mark the notification as read.
     *
     * @param Illuminate\Notifications\DatabaseNotification $notification
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(DatabaseNotification $notification)
    {
        $notification->markAsRead();

        return response()->json([
            'message' => 'Notification marked as read',
        ]);
    }
}
