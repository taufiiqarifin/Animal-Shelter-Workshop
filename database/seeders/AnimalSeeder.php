<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Animal;
use App\Models\Rescue;
use App\Models\Slot;
use App\Models\Vet;
use App\Models\Vaccination;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnimalSeeder extends Seeder
{
    public function run()
    {
        $species = ['Cat', 'Dog'];
        $genders = ['Male', 'Female'];

        $catNames = ['Milo', 'Coco', 'Luna', 'Oyen', 'Mimi', 'Snowy', 'Kitty', 'Nala', 'Bella'];
        $dogNames = ['Buddy', 'Rocky', 'Max', 'Shadow', 'Charlie', 'Bella', 'Duke', 'Lucky', 'Hunter'];

        // Get all successful rescues
        $successfulRescues = Rescue::where('status', 'Success')->get();

        // Get all available slots
        $availableSlots = Slot::where('status', 'available')->get();

        if ($availableSlots->isEmpty()) {
            $this->command->error('No available slots found. Please run SectionSlotSeeder first.');
            return;
        }

        // Get all vets for vaccination records
        $vets = Vet::all();
        if ($vets->isEmpty()) {
            $this->command->warn('No vets found. Vaccination records will not be created. Please run ClinicVetSeeder first.');
        }

        $animals = [];
        $totalAnimals = 100;
        $adoptedCount = (int)($totalAnimals * 0.15); // 15% adopted
        $notAdoptedCount = $totalAnimals - $adoptedCount; // 85% not adopted
        $animalsFromRescue = min((int)($totalAnimals * 0.8), $successfulRescues->count());

        // Shuffle slots for random assignment
        $shuffledSlots = $availableSlots->shuffle();
        $slotIndex = 0;

        // ===== CREATE NOT ADOPTED ANIMALS FIRST (THEY NEED SLOTS) =====
        for ($i = 0; $i < $notAdoptedCount; $i++) {
            $chosenSpecies = $species[array_rand($species)];
            $name = $chosenSpecies === 'Cat'
                ? $catNames[array_rand($catNames)]
                : $dogNames[array_rand($dogNames)];

            $ageCategories = $chosenSpecies === 'Cat'
                ? ['kitten', 'adult', 'senior']
                : ['puppy', 'adult', 'senior'];

            $age = $ageCategories[array_rand($ageCategories)];

            // Assign slot for NOT ADOPTED animals only
            $slotID = null;
            if ($slotIndex < $shuffledSlots->count()) {
                $slotID = $shuffledSlots[$slotIndex]->id;
                $slotIndex++;
            } else {
                $this->command->warn("Ran out of available slots at animal #{$i}. Stopping not adopted animal creation.");
                $notAdoptedCount = $i; // Update actual count
                break;
            }

            $rescueID = null;
            $createdAt = Carbon::now();

            if ($i < $animalsFromRescue && $successfulRescues->isNotEmpty()) {
                $rescue = $successfulRescues->random();
                $rescueID = $rescue->id;
                $createdAt = Carbon::parse($rescue->created_at)->addHours(rand(1, 24));
            } else {
                $year = rand(2024, 2025);
                $month = rand(1, 12);
                $day = rand(1, 28);
                $createdAt = Carbon::create($year, $month, $day, rand(0, 23), rand(0, 59));
            }

            $weight = $chosenSpecies === 'Cat' ? rand(2, 8) : rand(5, 35);

            $animals[] = [
                'name'            => $name . ' ' . Str::upper(Str::random(3)),
                'species'         => $chosenSpecies,
                'age'             => $age,
                'health_details'  => fake()->randomElement([
                    'Healthy and active',
                    'Needs regular vaccination',
                    'Recovering from minor injuries',
                    'Excellent condition, ready for adoption',
                    'Under medical observation',
                    'Fully vaccinated and healthy'
                ]),
                'weight'          => $weight,
                'gender'          => $genders[array_rand($genders)],
                'adoption_status' => 'Not Adopted',
                'rescueID'        => $rescueID,
                'slotID'          => $slotID, // HAS SLOT
                'created_at'      => $createdAt,
                'updated_at'      => $createdAt,
            ];
        }

        // ===== CREATE ADOPTED ANIMALS (NO SLOTS AT ALL) =====
        for ($i = 0; $i < $adoptedCount; $i++) {
            $chosenSpecies = $species[array_rand($species)];
            $name = $chosenSpecies === 'Cat'
                ? $catNames[array_rand($catNames)]
                : $dogNames[array_rand($dogNames)];

            $ageCategories = $chosenSpecies === 'Cat'
                ? ['kitten', 'adult', 'senior']
                : ['puppy', 'adult', 'senior'];

            $age = $ageCategories[array_rand($ageCategories)];

            $rescueID = null;
            $createdAt = Carbon::now();

            $totalProcessed = $notAdoptedCount + $i;
            if ($totalProcessed < $animalsFromRescue && $successfulRescues->isNotEmpty()) {
                $rescue = $successfulRescues->random();
                $rescueID = $rescue->id;
                $createdAt = Carbon::parse($rescue->created_at)->addHours(rand(1, 24));
            } else {
                $year = rand(2024, 2025);
                $month = rand(1, 12);
                $day = rand(1, 28);
                $createdAt = Carbon::create($year, $month, $day, rand(0, 23), rand(0, 59));
            }

            // Adoption date should be after creation (7-90 days later)
            $adoptedAt = Carbon::parse($createdAt)->addDays(rand(7, 90));

            $weight = $chosenSpecies === 'Cat' ? rand(2, 8) : rand(5, 35);

            $animals[] = [
                'name'            => $name . ' ' . Str::upper(Str::random(3)),
                'species'         => $chosenSpecies,
                'age'             => $age,
                'health_details'  => fake()->randomElement([
                    'Healthy and active',
                    'Needs regular vaccination',
                    'Recovering from minor injuries',
                    'Excellent condition, ready for adoption',
                    'Under medical observation',
                    'Fully vaccinated and healthy'
                ]),
                'weight'          => $weight,
                'gender'          => $genders[array_rand($genders)],
                'adoption_status' => 'Adopted',
                'rescueID'        => $rescueID,
                'slotID'          => null, // NO SLOT - CRITICAL FIX
                'created_at'      => $createdAt,
                'updated_at'      => $adoptedAt,
            ];
        }

        // Insert all animals
        $createdAnimals = [];
        foreach ($animals as $animalData) {
            $createdAnimals[] = Animal::create($animalData);
        }

        // Update slot status to 'occupied' for assigned slots ONLY
        $assignedSlotIds = array_filter(array_column($animals, 'slotID'));
        if (!empty($assignedSlotIds)) {
            Slot::whereIn('id', $assignedSlotIds)->update(['status' => 'occupied']);
        }

        // ===== CREATE VACCINATION RECORDS FOR ALL ANIMALS =====
        if ($vets->isNotEmpty()) {
            $this->createVaccinationRecords($createdAnimals, $vets);
        }

        // Statistics
        $fromRescueCount = count(array_filter($animals, fn($a) => $a['rescueID'] !== null));
        $actualAdoptedCount = count(array_filter($animals, fn($a) => $a['adoption_status'] === 'Adopted'));
        $actualNotAdoptedCount = count(array_filter($animals, fn($a) => $a['adoption_status'] === 'Not Adopted'));
        $ageCount = array_count_values(array_column($animals, 'age'));

        $this->command->info('');
        $this->command->info('=================================');
        $this->command->info('Animal Seeding Completed!');
        $this->command->info('=================================');
        $this->command->info("Total animals created: " . count($animals));
        $this->command->info("From rescues: {$fromRescueCount}");
        $this->command->info("Direct intakes: " . (count($animals) - $fromRescueCount));
        $this->command->info('');
        $this->command->info('Adoption Status:');
        $this->command->info("  - Not Adopted (available for adoption): {$actualNotAdoptedCount}");
        $this->command->info("  - Adopted (no longer in shelter): {$actualAdoptedCount}");
        $this->command->info("  - Slots occupied: " . count($assignedSlotIds));
        $this->command->info('');
        $this->command->info('Age Distribution:');
        foreach ($ageCount as $category => $count) {
            $this->command->info("  - " . ucfirst($category) . ": {$count}");
        }
        $this->command->info('=================================');
    }

    /**
     * Create vaccination records for animals
     */
    private function createVaccinationRecords($animals, $vets)
    {
        $vaccineTypes = [
            'Cat' => [
                ['name' => 'FVRCP', 'type' => 'Core', 'interval' => 365],
                ['name' => 'Rabies', 'type' => 'Core', 'interval' => 365],
                ['name' => 'FeLV', 'type' => 'Non-core', 'interval' => 365],
                ['name' => 'Bordetella', 'type' => 'Non-core', 'interval' => 180],
            ],
            'Dog' => [
                ['name' => 'DHPP', 'type' => 'Core', 'interval' => 365],
                ['name' => 'Rabies', 'type' => 'Core', 'interval' => 365],
                ['name' => 'Bordetella', 'type' => 'Non-core', 'interval' => 180],
                ['name' => 'Leptospirosis', 'type' => 'Non-core', 'interval' => 365],
                ['name' => 'Canine Influenza', 'type' => 'Non-core', 'interval' => 365],
            ],
        ];

        $totalVaccinations = 0;

        foreach ($animals as $animal) {
            // Random number of vaccination records per animal (1-5)
            $numVaccinations = rand(1, 5);

            $availableVaccines = $vaccineTypes[$animal->species];
            $selectedVaccines = [];

            // Ensure core vaccines are included first
            $coreVaccines = array_filter($availableVaccines, fn($v) => $v['type'] === 'Core');
            $nonCoreVaccines = array_filter($availableVaccines, fn($v) => $v['type'] === 'Non-core');

            // Add core vaccines
            foreach ($coreVaccines as $vaccine) {
                if (count($selectedVaccines) < $numVaccinations) {
                    $selectedVaccines[] = $vaccine;
                }
            }

            // Add random non-core vaccines if needed
            shuffle($nonCoreVaccines);
            foreach ($nonCoreVaccines as $vaccine) {
                if (count($selectedVaccines) < $numVaccinations) {
                    $selectedVaccines[] = $vaccine;
                }
            }

            // Create vaccination records
            foreach ($selectedVaccines as $index => $vaccine) {
                $vet = $vets->random();

                // Vaccination date is after animal creation date
                $vaccinationDate = Carbon::parse($animal->created_at)
                    ->addDays(rand(1, 30))
                    ->subDays($index * rand(30, 90)); // Space out vaccinations

                // Next due date based on vaccine interval
                $nextDueDate = Carbon::parse($vaccinationDate)->addDays($vaccine['interval']);

                // Cost varies by vaccine type
                $cost = $vaccine['type'] === 'Core'
                    ? rand(50, 150)
                    : rand(80, 200);

                $remarks = fake()->randomElement([
                    'Vaccination completed successfully',
                    'No adverse reactions observed',
                    'Animal tolerated vaccine well',
                    'Follow-up scheduled',
                    'Booster required in ' . ($vaccine['interval'] / 30) . ' months',
                    'Part of standard vaccination protocol',
                ]);

                Vaccination::create([
                    'name' => $vaccine['name'],
                    'type' => $vaccine['type'],
                    'next_due_date' => $nextDueDate,
                    'remarks' => $remarks,
                    'weight' => $animal->weight,
                    'costs' => $cost,
                    'animalID' => $animal->id,
                    'vetID' => $vet->id,
                    'created_at' => $vaccinationDate,
                    'updated_at' => $vaccinationDate,
                ]);

                $totalVaccinations++;
            }
        }

        $this->command->info("Total vaccination records created: {$totalVaccinations}");
        $avgVaccinations = round($totalVaccinations / count($animals), 1);
        $this->command->info("Average vaccinations per animal: {$avgVaccinations}");
    }
}
