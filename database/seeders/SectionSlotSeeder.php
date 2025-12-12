<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectionSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Sections and Slots are stored in Atiqah's database (Shelter Management Module)
     */
    public function run(): void
    {
        $this->command->info('Starting Section & Slot Seeder...');
        $this->command->info('========================================');

        // Define sections
        $sections = [
            [
                'name' => 'Cat Zone',
                'description' => 'Dedicated area for cats with climbing structures and cozy spaces',
                'slots_count' => 30,
            ],
            [
                'name' => 'Dog Area',
                'description' => 'Spacious area for dogs with play equipment and exercise space',
                'slots_count' => 40,
            ],
            [
                'name' => 'Puppy Nursery',
                'description' => 'Special care area for puppies and young dogs',
                'slots_count' => 15,
            ],
            [
                'name' => 'Kitten Corner',
                'description' => 'Warm and safe environment for kittens',
                'slots_count' => 20,
            ],
            [
                'name' => 'Medical Ward',
                'description' => 'Quarantine and recovery area for animals receiving treatment',
                'slots_count' => 10,
            ],
            [
                'name' => 'Isolation Unit',
                'description' => 'Separate area for animals with contagious conditions',
                'slots_count' => 8,
            ],
        ];

        $totalSlots = 0;

        // Use transaction for Atiqah's database
        DB::connection('atiqah')->beginTransaction();

        try {
            // Insert sections and create slots for each
            foreach ($sections as $sectionData) {
                // Insert section into Atiqah's database
                $sectionId = DB::connection('atiqah')->table('section')->insertGetId([
                    'name' => $sectionData['name'],
                    'description' => $sectionData['description'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Create slots for this section
                $slots = [];
                $slotsCount = $sectionData['slots_count'];
                $currentLetter = 'A';
                $currentNumber = 1;
                $slotsPerRow = 10; // 10 slots per row before moving to next letter

                for ($i = 1; $i <= $slotsCount; $i++) {
                    $slotName = $currentLetter . $currentNumber;

                    $slots[] = [
                        'name' => $slotName,
                        'sectionID' => $sectionId,
                        'capacity' => 1, // Each slot holds 1 animal
                        'status' => 'available',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $totalSlots++;

                    // Move to next slot number
                    $currentNumber++;

                    // If we've reached 10 slots, move to next letter
                    if ($currentNumber > $slotsPerRow) {
                        $currentNumber = 1;
                        $currentLetter++;
                    }
                }

                // Insert slots for this section in chunks into Atiqah's database
                foreach (array_chunk($slots, 50) as $chunk) {
                    DB::connection('atiqah')->table('slot')->insert($chunk);
                }

                $this->command->info("✓ Created section '{$sectionData['name']}' with {$slotsCount} slots");
            }

            DB::connection('atiqah')->commit();

            $this->command->info('');
            $this->command->info('=================================');
            $this->command->info('✓ Section & Slot Seeding Completed!');
            $this->command->info('=================================');
            $this->command->info('Total Sections: ' . count($sections));
            $this->command->info('Total Slots: ' . $totalSlots);
            $this->command->info('Database: Atiqah (MySQL)');
            $this->command->info('=================================');

        } catch (\Exception $e) {
            DB::connection('atiqah')->rollBack();

            $this->command->error('');
            $this->command->error('Error seeding sections and slots: ' . $e->getMessage());
            $this->command->error('Transaction rolled back');

            throw $e;
        }
    }
}
