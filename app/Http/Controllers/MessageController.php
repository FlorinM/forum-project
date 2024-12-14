<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Send a message from the authenticated user to another user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function sendMessage(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'receiver_id' => ['required', 'exists:users,id', 'not_in:' . auth()->id()],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        // Create the message
        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $validated['receiver_id'],
            'message' => $validated['message'],
        ]);

        // Optionally notify the receiver (e.g., real-time or email notifications)

        // Return the response
        return response()->json([
            'success' => true,
            'message' => $message,
        ], 201);
    }
}
