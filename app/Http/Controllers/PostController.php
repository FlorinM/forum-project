<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Thread;
use App\Models\Post;
use Inertia\Inertia;
use App\Http\Requests\StorePostRequest;
use App\Services\SanitizationService;

class PostController extends Controller
{
    protected $sanitizationService;

    public function __construct(SanitizationService $sanitizationService)
    {
        $this->sanitizationService = $sanitizationService;
    }

    /**
     * Display the posts for a given thread in a category.
     *
     * @param Category $category The category to which the thread belongs
     * @param Thread $thread The thread to display posts for
     * @return \Inertia\Response The Inertia response with the category, thread, and posts
     */
    public function show (Category $category, Thread $thread)
    {
        $posts = $thread->posts()->with('user')->get(); // Eager load the user relationship

        return Inertia::render('Posts/Show', [
            'category' => $category,
            'thread' => $thread,
            'posts' => $posts
        ]);
    }

    /**
     * Store a new reply for a thread.
     *
     * @param Request $request The incoming HTTP request
     * @param Category $category The category of the thread
     * @param Thread $thread The thread to which the reply belongs
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StorePostRequest $request, Category $category, Thread $thread)
    {
        // Sanitize the content using the service
        $sanitizedContent = $this->sanitizationService->sanitize($request->input('content'));

        // Create the new post
        $thread->posts()->create([
            'user_id' => auth()->id(), // Use the currently authenticated user
            'thread_id' => $thread->id,
            'content' => $sanitizedContent,
        ]);

        return back(); // Redirect back to the thread view
    }
}
