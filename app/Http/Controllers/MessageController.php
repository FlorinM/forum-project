<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Requests\SendMessageRequest;

class MessageController extends BaseServiceController
{
    /**
     * Send a message from the authenticated user to another user.
     *
     * @param  App\Http\Requests\SendMessageRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendMessage(SendMessageRequest $request)
    {
        $authUser = auth()->user();

        // Check if the user is banned
        if ($authUser->isBanned()) {
            // Get the ban expiration message
            $banMessage = "You are banned from sending messages. Your ban will be lifted on " . $authUser->getBanDuration();

            // Redirect back with the ban message
            return back()->with([
                'banMessage' => $banMessage,
            ]);
        }

        if (config('quill.use_image_handler')) {
            // Extract images from the string and replace with urls
            $message = $this->imageExtractorService->extractAndReplaceImages($request->input('message'));
        } else {
            $message = $request->input('message');
        }

        // Sanitize the content using the service
        $message = $this->sanitizationService->sanitize($message);

        // Create the message
        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->input('receiver_id'),
            'discussion_id' => $request->input('discussion_id'),
            'message' => $message,
        ]);

        // Optionally notify the receiver (e.g., real-time or email notifications)

        // Return the response
        return back();
    }
}
