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
}
