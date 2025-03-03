<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine if the authenticated user can promote another user to admin.
     *
     * @param \App\Models\User $authUser
     * @param \App\Models\User $targetUser
     * @return bool
     */
    public function promoteToAdmin(User $authUser, User $targetUser): bool
    {
        // Check if the authenticated user has the "assign_admin_role" permission
        if (!$authUser->hasPermissionTo('assign_admin_role')) {
            return false;
        }

        // Only moderators can be promoted to admins
        if (!$targetUser->hasRole('Moderator')) {
            return false;
        }

        return true;
    }

    /**
     * Determine if the authenticated user can demote another user to regular user.
     *
     * @param \App\Models\User $authUser
     * @param \App\Models\User $targetUser
     * @return bool
     */
    public function demoteToModerator(User $authUser, User $targetUser): bool
    {
        // Check if the authenticated user has the "assign_admin_role" permission
        if (!$authUser->hasPermissionTo('assign_admin_role')) {
            return false;
        }

        // Only admins can be demoted to moderators
        if (!$targetUser->hasRole('Admin')) {
            return false;
        }

        return true;
    }


    /**
     * Determine if the authenticated user can promote another user to moderator.
     *
     * @param \App\Models\User $authUser
     * @param \App\Models\User $targetUser
     * @return bool
     */
    public function promoteToModerator(User $authUser, User $targetUser): bool
    {
        // Check if the authenticated user has the "promote_user" permission
        if (!$authUser->hasPermissionTo('promote_user')) {
            return false;
        }

        // Only regular users can be promoted to moderators
        if (!$targetUser->hasRole('User')) {
            return false;
        }

        return true;
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
        // Check if the authenticated user has the "promote_user" permission
        if (!$authUser->hasPermissionTo('promote_user')) {
            return false;
        }

        // Only moderators can be demoted to users
        if (!$targetUser->hasRole('Moderator')) {
            return false;
        }

        return true;
    }

    /**
     * Determine if the authenticated user can ban another user.
     *
     * @param \App\Models\User $authUser
     * @param \App\Models\User $targetUser
     * @return bool
     */
    public function ban(User $authUser, ?User $targetUser = null): bool
    {
        // First, check if the authenticated user has the "ban_user" permission
        if (!$authUser->hasPermissionTo('ban_user')) {
            return false;
        }

        // Nothing to ban
        if ($targetUser === null) {
            return false;
        }

        // SuperAdmin can ban anyone
        if ($authUser->hasRole('SuperAdmin')) {
            return true;
        }

        // Admins can ban anyone except other Admins and the SuperAdmin
        if ($authUser->hasRole('Admin')) {
            if ($targetUser->hasRole('SuperAdmin') || $targetUser->hasRole('Admin')) {
                return false;
            }

            return true;
        }

        // Moderators can only ban regular Users and New Users
        if ($authUser->hasRole('Moderator')) {
            return $targetUser->hasRole('User') || $targetUser->hasRole('NewUser');
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
        if (!$authUser->hasPermissionTo('unban_user')) {
            return false;
        }

        // SuperAdmin can unban anyone
        if ($authUser->hasRole('SuperAdmin')) {
            return true;
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
    public function edit(User $authUser, ?User $targetUser = null): bool
    {
        // First, check if the authenticated user has the "edit_user" permission
        if (!$authUser->hasPermissionTo('edit_user')) {
            return false;
        }

        // New user case, in admin panel
        if ($targetUser === null) {
            return true;
        }

        // Admins can edit anyone except other Admins and the SuperAdmin
        if ($authUser->hasRole('Admin')) {
            if ($targetUser->hasRole('SuperAdmin') || $targetUser->hasRole('Admin')) {
                return false;
            }

            return true;
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
        if (!$authUser->hasPermissionTo('delete_user')) {
            return false;
        }

        // SuperAdmin can delete anyone
        if ($authUser->hasRole('SuperAdmin')) {
            return true;
        }

        // Admins can delete anyone except other Admins and the SuperAdmin
        if ($authUser->hasRole('Admin')) {
            if ($targetUser->hasRole('SuperAdmin') || $targetUser->hasRole('Admin')) {
                return false;
            }

            return true;
        }

        // Other roles can't delete anyone
        return false;
    }
}
