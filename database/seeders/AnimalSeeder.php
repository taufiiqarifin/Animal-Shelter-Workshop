<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Animal;
use App\Models\Rescue;
use App\Models\Slot;
use App\Models\Vet;
use App\Models\Vaccination;
use App\Models\Medical;
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

        // Define available images by species
        $catImages = [];
        $dogImages = [];

        for ($i = 1; $i <= 8; $i++) {
            $catImages[] = "animal_images/cat{$i}.jpg";
        }

        for ($i = 1; $i <= 6; $i++) {
            $dogImages[] = "animal_images/dog{$i}.jpg";
        }

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

        // ===== ASSIGN IMAGES TO ANIMALS =====
        $this->assignImagesToAnimals($createdAnimals, $catImages, $dogImages);

        // ===== CREATE VACCINATION RECORDS FOR ALL ANIMALS =====
        if ($vets->isNotEmpty()) {
            $this->createVaccinationRecords($createdAnimals, $vets);
            $this->createMedicalRecords($createdAnimals, $vets);
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
     * Assign images to animals based on their species
     */
    private function assignImagesToAnimals($animals, $catImages, $dogImages)
    {
        $images = [];
        $totalImages = 0;

        foreach ($animals as $animal) {
            // Randomly assign 1-3 images per animal
            $numImages = rand(1, 3);

            // Select appropriate images based on species
            $availableImages = $animal->species === 'Cat' ? $catImages : $dogImages;

            // Randomly select images for this animal
            $selectedImages = array_rand(array_flip($availableImages), min($numImages, count($availableImages)));

            // Handle case where only 1 image is selected (array_rand returns string, not array)
            if (!is_array($selectedImages)) {
                $selectedImages = [$selectedImages];
            }

            foreach ($selectedImages as $imagePath) {
                $images[] = [
                    'image_path' => $imagePath,
                    'animalID'   => $animal->id,
                    'reportID'   => null,
                    'clinicID'   => null,
                    'created_at' => $animal->created_at,
                    'updated_at' => $animal->created_at,
                ];
                $totalImages++;
            }
        }

        // Insert all images
        DB::table('image')->insert($images);

        $this->command->info("Total images assigned to animals: {$totalImages}");
        $avgImages = round($totalImages / count($animals), 1);
        $this->command->info("Average images per animal: {$avgImages}");
    }

    /**
     * Create vaccination and medical records for animals
     */
    private function createMedicalRecords($animals, $vets)
    {
        $treatmentTypes = [
            'Cat' => [
                'Routine Check-up',
                'Dental Cleaning',
                'Spay/Neuter Surgery',
                'Upper Respiratory Infection',
                'Flea/Tick Treatment',
                'Urinary Tract Infection',
                'Wound Care',
                'Ear Infection',
                'Gastrointestinal Issues',
                'Skin Allergies',
            ],
            'Dog' => [
                'Routine Check-up',
                'Dental Cleaning',
                'Spay/Neuter Surgery',
                'Kennel Cough',
                'Flea/Tick Treatment',
                'Hip Dysplasia',
                'Ear Infection',
                'Hot Spots',
                'Arthritis Treatment',
                'Wound Care',
                'Gastric Issues',
            ],
        ];

        $diagnoses = [
            'Routine Check-up' => [
                'Healthy - No issues found',
                'Overall good health',
                'Minor concerns noted',
            ],
            'Dental Cleaning' => [
                'Tartar buildup',
                'Mild gingivitis',
                'Plaque removal needed',
                'Tooth extraction required',
            ],
            'Spay/Neuter Surgery' => [
                'Pre-operative assessment completed',
                'Surgery performed successfully',
                'Post-operative recovery',
            ],
            'Upper Respiratory Infection' => [
                'Viral upper respiratory infection',
                'Bacterial infection diagnosed',
                'Sneezing and nasal discharge',
            ],
            'Kennel Cough' => [
                'Bordetella bronchiseptica infection',
                'Mild to moderate kennel cough',
            ],
            'Flea/Tick Treatment' => [
                'Flea infestation detected',
                'Tick removal required',
                'Preventative treatment applied',
            ],
            'default' => [
                'Diagnosed after examination',
                'Symptoms observed and treated',
                'Follow-up recommended',
            ],
        ];

        $actions = [
            'Routine Check-up' => [
                'Physical examination performed',
                'Vital signs checked - all normal',
                'Weight recorded and monitored',
            ],
            'Dental Cleaning' => [
                'Professional dental cleaning performed',
                'Scaling and polishing completed',
                'Tooth extraction performed',
                'Dental X-rays taken',
            ],
            'Spay/Neuter Surgery' => [
                'Surgery performed under general anesthesia',
                'Post-operative pain management administered',
                'Sutures applied, follow-up in 10-14 days',
            ],
            'Flea/Tick Treatment' => [
                'Topical flea/tick medication applied',
                'Oral medication prescribed',
                'Environmental treatment recommended',
            ],
            'default' => [
                'Medication prescribed',
                'Treatment administered',
                'Observation and monitoring',
                'Symptomatic treatment provided',
                'Antibiotics prescribed',
                'Pain management initiated',
            ],
        ];

        $totalMedicalRecords = 0;

        foreach ($animals as $animal) {
            // Random number of medical records per animal (1-4)
            $numRecords = rand(1, 4);

            $availableTreatments = $treatmentTypes[$animal->species];

            for ($i = 0; $i < $numRecords; $i++) {
                $vet = $vets->random();
                $treatmentType = $availableTreatments[array_rand($availableTreatments)];

                // Get specific diagnosis for treatment type or use default
                $diagnosisOptions = $diagnoses[$treatmentType] ?? $diagnoses['default'];
                $diagnosis = $diagnosisOptions[array_rand($diagnosisOptions)];

                // Get specific action for treatment type or use default
                $actionOptions = $actions[$treatmentType] ?? $actions['default'];
                $action = $actionOptions[array_rand($actionOptions)];

                // Medical record date is after animal creation date
                $recordDate = Carbon::parse($animal->created_at)
                    ->addDays(rand(7, 180));

                // Cost varies by treatment type
                $costRanges = [
                    'Routine Check-up' => [30, 80],
                    'Dental Cleaning' => [150, 400],
                    'Spay/Neuter Surgery' => [200, 500],
                    'Upper Respiratory Infection' => [80, 200],
                    'Kennel Cough' => [80, 180],
                    'Flea/Tick Treatment' => [40, 120],
                    'Hip Dysplasia' => [200, 600],
                    'Arthritis Treatment' => [100, 300],
                    'default' => [50, 250],
                ];

                $costRange = $costRanges[$treatmentType] ?? $costRanges['default'];
                $cost = rand($costRange[0], $costRange[1]);

                $remarksOptions = [
                    'Animal responded well to treatment',
                    'Follow-up appointment scheduled',
                    'Owner advised on home care',
                    'Medication prescribed for 7-14 days',
                    'Condition improving with treatment',
                    'Monitor for any changes',
                    'Preventative measures discussed',
                    'No complications observed',
                    'Animal in stable condition',
                    'Treatment completed successfully',
                ];

                Medical::create([
                    'treatment_type' => $treatmentType,
                    'diagnosis' => $diagnosis,
                    'action' => $action,
                    'remarks' => $remarksOptions[array_rand($remarksOptions)],
                    'costs' => $cost,
                    'vetID' => $vet->id,
                    'animalID' => $animal->id,
                    'created_at' => $recordDate,
                    'updated_at' => $recordDate,
                ]);

                $totalMedicalRecords++;
            }
        }

        $this->command->info("Total medical records created: {$totalMedicalRecords}");
        $avgRecords = round($totalMedicalRecords / count($animals), 1);
        $this->command->info("Average medical records per animal: {$avgRecords}");
    }

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
