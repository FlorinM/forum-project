<?php

namespace App\Policies;

use App\Models\User;

class ThreadPolicy
{
    /**
     * Determine if the authenticated user can edit a post.
     *
     * @param \App\Models\User $authUser
     * @return bool
     */
    public function edit(User $authUser): bool
    {
        // Check if the authenticated user has the "edit_thread" permission
        if ($authUser->hasPermissionTo('edit_thread')) {
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
        // Check if the authenticated user has the "delete_thread" permission
        if ($authUser->hasPermissionTo('delete_thread')) {
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
        // Check if the authenticated user has the "create_thread" permission
        if ($authUser->hasPermissionTo('create_thread')) {
            return true;
        }

        return false;
    }
}
