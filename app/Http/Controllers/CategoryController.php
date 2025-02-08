<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * Show the subcategories and threads of a given category.
     *
     * @param Category $category The category for which to show subcategories and threads
     * @return \Inertia\Response The Inertia response with subcategories and threads
     */
    public function showSubcategories(Category $category)
    {
        // Fetch the subcategories (immediate subcategories)
        $subcategories = $category->subcategories;

        // Fetch the threads for the current category (eager load the user for each thread)
        $threads = $category->threads()->with('user')->get();

        // Initialize an array to hold the latest posts for each subcategory
        $latestPosts = [];

        // Loop through each subcategory to fetch the latest post data
        foreach ($subcategories as $index => $subcategory) {
            // Get the latest post for the current subcategory (by created_at)
            $latestPost = \App\Models\Post::whereHas('thread', function ($query) use ($subcategory) {
                $query->where('category_id', $subcategory->id);
            })
            ->latest('created_at') // Get the latest post
            ->first(); // Get only the first (most recent) post

            // Lazy load the user and thread relationships for the latest post
            if ($latestPost) {
                $latestPost->load('thread', 'user'); // Lazy load thread and user relationships
            }

            // Add the latest post to the $latestPosts array, synchronized with $subcategories
            $latestPosts[$index] = $latestPost;
        }

        // Initialize an array to hold the latest posts for each thread in current category
        $latestPostInThreads = [];

        foreach ($threads as $thread) {
            // Get the latest post for the current thread with user relation
            $latestPost = \App\Models\Post::where('thread_id', $thread->id)
                ->latest('created_at')
                ->with('user')
                ->first();

            // Store in the array with thread ID as the key
            $latestPostInThreads[] = $latestPost;
        }

        // Return the view with subcategories, threads, and the latest post data for each subcategory
        return Inertia::render('Categories/Subcategories', [
            'category' => $category,
            'subcategories' => $subcategories,
            'threads' => $threads,  // Keep the threads as is
            'latestPosts' => $latestPosts,  // Send the latest post data for each subcategory
            'latestPostInThreads' => $latestPostInThreads, // Send the latest post for each thread in current category
        ]);
    }
}
