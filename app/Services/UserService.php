<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    /**
     * Check if the user has unapproved posts.
     *
     * @param \App\Models\User $user The user to check.
     * @return bool True if the user has unapproved posts, false otherwise.
     */
    public function hasUnapprovedPosts(User $user): bool
    {
        // Number of approved posts required for a newUser
        $minim = config('user.min_approved_posts_for_new_user');

        $recentPosts = $user->posts()->latest()->take($minim)->get();
        return $recentPosts->contains(fn($post) => !$post->approved);
    }

    /**
     * Check if the user has any unapproved threads.
     *
     * @param \App\Models\User $user The user to check.
     * @return bool True if the user has unapproved threads, false otherwise.
     */
    public function hasUnapprovedThreads(User $user): bool
    {
        // Fetch all threads created by the user
        $allThreads = $user->threads()->get();

        // Check if any thread is unapproved
        return $allThreads->contains(fn($thread) => !$thread->approved);
    }

    /**
     * Handle the case when a user is banned.
     *
     * @param \App\Models\User $user The user to check.
     * @param string $message The ban message to show.
     * @return \Illuminate\Http\RedirectResponse Redirect with a ban message.
     */
    public function ifBanned(User $user, string $message)
    {
        if ($user->isBanned()) {
            $banMessage = $message . " Your ban will be lifted on " . $user->getBanDuration();

            return back()->with(['banMessage' => $banMessage]);
        }

        return null;
    }

    /**
     * Promote the user to a "User" role if they are eligible.
     *
     * This method checks if the user has met the requirements to be promoted
     * from "NewUser" to "User", based on the number of approved posts they have.
     * If the conditions are met, the user’s role is updated accordingly.
     *
     * @param \App\Models\User $user The user instance to check and potentially promote.
     */
    public function promoteIfEligible(User $user): void
    {
        $minApprovedPosts = config('user.min_approved_posts_for_new_user');

        // Check if the user is already promoted or is banned
        if (!$user->hasRole('NewUser') || $user->isBanned()) {
            return;
        }

        // Check if the user has enough posts
        if ($user->posts()->count() < $minApprovedPosts) {
            return;
        }

        // Get the last 10 posts and check if all are approved
        $recentPosts = $user->posts()->latest()->take($minApprovedPosts)->get();
        if ($recentPosts->contains(fn($post) => !$post->approved)) {
            return;
        }

        // Promote the user to 'User' role
        $user->removeRole('NewUser');
        $user->assignRole('User');
    }
}
