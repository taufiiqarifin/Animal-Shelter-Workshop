<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Carbon\Carbon;

class ReportSeeder extends Seeder
{
    public function run()
    {
        // Load CSV
        $csvPath = database_path('seeders/report.csv');
        $rows = array_map('str_getcsv', file($csvPath));
        $header = array_shift($rows); // first row as header

        $data = [];
        foreach ($rows as $row) {
            $data[] = array_combine($header, $row);
        }

        // Get public users (exclude admin & caretaker)
        $excludedRoles = ['admin', 'caretaker'];
        $userIDs = User::whereDoesntHave('roles', function ($q) use ($excludedRoles) {
            $q->whereIn('name', $excludedRoles);
        })->pluck('id')->toArray();

        if (empty($userIDs)) {
            $this->command->info('No eligible users found!');
            return;
        }

        $reportStatuses = ['Pending', 'In Progress', 'Resolved', 'Closed'];
        $reports = [];

        // Generate 300 reports
        for ($i = 0; $i < 300; $i++) {

            $row = $data[array_rand($data)]; // pick random CSV row

            // Random date in last 2 years
            $createdAt = Carbon::now()->subDays(rand(0, 730));

            $reports[] = [
                'latitude'      => $row['latitude'],
                'longitude'     => $row['longitude'],
                'address'       => $row['address'],
                'city'          => $row['city'],
                'state'         => $row['state'],
                'report_status' => $row['report_status'], // or randomElement($reportStatuses)
                'description'   => $row['description'],

                // Use CSV userID OR assign random public user
                'userID'        => $row['userID'], 
                // 'userID'     => $faker->randomElement($userIDs),

                'created_at'    => $createdAt,
                'updated_at'    => $createdAt,
            ];
        }

        DB::table('report')->insert($reports);

        $this->command->info("300 reports generated successfully!");
    }
}
