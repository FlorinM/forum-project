<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Post;
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
        // Fetch the subcategories and latest posts data
        $subcategories = $this->getSubcategories($category);

        // Fetch the threads for the current category with the latest post order
        $threads = $this->getThreads($category);

        // Fetch the latest posts for each subcategory
        $latestPosts = $this->getLatestPostsForSubcategories($subcategories);

        // Fetch the latest posts for each thread
        $latestPostInThreads = $this->getLatestPostsForThreads($threads);

        // Get the post count for each thread and add it to the response
        $postCounts = $this->getPostCountsForThreads($threads);

        // Get the thread count for each subcategory
        $threadCounts = $this->getThreadCountsForSubcategories($subcategories);

        // Return the view with the necessary data
        return Inertia::render('Categories/Subcategories', [
            'category' => $category,
            'subcategories' => $subcategories,
            'threads' => $threads,
            'latestPosts' => $latestPosts,
            'latestPostInThreads' => $latestPostInThreads,
            'postCounts' => $postCounts, // Send the number of posts for each thread
            'threadCounts' => $threadCounts, // Send the number of threads for each subcategory
            'postsPerPage' => 10, // The number of posts per page
        ]);
    }

    /**
     * Get the subcategories for the given category.
     *
     * @param Category $category
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getSubcategories(Category $category)
    {
        return $category->subcategories;
    }

    /**
     * Get the threads for the given category, ordered by the latest post's created_at.
     *
     * @param Category $category
     * @return \Illuminate\Database\Eloquent\Collection
     */
    private function getThreads(Category $category)
    {
        return $category
        ->threads()
        ->select('threads.*')
        ->addSelect(
            \DB::raw('(
                SELECT created_at
                FROM posts
                WHERE posts.thread_id = threads.id
                ORDER BY created_at DESC
                LIMIT 1
            ) AS latest_post_created_at')
        )
        ->orderByDesc('latest_post_created_at')
        ->with('user')
        ->get();
    }

    /**
     * Get the latest post for each subcategory.
     *
     * @param \Illuminate\Database\Eloquent\Collection $subcategories
     * @return array
     */
    private function getLatestPostsForSubcategories($subcategories)
    {
        $latestPosts = [];

        foreach ($subcategories as $index => $subcategory) {
            $latestPost = Post::whereHas('thread', function ($query) use ($subcategory) {
                $query->where('category_id', $subcategory->id);
            })
            ->latest('created_at')
            ->first();

            if ($latestPost) {
                $latestPost->load('thread', 'user'); // Lazy load thread and user relationships
            }

            $latestPosts[$index] = $latestPost;
        }

        return $latestPosts;
    }

    /**
     * Get the latest post for each thread in the given threads.
     *
     * @param \Illuminate\Database\Eloquent\Collection $threads
     * @return array
     */
    private function getLatestPostsForThreads($threads)
    {
        $latestPostInThreads = [];

        foreach ($threads as $thread) {
            $latestPost = Post::where('thread_id', $thread->id)
            ->latest('created_at')
            ->with('user')
            ->first();

            $latestPostInThreads[] = $latestPost;
        }

        return $latestPostInThreads;
    }

    /**
     * Get the number of posts for each thread.
     *
     * @param \Illuminate\Support\Collection $threads
     * @return array
     */
    protected function getPostCountsForThreads($threads)
    {
        $postCounts = [];

        foreach ($threads as $thread) {
            $postCounts[] = $thread->posts()->count();
        }

        return $postCounts;
    }

    /**
     * Get the number of posts for each thread.
     *
     * @param \Illuminate\Support\Collection $subcategories
     * @return array
     */
    protected function getThreadCountsForSubcategories($subcategories)
    {
        $threadCounts = [];

        foreach ($subcategories as $subcategory) {
            $threadCounts[] = $subcategory->threads()->count();
        }

        return $threadCounts;
    }
}
