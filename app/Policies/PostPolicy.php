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
        if ($authUser->hasPermissionTo('edit_post')) {
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
        if ($authUser->hasPermissionTo('delete_post')) {
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
        if ($authUser->hasPermissionTo('create_post')) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the authenticated user can approve a post.
     *
     * @param \App\Models\User $authUser
     * @return bool
     */
    public function approve(User $authUser): bool
    {
        // Check if the authenticated user has the "approve_post" permission
        if ($authUser->hasPermissionTo('approve_post')) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the authenticated user can move a post.
     *
     * @param \App\Models\User $authUser
     * @return bool
     */
    public function move(User $authUser): bool
    {
        // Check if the authenticated user has the "move_post" permission
        if ($authUser->hasPermissionTo('move_post')) {
            return true;
        }

        return false;
    }
}
