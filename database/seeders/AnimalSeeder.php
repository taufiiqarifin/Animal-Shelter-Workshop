<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Faker\Factory as Faker;
use Carbon\Carbon;

class AnimalSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        for ($i = 0; $i < 50; $i++) { // Generate 50 dummy animals
            DB::table('animal')->insert([
                'species' => $faker->randomElement(['Dog', 'Cat', 'Rabbit', 'Bird']),
                'health_details' => $faker->sentence(6),
                'age' => $faker->numberBetween(1, 15),
                'gender' => $faker->randomElement(['Male', 'Female']),
                'adoption_status' => $faker->randomElement(['Available', 'Adopted', 'Pending']),
                'arrival_date' => $faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
                'medical_status' => $faker->randomElement(['Healthy', 'Sick', 'Recovering']),
                'rescueID' => $faker->randomElement($rescueIDs),
                'slotID' => $faker->randomElement($slotIDs),
                'vaccinationID' => $faker->randomElement($vaccinationIDs),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);            
        }
    }
}
