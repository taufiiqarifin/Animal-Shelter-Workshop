<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RescueSeeder extends Seeder
{
    public function run()
    {
        $reports = DB::table('report')->get();

        if ($reports->isEmpty()) {
            $this->command->info("No reports found. Seed Reports first.");
            return;
        }

        // Get all caretakers
        $caretakers = DB::table('users')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('roles.name', 'caretaker')
            ->pluck('users.id')
            ->toArray();

        if (empty($caretakers)) {
            $this->command->info("No caretakers found. Seed Users & assign caretaker role first.");
            return;
        }

        // Remarks templates
        $successRemarks = [
            'Animal(s) successfully rescued and brought to shelter. All animals are in stable condition.',
            'Rescue operation completed. Animals secured and transported safely to the facility.',
            'Successfully rescued and relocated animals to shelter. Initial health check completed.',
            'Animals rescued without complications. Currently under observation at the shelter.',
            'Rescue mission accomplished. All animals have been safely recovered and are receiving care.',
            'Operation successful. Animals are now safe at the shelter and receiving medical attention.',
            'Animals successfully rescued from reported location. No injuries sustained during rescue.',
            'Rescue completed successfully. Animals are calm and adapting well to shelter environment.',
            'All animals safely rescued and transported. Veterinary assessment scheduled.',
            'Successful rescue operation. Animals are healthy and have been assigned to shelter sections.',
        ];

        $failedRemarks = [
            'Animals could not be located at the reported address. Area searched thoroughly.',
            'Rescue operation failed. Animals had already left the location before team arrival.',
            'Unable to complete rescue due to dangerous location conditions. Will retry with proper equipment.',
            'Animals were too scared and fled before rescue team could secure them safely.',
            'Location access denied by property owner. Legal intervention required.',
            'Rescue attempt failed. Animals were already rescued by another organization.',
            'Could not locate animals despite multiple search attempts in the reported area.',
            'Weather conditions made rescue unsafe. Operation postponed for animal and team safety.',
            'Animals in location too aggressive to approach safely. Specialist team required.',
            'Report deemed inaccurate upon arrival. No animals found at specified location.',
        ];

        $scheduledRemarks = [
            'Rescue operation scheduled. Team will be dispatched within 24-48 hours.',
            'Rescue date and time confirmed. Caretaker team assigned and notified.',
            'Operation planned for next available window. Resources being prepared.',
            'Scheduled for rescue. Awaiting optimal conditions and team availability.',
        ];

        $inProgressRemarks = [
            'Rescue team currently on-site. Operation in progress.',
            'Caretakers are actively working to secure the animals safely.',
            'Rescue operation underway. Team is assessing situation and planning approach.',
            'In the process of rescuing animals. Updates will be provided upon completion.',
        ];

        $pendingRemarks = [
            'Awaiting initial assessment and resource allocation.',
            'Report received and under review. Rescue team will be assigned shortly.',
            'Pending approval and scheduling. Priority level being determined.',
            'Awaiting caretaker availability for rescue operation.',
        ];

        $rescues = [];
        $statusCounts = [
            'Success' => 0,
            'Failed' => 0,
            'Scheduled' => 0,
            'In Progress' => 0,
            'Pending' => 0,
        ];

        foreach ($reports as $report) {

            // 🎯 Keep 20% of reports PENDING (no rescue record)
            if (rand(1, 100) <= 20) {
                continue;
            }

            $remarks = '';

            // 🎯 SUCCESS = 40% chance
            if (rand(1, 100) <= 40) {
                $status = 'Success';
                $remarks = $successRemarks[array_rand($successRemarks)];

                // 🔥 Update related report status to Resolved
                DB::table('report')
                    ->where('id', $report->id)
                    ->update([
                        'report_status' => 'Resolved',
                        'updated_at'    => now(),
                    ]);

                $statusCounts['Success']++;
            }
            else {
                // Remaining 60% get random non-success status
                $statusOptions = [
                    'Failed' => 30,      // 30% chance
                    'Scheduled' => 25,   // 25% chance
                    'In Progress' => 20, // 20% chance
                    'Pending' => 25,     // 25% chance
                ];

                $rand = rand(1, 100);
                $cumulative = 0;

                foreach ($statusOptions as $statusOption => $probability) {
                    $cumulative += $probability;
                    if ($rand <= $cumulative) {
                        $status = $statusOption;
                        break;
                    }
                }

                // Assign appropriate remarks based on status
                switch ($status) {
                    case 'Failed':
                        $remarks = $failedRemarks[array_rand($failedRemarks)];
                        break;
                    case 'Scheduled':
                        $remarks = $scheduledRemarks[array_rand($scheduledRemarks)];
                        break;
                    case 'In Progress':
                        $remarks = $inProgressRemarks[array_rand($inProgressRemarks)];
                        break;
                    case 'Pending':
                        $remarks = $pendingRemarks[array_rand($pendingRemarks)];
                        break;
                }

                $statusCounts[$status]++;
            }

            // Rescue should be created some hours after the report
            $rescueDate = Carbon::parse($report->created_at)->addHours(rand(1, 48));

            $rescues[] = [
                'status'      => $status,
                'remarks'     => $remarks,
                'reportID'    => $report->id,
                'caretakerID' => $caretakers[array_rand($caretakers)],
                'created_at'  => $rescueDate,
                'updated_at'  => $rescueDate,
            ];
        }


        if (!empty($rescues)) {
            // Insert rescues in chunks to avoid SQL Server 2100 parameter limit
            // Each rescue has 6 columns, so chunk size of 300 = 1800 parameters (safe for SQL Server)
            $chunkSize = 300;
            $totalInserted = 0;

            foreach (array_chunk($rescues, $chunkSize) as $chunk) {
                DB::table('rescue')->insert($chunk);
                $totalInserted += count($chunk);
                $this->command->info("Inserted {$totalInserted} / " . count($rescues) . " rescues...");
            }
        }

        $this->command->info('');
        $this->command->info('=================================');
        $this->command->info('Rescue Seeding Completed!');
        $this->command->info('=================================');
        $this->command->info("Total rescue records created: " . count($rescues));
        $this->command->info('');
        $this->command->info('Status Distribution:');
        foreach ($statusCounts as $status => $count) {
            $percentage = count($rescues) > 0 ? round(($count / count($rescues)) * 100, 1) : 0;
            $this->command->info("  - {$status}: {$count} ({$percentage}%)");
        }
        $this->command->info('=================================');
    }
}
