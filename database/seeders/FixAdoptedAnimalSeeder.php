<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Animal;
use App\Models\Slot;

class FixAdoptedAnimalSeeder extends Seeder
{
    public function run()
    {
        // Find all adopted animals that still have slots assigned
        $adoptedWithSlots = Animal::where('adoption_status', 'Adopted')
            ->whereNotNull('slotID')
            ->get();

        $count = $adoptedWithSlots->count();

        if ($count > 0) {
            // Get their slot IDs before removing
            $slotIds = $adoptedWithSlots->pluck('slotID')->toArray();

            // Remove slot assignments from adopted animals
            Animal::where('adoption_status', 'Adopted')
                ->whereNotNull('slotID')
                ->update(['slotID' => null]);

            // Mark those slots as available again
            Slot::whereIn('id', $slotIds)
                ->update(['status' => 'available']);

            $this->command->info("Fixed {$count} adopted animals by removing their slot assignments.");
            $this->command->info("Freed up {$count} slots and marked them as available.");
        } else {
            $this->command->info("No issues found. All adopted animals have no slot assignments.");
        }
    }
}
