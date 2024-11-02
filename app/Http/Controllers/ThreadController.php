<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Thread;
use App\Models\Post;
use Inertia\Inertia;
use App\Http\Requests\StoreThreadRequest;

class ThreadController extends Controller
{
    public function show(Category $category)
    {
        $threads = $category->threads()->with('user')->get(); // Eager load the user relationship
        return Inertia::render('Threads/Show', [
            'category' => $category,
            'threads' => $threads,
        ]);
    }

    public function create($categoryId)
    {
        // Find the category by ID
        $category = Category::findOrFail($categoryId);

        // Return the Inertia view to create a new thread, passing the category
        return Inertia::render('Threads/Create', [
            'category' => $category,
        ]);
    }

    public function store(StoreThreadRequest $request, $categoryId)
    {
        // At this point the input is validated
        $thread = Thread::create([
            'category_id' => $categoryId,
            'user_id' => auth()->id(),
            'title' => $request->get('title'),
            'content' => $request->get('content'), // This is for the thread description
        ]);

        // Now create the first post for this thread
        Post::create([
            'thread_id' => $thread->id,
            'user_id' => auth()->id(),
            'content' => $request->get('postContent'), // Use postContent to match the input
        ]);

        // Redirect or return response
        return redirect()->route('threads.show', [$categoryId, $thread->id])
            ->with('success', 'Thread created successfully');
    }
}
