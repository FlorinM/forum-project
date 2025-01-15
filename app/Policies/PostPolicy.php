<?php

namespace App\Policies;

use App\Models\User;

class PostPolicy
{
    /**
     * Determine if the authenticated user can edit a post.
     *
     * @param \App\Models\User $authUser
     * @return bool
     */
    public function edit(User $authUser): bool
    {
        // Check if the authenticated user has the "edit_post" permission
        if ($authUser->can('edit_post')) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the authenticated user can delete a post.
     *
     * @param \App\Models\User $authUser
     * @return bool
     */
    public function delete(User $authUser): bool
    {
        // Check if the authenticated user has the "delete_post" permission
        if ($authUser->can('delete_post')) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the authenticated user can create a post.
     *
     * @param \App\Models\User $authUser
     * @return bool
     */
    public function create(User $authUser): bool
    {
        // Check if the authenticated user has the "create_post" permission
        if ($authUser->can('create_post')) {
            return true;
        }

        return false;
    }
}
