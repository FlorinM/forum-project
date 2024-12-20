<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Thread;
use App\Models\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VisitedUserController extends Controller
{
    /**
     * Display the profile of a visited user.
     *
     * @param User $user The user whose profile is being viewed. This will be automatically injected by the route model binding.
     * @return \Inertia\Response Renders the profile page of the specified user.
     */
    public function showProfile(?User $user)
    {
        // Check if the user exists, if not redirect back with a message
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        // Calculate the total number of posts the user has made
        $totalPosts = Post::where('user_id', $user->id)->count();

        // Calculate the total number of threads the user has started
        $totalThreads = Thread::where('user_id', $user->id)->count();

        // Return the profile page with the user, total posts, and total threads
        return Inertia::render('VisitedUser/Profile', [
            'user' => $user,
            'totalPosts' => $totalPosts,
            'totalThreads' => $totalThreads,
        ]);
    }

    /**
     * Fetch the latest 20 threads started by the specified user.
     *
     * This method retrieves threads where the `user_id` matches the provided user's ID,
     * orders them by the latest creation date, and limits the result to 20 threads.
     * The threads are returned as a JSON response.
     *
     * @param \App\Models\User $user The user whose threads are being fetched.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the user's threads.
     */
    public function fetchThreads(User $user)
    {
        $threads = Thread::where('user_id', $user->id)->latest()->take(20)->get();

        return response()->json([
            'fetchedData' => $threads,
        ]);
    }

    /**
     * Fetch the latest 10 posts of a user along with thread and category details.
     *
     * This method retrieves the latest 10 posts of a specified user, along with the
     * thread the post belongs to and the category the thread belongs to.
     *
     * @param \App\Models\User $user The user whose posts are being fetched.
     * @return \Illuminate\Http\JsonResponse A JSON response containing the user's posts,
     *         along with thread and category details.
     */
    public function fetchPosts(User $user)
    {
        // Fetch latest 10 posts of the user, including the thread and category details
        $posts = Post::with(['thread.category']) // Eager load the thread and its category
            ->where('user_id', $user->id) // Filter posts by the specified user
            ->latest() // Order by the most recent posts
            ->take(10) // Limit to the latest 10 posts
            ->get();

        return response()->json([
            'fetchedData' => $posts,
        ]);
    }
}
