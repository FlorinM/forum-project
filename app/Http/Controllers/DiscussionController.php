<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use App\Models\Message;
use App\Http\Requests\StartDiscussionRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DiscussionController extends BaseServiceController
{
    //
    public function show() {
        return Inertia::render('Discussions/MyMessages');
    }

    /**
     * Fetches the inbox discussions for the authenticated user.
     * Joins discussions with messages and users to include the last message's order
     * and the initiator's nickname. Returns the discussions ordered by the latest message.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function inbox() {
        $authUserId = Auth::id();

        $inboxDiscussions = DB::table('discussions')
            ->leftJoin('messages', 'discussions.id', '=', 'messages.discussion_id')
            ->leftJoin('users', 'discussions.initiator_id', '=', 'users.id')
            ->where('discussions.participant_id', $authUserId)
            ->orderBy('messages.created_at', 'desc')
            ->take(50)
            ->get([
                'discussions.*',
                'users.nickname as initiator_nickname', // Select the nickname
            ]);

        return response()->json([
            'fetchedData' => $inboxDiscussions,
        ]);
    }

    /**
     * Fetches the discussions initiated by the authenticated user.
     * Joins discussions with messages and users to include the last message's order
     * and the participant's nickname. Returns the discussions ordered by the latest message.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function sent() {
        $authUserId = Auth::id();

        $sentDiscussions = DB::table('discussions')
            ->leftJoin('messages', 'discussions.id', '=', 'messages.discussion_id')
            ->leftJoin('users', 'discussions.participant_id', '=', 'users.id')
            ->where('discussions.initiator_id', $authUserId)
            ->orderBy('messages.created_at', 'desc')
            ->take(50)
            ->get([
                'discussions.*',
                'users.nickname as participant_nickname', // Select the nickname
            ]);

        return response()->json([
            'fetchedData' => $sentDiscussions,
        ]);
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
