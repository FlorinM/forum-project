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

    /**
     * Check if a user has blocked another user.
     *
     * @param \App\Models\User $blocker The user performing the block action.
     * @param \App\Models\User $blocked The user to check if blocked.
     * @return bool True if the user has blocked the other user, false otherwise.
     */
    public function hasBlocked(User $blocker, User $blocked): bool
    {
        return $blocker->blockedUsers()->where('blocked_user_id', $blocked->id)->exists();
    }

    /**
     * Check if a user is blocked by another user.
     *
     * @param \App\Models\User $blocked The user being checked.
     * @param \App\Models\User $blocker The user who may have blocked them.
     * @return bool True if the user is blocked by the given user, false otherwise.
     */
    public function isBlockedBy(User $blocked, User $blocker): bool
    {
        return $blocked->blockedBy()->where('user_id', $blocker->id)->exists();
    }

    /**
     * Block a user.
     *
     * This method ensures that a user can block another user only if they haven't already.
     *
     * @param \App\Models\User $blocker The user who is blocking.
     * @param \App\Models\User $blocked The user being blocked.
     */
    public function block(User $blocker, User $blocked): void
    {
        if (!$this->hasBlocked($blocker, $blocked)) {
            $blocker->blockedUsers()->attach($blocked->id);
        }
    }

    /**
     * Unblock a user.
     *
     * This method removes a previously blocked user from the block list.
     *
     * @param \App\Models\User $blocker The user who is unblocking.
     * @param \App\Models\User $blocked The user being unblocked.
     */
    public function unblock(User $blocker, User $blocked): void
    {
        if ($this->hasBlocked($blocker, $blocked)) {
            $blocker->blockedUsers()->detach($blocked->id);
        }
    }
}
