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

        // Check if the user is banned using UserService
        if ($redirect = $this->userService->ifBanned($authUser, "You are banned from creating new threads.")) {
            return $redirect;
        }

        // Check if the user is a "NewUser" and if their posts/threads are approved
        if ($authUser->hasRole('NewUser') &&
            ($this->userService->hasUnapprovedPosts($authUser) ||
             $this->userService->hasUnapprovedThreads($authUser))
        ) {
            return back()->with([
                'errorNewUserMessage' => 'You cannot start new threads until your previous post/thread is approved.',
            ]);
        }

        // Create the thread
        $thread = $this->createThread($authUser, $request, $categoryId);

        // Prepare and sanitize the first post content
        $postContent = $this->preparePostContent($request);

        // Create the first post for the thread
        $this->createFirstPost($authUser, $thread, $postContent);

        // Redirect to the thread view
        return $this->redirectToThread($categoryId, $thread);
    }

    /**
     * Create a new thread in the specified category.
     *
     * @param \App\Models\User $authUser The authenticated user
     * @param StoreThreadRequest $request The validated request data
     * @param int $categoryId The category ID
     * @return \App\Models\Thread The created thread
     */
    private function createThread($authUser, StoreThreadRequest $request, $categoryId): Thread
    {
        $approved = false;
        if (!auth()->user()->hasRole('NewUser')) {
            $approved = true;
        }

        return Thread::create([
            'category_id' => $categoryId,
            'user_id' => $authUser->id,
            'title' => $request->get('title'),
            'content' => $request->get('content'), // Thread description
            'approved' => $approved,
        ]);
    }

    /**
     * Prepare and sanitize the content for the first post.
     *
     * @param StoreThreadRequest $request The validated request data
     * @return string The sanitized content
     */
    private function preparePostContent(StoreThreadRequest $request): string
    {
        if (config('quill.use_image_handler')) {
            // Extract images and replace them with URLs
            $content = $this->imageExtractorService->extractAndReplaceImages($request->input('postContent'));
        } else {
            $content = $request->input('postContent');
        }

        // Sanitize the content
        return $this->sanitizationService->sanitize($content);
    }

    /**
     * Create the first post for the thread.
     *
     * @param \App\Models\User $authUser The authenticated user
     * @param \App\Models\Thread $thread The created thread
     * @param string $postContent The content of the first post
     * @return void
     */
    private function createFirstPost($authUser, Thread $thread, string $postContent): void
    {
        $approved = false;
        if (!auth()->user()->hasRole('NewUser')) {
            $approved = true;
        }

        Post::create([
            'thread_id' => $thread->id,
            'user_id' => $authUser->id,
            'content' => $postContent,
            'approved' => $approved,
        ]);
    }

    /**
     * Redirect to the newly created thread.
     *
     * @param int $categoryId The category ID
     * @param \App\Models\Thread $thread The created thread
     * @return \Illuminate\Http\RedirectResponse
     */
    private function redirectToThread(int $categoryId, Thread $thread)
    {
        return redirect()
            ->route('threads.show', [$categoryId, $thread->id])
            ->with('success', 'Thread created successfully');
    }

    /**
     * Retrieve the last 10 threads the authenticated user has posted in,
     * ordered by the latest post in each thread (regardless of the author).
     * Threads with new posts since the user's last visit are marked as bold.
     *
     * @return \Illuminate\Http\JsonResponse The JSON response containing the fetched threads
     */
    public function contentIFollow()
    {
        $authUser = auth()->user();

        // Get the last 10 threads where the user has posted
        $lastThreads = Thread::whereHas('posts', function ($query) use ($authUser) {
            $query->where('user_id', $authUser->id);
        })
        ->with(['posts' => function ($query) {
            $query->latest(); // Get latest posts in each thread
        }])
        ->orderByDesc(
            Post::select('created_at')
            ->whereColumn('thread_id', 'threads.id')
            ->latest()
            ->take(1)
        )
        ->limit(10)
        ->get();

        // Mark threads with new posts since last visit
        $lastVisit = $authUser->last_visit_at ?? now()->subYears(10); // Default if no last visit recorded

        $lastThreads->transform(function ($thread) use ($lastVisit) {
            $newestPost = $thread->posts->first(); // Get the latest post in this thread
            $thread->bold = $newestPost && $newestPost->created_at > $lastVisit ? true : false;
            return $thread;
        });

        return response()->json([
            'fetchedData' => $lastThreads,
        ]);
    }
}
