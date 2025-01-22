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
            'approve_report',
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
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles and assign existing permissions
        $admin = Role::firstOrCreate(['name' => 'Admin']);
        $moderator = Role::firstOrCreate(['name' => 'Moderator']);
        $user = Role::firstOrCreate(['name' => 'User']);
        $newUser = Role::firstOrCreate(['name' => 'NewUser']);

        // Assign permissions to roles
        $admin->givePermissionTo(Permission::all());

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
            'approve_report',
            'approve_thread',
            'approve_post',
            'send_message',
        ];
        $moderator->givePermissionTo($moderatorPermissions);

        $userPermissions = [
            'create_thread',
            'create_post',
            'send_message',
        ];
        $user->givePermissionTo($userPermissions);

        $newUserPermissions = [
            'create_thread',
            'create_post',
        ];
        $newUser->givePermissionTo($newUserPermissions);
    }
}
