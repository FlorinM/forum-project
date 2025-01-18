<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Thread;
use App\Models\Post;
use Inertia\Inertia;
use App\Http\Requests\StoreThreadRequest;

class ThreadController extends BaseServiceController
{
    /**
     * Show the form to create a new thread.
     *
     * @param int $categoryId The category ID for the new thread
     * @return \Inertia\Response The Inertia response with the category
     */
    public function create($categoryId)
    {
        // Find the category by ID
        $category = Category::findOrFail($categoryId);

        // Return the Inertia view to create a new thread, passing the category
        return Inertia::render('Threads/Create', [
            'category' => $category,
        ]);
    }

    /**
     * Store a newly created thread and its first post.
     *
     * @param StoreThreadRequest $request The validated request data
     * @param int $categoryId The category ID to associate with the thread
     * @return \Illuminate\Http\RedirectResponse Redirect to the new thread page
     */
    public function store(StoreThreadRequest $request, $categoryId)
    {
        $authUser = auth()->user();

        // Check if the user is banned
        if ($authUser->isBanned()) {
            // Get the ban expiration message
            $banMessage = "You are banned from creating new threads. Your ban will be lifted on " . $authUser->getBanDuration();

            // Redirect back with the ban message
            return back()->with([
                'banMessage' => $banMessage,
            ]);
        }

        // At this point the input is validated
        $thread = Thread::create([
            'category_id' => $categoryId,
            'user_id' => auth()->id(),
            'title' => $request->get('title'),
            'content' => $request->get('content'), // This is for the thread description
        ]);

        // Now create the first post for this thread
        if (config('quill.use_image_handler')) {
            // Extract images from the string and replace with urls
            $postContent = $this->imageExtractorService->extractAndReplaceImages($request->input('postContent'));
        } else {
            $postContent = $request->input('postContent');
        }

        // Sanitize the content using the service
        $postContent = $this->sanitizationService->sanitize($postContent);
        Post::create([
            'thread_id' => $thread->id,
            'user_id' => auth()->id(),
            'content' => $postContent,
        ]);

        // Redirect or return response
        return redirect()->route('threads.show', [$categoryId, $thread->id])
            ->with('success', 'Thread created successfully');
    }
}
