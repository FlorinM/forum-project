<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Define permissions
        $permissions = [
            'create_thread',
            'create_post',
            'edit_thread',
            'delete_thread',
            'move_thread',
            'move_post',
            'edit_post',
            'delete_post',
            'ban_user',
            'unban_user',
            'approve_registration',
            'approve_thread',
            'approve_post',
            'create_category',
            'edit_category',
            'delete_category',
            'move_category',
            'delete_user',
            'promote_user',
            'assign_admin_role',
            'edit_user',
            'send_message',
            'solve_report_user_reported',
            'solve_report_moderator_reported',
            'solve_report_admin_reported',
            'report_post',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign existing permissions
        $superAdmin = Role::firstOrCreate(['name' => 'SuperAdmin']);
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $moderator = Role::firstOrCreate(['name' => 'Moderator']);
        $user = Role::firstOrCreate(['name' => 'User']);
        $newUser = Role::firstOrCreate(['name' => 'NewUser']);

        // Assign permissions to roles
        $superAdmin->givePermissionTo(Permission::all());

        $adminPermissions = [
            'create_thread',
            'create_post',
            'edit_thread',
            'delete_thread',
            'move_thread',
            'move_post',
            'edit_post',
            'delete_post',
            'ban_user',
            'unban_user',
            'approve_registration',
            'approve_thread',
            'approve_post',
            'create_category',
            'edit_category',
            'delete_category',
            'move_category',
            'delete_user',
            'promote_user',
            'edit_user',
            'send_message',
            'solve_report_user_reported',
            'solve_report_moderator_reported',
            'report_post',
        ];
        $admin->givePermissionTo($adminPermissions);

        $moderatorPermissions = [
            'create_thread',
            'create_post',
            'edit_thread',
            'delete_thread',
            'move_thread',
            'edit_post',
            'delete_post',
            'ban_user',
            'unban_user',
            'approve_registration',
            'approve_thread',
            'approve_post',
            'send_message',
            'solve_report_user_reported',
            'report_post',
        ];
        $moderator->givePermissionTo($moderatorPermissions);

        $userPermissions = [
            'create_thread',
            'create_post',
            'send_message',
            'report_post',
        ];
        $user->givePermissionTo($userPermissions);

        $newUserPermissions = [
            'create_thread',
            'create_post',
        ];
        $newUser->givePermissionTo($newUserPermissions);
    }
}
