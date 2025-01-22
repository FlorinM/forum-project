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
        if ($redirect = $this->handleBannedUser($authUser, "You are banned from sending messages.")) {
            return $redirect;
        }

        // Check if the user has the permission to send new messages
        if (!$authUser->can('send_message')) {
            return back()->with([
                'errorSendMessage' => 'You are not ready to send private messages.',
            ]);
        }

        // Prepare and sanitize the message content
        $messageContent = $this->prepareMessageContent($request->input('message'));

        // Save the message
        $this->createMessage($request, $messageContent);

        // Optionally notify the receiver (e.g., real-time or email notifications)

        // Return the response
        return back();
    }

    /**
     * Handle banned user logic.
     *
     * @param User $authUser The authenticated user.
     * @param string $banMessage The ban message to display.
     * @return \Illuminate\Http\RedirectResponse|null
     */
    private function handleBannedUser(User $authUser, string $banMessage)
    {
        return $this->userService->ifBanned($authUser, $banMessage);
    }

    /**
     * Prepare and sanitize the message content.
     *
     * @param string $message The raw message content.
     * @return string The sanitized and processed message.
     */
    private function prepareMessageContent(string $message): string
    {
        if (config('quill.use_image_handler')) {
            $message = $this->imageExtractorService->extractAndReplaceImages($message);
        }

        return $this->sanitizationService->sanitize($message);
    }

    /**
     * Create the message and save it to the database.
     *
     * @param SendMessageRequest $request The request containing message data.
     * @param string $messageContent The sanitized message content.
     * @return void
     */
    private function createMessage(SendMessageRequest $request, string $messageContent): void
    {
        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->input('receiver_id'),
            'discussion_id' => $request->input('discussion_id'),
            'message' => $messageContent,
        ]);
    }
}
