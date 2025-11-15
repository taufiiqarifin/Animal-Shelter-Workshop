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
        // Create roles if they do not exist
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Create Admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@gmail.com',
            'password' => bcrypt('password'),
            'phoneNum' => '0137121612',
            'address' => '29, Jalan Sejahtera 9',
            'city' => 'Ayer Keroh',
            'state' => 'Melaka',
        ]);

        $admin->assignRole($adminRole);


    }
}