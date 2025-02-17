<?php

namespace App\Policies;

use App\Models\Report;
use App\Models\User;
use App\Models\Post;
use Illuminate\Auth\Access\Response;

class ReportPolicy
{
    /**
     * Determine whether the user can solve reports.
     */
    public function solve(User $authUser, Report $report) {
        // Prevent solving if the report is already resolved
        if ($report->isSolved()) {
            return false;
        }

        // Fetch the reported user, including soft-deleted posts
        $reportedUser = $report->post()->withTrashed()->first()?->user;

        // Check if the reported user exists
        if (!$reportedUser) {
            return false; // If the reported user doesn't exist, can't solve the report
        }

        // Check if auth user can edit and delete posts
        if (!$authUser->hasPermissionTo('edit_post') || !$authUser->hasPermissionTo('delete_post')) {
            return false;
        }

        if ($reportedUser->hasRole('SuperAdmin')) {
            return false; // No one can solve a SuperAdmin's report
        }

        if ($reportedUser->hasRole('Admin')) {
            return $authUser->hasPermissionTo('solve_report_admin_reported');
        }

        if ($reportedUser->hasRole('Moderator')) {
            return $authUser->hasPermissionTo('solve_report_moderator_reported');
        }

        if ($reportedUser->hasRole('User') || $reportedUser->hasRole('NewUser')) {
            return $authUser->hasPermissionTo('solve_report_user_reported');
        }

        return false; // Default deny
    }
}
