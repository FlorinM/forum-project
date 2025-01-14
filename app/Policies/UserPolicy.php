<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine if the authenticated user can promote another user to moderator.
     *
     * @param \App\Models\User $authUser
     * @param \App\Models\User $targetUser
     * @return bool
     */
    public function promoteToModerator(User $authUser, User $targetUser): bool
    {
        // Check if the authenticated user has the "promote_users" permission
        if (!$authUser->can('promote_user')) {
            return false;
        }

        // Only Admin can promote users to Moderator
        return $authUser->hasRole('Admin');
    }

    /**
     * Determine if the authenticated user can demote another user to regular user.
     *
     * @param \App\Models\User $authUser
     * @param \App\Models\User $targetUser
     * @return bool
     */
    public function demoteToUser(User $authUser, User $targetUser): bool
    {
        // Check if the authenticated user has the "promote_users" permission
        if (!$authUser->can('promote_user')) {
            return false;
        }

        // Only Admin can demote users to regular User
        return $authUser->hasRole('Admin');
    }

    /**
     * Determine if the authenticated user can ban another user.
     *
     * @param \App\Models\User $authUser
     * @param \App\Models\User $targetUser
     * @return bool
     */
    public function ban(User $authUser, User $targetUser): bool
    {
        // First, check if the authenticated user has the "ban_user" permission
        if (!$authUser->can('ban_user')) {
            return false;
        }

        // Admins can ban anyone except other Admins
        if ($authUser->hasRole('Admin')) {
            return !$targetUser->hasRole('Admin');
        }

        // Moderators can only ban regular Users
        if ($authUser->hasRole('Moderator')) {
            return $targetUser->hasRole('User');
        }

        // Other roles can't ban anyone
        return false;
    }

    /**
     * Determine if the authenticated user can unban another user.
     *
     * @param \App\Models\User $authUser
     * @param \App\Models\User $targetUser
     * @return bool
     */
    public function unban(User $authUser, User $targetUser): bool
    {
        // First, check if the authenticated user has the "unban_user" permission
        if (!$authUser->can('unban_user')) {
            return false;
        }

        // Admins can unban anyone except other Admins
        if ($authUser->hasRole('Admin')) {
            return !$targetUser->hasRole('Admin');
        }

        // Moderators can unban only regular Users
        if ($authUser->hasRole('Moderator')) {
            return $targetUser->hasRole('User');
        }

        // Other roles can't unban anyone
        return false;
    }

    /**
     * Determine if the authenticated user can edit another user.
     *
     * @param \App\Models\User $authUser
     * @param \App\Models\User $targetUser
     * @return bool
     */
    public function edit(User $authUser, User $targetUser): bool
    {
        // First, check if the authenticated user has the "edit_user" permission
        if (!$authUser->can('edit_user')) {
            return false;
        }

        // Admins can edit anyone except other Admins
        if ($authUser->hasRole('Admin')) {
            return !$targetUser->hasRole('Admin');
        }

        // Other roles can't edit anyone
        return false;
    }

    /**
     * Determine if the authenticated user can delete another user.
     *
     * @param \App\Models\User $authUser
     * @param \App\Models\User $targetUser
     * @return bool
     */
    public function delete(User $authUser, User $targetUser): bool
    {
        // First, check if the authenticated user has the "delete_user" permission
        if (!$authUser->can('delete_user')) {
            return false;
        }

        // Admins can delete anyone except other Admins
        if ($authUser->hasRole('Admin')) {
            return !$targetUser->hasRole('Admin');
        }

        // Other roles can't delete anyone
        return false;
    }
}
