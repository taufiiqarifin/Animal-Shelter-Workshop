<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Bookings and animal_booking pivot are stored in Danish's database
     * Cross-database references to:
     * - Users (Taufiq's database)
     * - Animals (Shafiqah's database)
     */
    public function run()
    {
        $this->command->info('Starting Booking Seeder...');
        $this->command->info('========================================');

        // Get users from Taufiq's database (cross-database)
        $this->command->info('Fetching users from Taufiq\'s database...');
        $users = DB::connection('taufiq')->table('users')->pluck('id')->toArray();

        if (empty($users)) {
            $this->command->error('No users found. Please run UserSeeder first.');
            return;
        }

        $this->command->info("Found " . count($users) . " users");

        // CRITICAL FIX: Only get animals that are NOT adopted from Shafiqah's database (cross-database)
        // Adopted animals should not be available for bookings
        $this->command->info('Fetching not adopted animals from Shafiqah\'s database...');
        $notAdoptedAnimalIds = DB::connection('shafiqah')
            ->table('animal')
            ->where('adoption_status', 'Not Adopted')
            ->pluck('id')
            ->toArray();

        if (empty($notAdoptedAnimalIds)) {
            $this->command->error('No "Not Adopted" animals found. Please run AnimalSeeder first.');
            return;
        }

        $this->command->info('Found ' . count($notAdoptedAnimalIds) . ' not adopted animals for bookings');

        $statuses = ['Pending', 'Completed', 'Confirmed', 'Cancelled'];

        // Define appointment times from 9am to 5pm with 30-minute intervals
        $appointmentTimes = [
            '09:00:00', '09:30:00',
            '10:00:00', '10:30:00',
            '11:00:00', '11:30:00',
            '12:00:00', '12:30:00',
            '13:00:00', '13:30:00',
            '14:00:00', '14:30:00',
            '15:00:00', '15:30:00',
            '16:00:00', '16:30:00',
            '17:00:00'
        ];

        // Use transaction for Danish's database
        DB::connection('danish')->beginTransaction();

        try {
            $this->command->info('');
            $this->command->info('Creating bookings in Danish\'s database...');

            $totalBookings = 100;
            $bookingCount = 0;

            for ($i = 0; $i < $totalBookings; $i++) {
                // Random date in past 6 months
                $date = Carbon::now()->subDays(rand(0, 180));

                // Pick a random time from the available slots
                $time = $appointmentTimes[array_rand($appointmentTimes)];

                // Insert booking into Danish's database
                $bookingId = DB::connection('danish')->table('booking')->insertGetId([
                    'appointment_date' => $date->format('Y-m-d'),
                    'appointment_time' => $time,
                    'status'           => $statuses[array_rand($statuses)],
                    'remarks'          => 'N/A',
                    'userID'           => $users[array_rand($users)], // Cross-database reference to Taufiq
                    'created_at'       => $date,
                    'updated_at'       => $date,
                ]);

                // Attach 1-3 random NOT ADOPTED animals to this booking via pivot table
                $numAnimals = rand(1, min(3, count($notAdoptedAnimalIds)));
                $randomAnimalIds = array_rand(array_flip($notAdoptedAnimalIds), $numAnimals);
                $randomAnimalIds = is_array($randomAnimalIds) ? $randomAnimalIds : [$randomAnimalIds];

                foreach ($randomAnimalIds as $animalId) {
                    // Insert into animal_booking pivot table in Danish's database
                    DB::connection('danish')->table('animal_booking')->insert([
                        'bookingID'  => $bookingId,
                        'animalID'   => $animalId, // Cross-database reference to Shafiqah
                        'remarks'    => null,
                        'created_at' => $date,
                        'updated_at' => $date,
                    ]);
                }

                $bookingCount++;
            }

            DB::connection('danish')->commit();

            $this->command->info('');
            $this->command->info('=================================');
            $this->command->info('✓ Booking Seeding Completed!');
            $this->command->info('=================================');
            $this->command->info("Total bookings created: {$bookingCount}");
            $this->command->info('Database: Danish (SQL Server)');
            $this->command->info('Cross-references: Taufiq (Users), Shafiqah (Animals)');
            $this->command->info('=================================');

        } catch (\Exception $e) {
            DB::connection('danish')->rollBack();

            $this->command->error('');
            $this->command->error('Error seeding bookings: ' . $e->getMessage());
            $this->command->error('Transaction rolled back');

            throw $e;
        }
    }
}
