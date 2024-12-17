<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Thread;
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
    public function showProfile(User $user)
    {
        // You can add additional logic here, like checking if the user exists
        return Inertia::render('VisitedUser/Profile', [
            'user' => $user,
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
            'threads' => $threads,
        ]);
    }
}
