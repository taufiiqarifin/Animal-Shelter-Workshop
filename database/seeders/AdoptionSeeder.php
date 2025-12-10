<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\Booking;
use App\Models\Adoption;
use App\Models\Animal;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdoptionSeeder extends Seeder
{
    public function run()
    {
        // Get animals that are already marked as 'Adopted' from AnimalSeeder
        $adoptedAnimals = Animal::where('adoption_status', 'Adopted')->get();

        if ($adoptedAnimals->isEmpty()) {
            $this->command->warn('No adopted animals found. Adoptions will not be created.');
            return;
        }

        $this->command->info("Found {$adoptedAnimals->count()} adopted animals. Creating adoption records...");

        $adoptionCount = 0;
        $adoptedAnimalsByUser = [];

        // Group adopted animals by creating fake user assignments
        // Each adoption typically has 1-2 animals
        $animalGroups = $adoptedAnimals->chunk(rand(1, 2));

        foreach ($animalGroups as $animalGroup) {
            // Get a random completed booking to associate with
            // In real scenario, these animals would have been in the booking
            $completedBooking = Booking::whereIn('status', ['completed', 'Completed'])
                ->whereHas('animals')
                ->inRandomOrder()
                ->first();

            if (!$completedBooking) {
                $this->command->warn('No completed bookings found. Skipping remaining adoptions.');
                break;
            }

            // Calculate total fee based on number of animals in this group
            $totalFee = $animalGroup->count() * rand(50, 150);
            $feePerAnimal = round($totalFee / $animalGroup->count(), 2);

            // Create a transaction for this adoption
            $adoptionDate = Carbon::parse($animalGroup->first()->updated_at); // Use animal's adoption date

            $transaction = Transaction::create([
                'amount'       => $totalFee,
                'status'       => 'Success',
                'remarks'      => 'Adoption fee for ' . $animalGroup->count() . ' animal(s)',
                'type'         => 'FPX Online Banking',
                'bill_code'    => 'BILL-' . strtoupper(Str::random(8)),
                'reference_no' => 'REF-' . $adoptionDate->format('Ymd') . '-' . rand(1000, 9999),
                'userID'       => $completedBooking->userID,
                'created_at'   => $adoptionDate,
                'updated_at'   => $adoptionDate,
            ]);

            // Create one adoption record per animal in this group
            foreach ($animalGroup as $animal) {
                Adoption::create([
                    'fee'           => $feePerAnimal,
                    'remarks'       => $animal->name . ' Adopted',
                    'bookingID'     => $completedBooking->id,
                    'transactionID' => $transaction->id,
                    'created_at'    => $adoptionDate,
                    'updated_at'    => $adoptionDate,
                ]);

                // Animal is ALREADY marked as 'Adopted' from AnimalSeeder
                // No need to update status again

                $adoptionCount++;
            }
        }

        $this->command->info("{$adoptionCount} adoption records created for adopted animals!");
    }
}
