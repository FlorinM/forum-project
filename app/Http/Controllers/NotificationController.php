<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
}
