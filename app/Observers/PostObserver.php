<?php

namespace App\Observers;

use App\Models\Post;
use App\Services\UserService;

class PostObserver
{
    /**
     * The service responsible for handling user-related logic,
     * such as promotions, bans, and other user-specific operations.
     *
     * @var \App\Services\UserService
     */
    protected UserService $userService;

    /**
     * Initialize the controller with the UserService dependency.
     *
     * @param \App\Services\UserService $userService The service responsible for user-related logic.
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Handle the "updated" event for the Post model.
     *
     * This method is triggered whenever an existing post is updated.
     * It checks if the post was approved and, if so, evaluates whether
     * the associated user is eligible for promotion.
     *
     * @param \App\Models\Post $post The post instance that was updated.
     */
    public function updated(Post $post)
    {
        // Check if the `approved` attribute was changed to `true`
        if ($post->wasChanged('approved') && $post->approved) {
            $user = $post->user;

            // Trigger promotion logic if the user is eligible
            if ($user->hasRole('NewUser')) {
                $this->userService->promoteIfEligible($user);
            }
        }
    }
}

