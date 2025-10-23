<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class CreateStaffUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure the 'staff' role exists
        $role = Role::firstOrCreate(['name' => 'staff']);

        // Create a staff user
        $user = User::create([
            'name' => 'Danish Staff',
            'email' => 'danishIrwan@staff.com',
            'password' => bcrypt('password'), // Change this!
            'email_verified_at' => now(),
        ]);

        // Assign the staff role to the user
        $user->assignRole($role);
    }
}