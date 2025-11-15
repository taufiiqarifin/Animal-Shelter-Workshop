<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class CaretakerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $caretakerRole = Role::firstOrCreate(['name' => 'caretaker']);
        // Create Caretaker user
        $caretaker = User::create([
            'name' => 'Caretaker User',
            'email' => 'caretaker@gmail.com',
            'password' => bcrypt('password'),
            'phoneNum' => '0123456789',
            'address' => '8, Jalan Sejahtera 7, Taman Bukit Tambun Perdana',
            'city' => 'Durian Tunggal',
            'state' => 'Melaka',
        ]);

        $caretaker->assignRole($caretakerRole);
    }
}
