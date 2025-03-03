<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Models\Discussion;
use Illuminate\Http\Request;
use App\Http\Requests\SendMessageRequest;
use App\Notifications\PrivateMessageNotification;

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

        // Check if the discussion is blocked
        if ($this->isDiscussionBlocked($request)) {
            return back()->with([
                'errorSendMessage' => 'This discussion is blocked. To continue, you must unblock the other participant or wait to be unblocked.',
            ]);
        }

        // Prepare and sanitize the message content
        $messageContent = $this->prepareMessageContent($request->input('message'));

        // Save the message
        $message = $this->createMessage($request, $messageContent);

        // Notify the receiver
        $receiver = User::find($message->receiver_id);
        $sender = User::find($message->sender_id);
        if ($receiver && $sender) {
            $receiver->notify(new PrivateMessageNotification($message, $sender));
        }

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
     * @return App\Models\Message
     */
    private function createMessage(SendMessageRequest $request, string $messageContent): Message
    {
        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->input('receiver_id'),
            'discussion_id' => $request->input('discussion_id'),
            'message' => $messageContent,
        ]);

        return $message;
    }

    /**
     * Check if one of the discussion participants is blocked by the other.
     *
     * @param Request $request The request containing the discussion ID.
     * @return bool True if one participant has blocked the other, false otherwise.
     */
    protected function isDiscussionBlocked(Request $request): bool
    {
        $discussion = Discussion::findOrFail($request->input('discussion_id'));

        $initiator = User::find($discussion->initiator_id);
        $participant = User::find($discussion->participant_id);

        if (!$initiator || !$participant) {
            return false;
        }

        return $this->userService->hasBlocked($initiator, $participant) ||
               $this->userService->hasBlocked($participant, $initiator);
    }
}
