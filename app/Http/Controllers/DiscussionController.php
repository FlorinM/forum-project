<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use App\Models\Message;
use App\Http\Requests\StartDiscussionRequest;
use Inertia\Inertia;

class DiscussionController extends BaseServiceController
{
    //
    public function inbox() {
        return Inertia::render('Discussions/Inbox');
    }

    /**
     * Store a newly created discussion.
     *
     * @param  \App\Http\Requests\StartDiscussionRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StartDiscussionRequest $request) {
        // At this point the input is already validated via Laravel Precognition
        // Create the discussion
        $discussion = Discussion::create([
            'initiator_id' => auth()->id(),
            'participant_id' => $request->input('receiver_id'),
            'subject' => $request->input('subject'),
            'initiator_deleted_at' => null,
            'participant_deleted_at' => null,
        ]);

        // If the input uses Quill editor with default image handler
        if (config('quill_use_image_handler')) {
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
            'discussion_id' => $discussion->id,
            'message' => $message,
        ]);

        return back();
    }
}
