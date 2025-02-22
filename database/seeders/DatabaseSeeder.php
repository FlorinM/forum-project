<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'nickname' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'testuser12345678',

        ]);

        // Run the Roles and Permissions Seeder
        $this->call(RolesAndPermissionsSeeder::class);

        // Run the Categories Seeder
        $this->call(ForumSeeder::class);
    }
}
