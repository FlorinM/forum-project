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
     * Display the specified discussion and its messages.
     *
     * This method retrieves all the messages associated with the given discussion
     * and also loads the related initiator and participant for the discussion.
     * The data is then passed to the front-end using Inertia.js to render the
     * 'Discussions/Discussion' component.
     *
     * @param  \App\Models\Discussion  $discussion  The discussion to be shown.
     * @return \Inertia\Response  The Inertia response to render the discussion page.
     */
    public function showDiscussion (Discussion $discussion) {
        $messages = Message::where('discussion_id', $discussion->id)->get();
        $discussion->load(['initiator', 'participant']);

        return Inertia::render('Discussions/Discussion', [
            'messages' => $messages,
            'discussion' => $discussion,
        ]);
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

        $latestMessages = DB::table('messages')
            ->select('discussion_id', DB::raw('MAX(created_at) as last_message_at'))
            ->groupBy('discussion_id');

        $inboxDiscussions = DB::table('discussions')
            ->joinSub($latestMessages, 'latest_messages', function ($join) {
                $join->on('discussions.id', '=', 'latest_messages.discussion_id');
            })
            ->leftJoin('users', 'discussions.initiator_id', '=', 'users.id')
            ->where('discussions.participant_id', $authUserId)
            ->orderBy('latest_messages.last_message_at', 'desc')
            ->take(50)
            ->get([
                'discussions.*',
                'users.nickname as initiator_nickname',
                'latest_messages.last_message_at',
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

        $latestMessages = DB::table('messages')
            ->select('discussion_id', DB::raw('MAX(created_at) as last_message_at'))
            ->groupBy('discussion_id');

        $sentDiscussions = DB::table('discussions')
            ->joinSub($latestMessages, 'latest_messages', function ($join) {
                $join->on('discussions.id', '=', 'latest_messages.discussion_id');
            })
            ->leftJoin('users', 'discussions.participant_id', '=', 'users.id')
            ->where('discussions.initiator_id', $authUserId)
            ->orderBy('latest_messages.last_message_at', 'desc')
            ->take(50)
            ->get([
                'discussions.*',
                'users.nickname as participant_nickname',
                'latest_messages.last_message_at',
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
