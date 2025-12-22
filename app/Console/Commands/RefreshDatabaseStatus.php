<?php

namespace App\Console\Commands;

use App\Services\DatabaseConnectionChecker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class RefreshDatabaseStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:refresh-status {--silent : Run without output}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh database connection status cache (runs via scheduler)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $checker = app(DatabaseConnectionChecker::class);
        $silent = $this->option('silent');

        // Get previous status from cache
        $cacheKey = 'db_connection_status';
        $previousStatus = null;

        try {
            $previousStatus = Cache::get($cacheKey);
        } catch (\Exception $e) {
            // Try file cache
            try {
                $previousStatus = Cache::store('file')->get($cacheKey);
            } catch (\Exception $e2) {
                // No previous status available
            }
        }

        // Force fresh check and update cache
        $currentStatus = $checker->checkAll(false);

        // Detect changes if we have previous status
        if ($previousStatus) {
            $changes = $this->detectChanges($previousStatus, $currentStatus);

            if (!empty($changes)) {
                // Log significant changes
                Log::info('Database connection status changed', [
                    'changes' => $changes,
                    'timestamp' => now()->toDateTimeString(),
                ]);

                if (!$silent) {
                    $this->warn('Database status changes detected:');
                    foreach ($changes as $change) {
                        $this->line($change);
                    }
                }
            }
        }

        if (!$silent) {
            $connected = array_filter($currentStatus, fn($db) => $db['connected']);
            $this->info(sprintf(
                'Database status refreshed: %d/%d online',
                count($connected),
                count($currentStatus)
            ));
        }

        return Command::SUCCESS;
    }

    /**
     * Detect changes between two status arrays
     *
     * @param array $previous
     * @param array $current
     * @return array
     */
    private function detectChanges(array $previous, array $current): array
    {
        $changes = [];

        foreach ($current as $connection => $info) {
            $wasConnected = $previous[$connection]['connected'] ?? false;
            $isConnected = $info['connected'];

            if ($wasConnected !== $isConnected) {
                $status = $isConnected ? 'CAME ONLINE' : 'WENT OFFLINE';
                $changes[] = "{$connection} ({$info['module']}) {$status}";
            }
        }

        return $changes;
    }
}
