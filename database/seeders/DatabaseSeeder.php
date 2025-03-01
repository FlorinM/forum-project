<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $testUser = User::create([
            'name' => 'Test User',
            'nickname' => 'testuser',
            'email' => 'test@example.com',
            'password' => Hash::make('testuser12345678'),

        ]);

        // Run the Roles and Permissions Seeder
        $this->call(RolesAndPermissionsSeeder::class);

        // Run the Categories Seeder
        $this->call(ForumSeeder::class);

        // Give User role to Test User
        $userRole = Role::where('name', 'User')->first();
        if ($userRole) {
            $testUser->assignRole($userRole);
        }
    }
}
