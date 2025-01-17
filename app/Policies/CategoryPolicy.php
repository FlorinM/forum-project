<?php

namespace App\Policies;

use App\Models\User;

class CategoryPolicy
{
    /**
     * Determine if the authenticated user can edit a post.
     *
     * @param \App\Models\User $authUser
     * @return bool
     */
    public function edit(User $authUser): bool
    {
        // Check if the authenticated user has the "edit_category" permission
        if ($authUser->hasPermissionTo('edit_category')) {
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
        // Check if the authenticated user has the "delete_category" permission
        if ($authUser->hasPermissionTo('delete_category')) {
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
        // Check if the authenticated user has the "create_category" permission
        if ($authUser->hasPermissionTo('create_category')) {
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
    public function move(User $authUser): bool
    {
        // Check if the authenticated user has the "move_category" permission
        if ($authUser->hasPermissionTo('move_category')) {
            return true;
        }

        return false;
    }
}
