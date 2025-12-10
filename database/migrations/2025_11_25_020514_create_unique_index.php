<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('animal_booking', function (Blueprint $table) {
            // Add composite unique index to prevent duplicate bookings
            // This ensures an animal can't have multiple bookings at same date/time
            $table->unique(['animalID', 'bookingID'], 'unique_animal_booking');
        });

        // Add index on booking date and time for better query performance
        Schema::table('booking', function (Blueprint $table) {
            $table->index(['appointment_date', 'appointment_time', 'status'], 'idx_booking_schedule');
        });
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        // Check if unique index exists before dropping
        if ($this->indexExists('animal_booking', 'unique_animal_booking')) {
            Schema::table('animal_booking', function (Blueprint $table) use ($driver) {
                // MySQL doesn't allow dropping a unique index if it's used by a foreign key
                // For MySQL, we need to drop the foreign keys first, then the unique index, then recreate the foreign keys
                if ($driver === 'mysql') {
                    // Drop foreign keys first
                    $table->dropForeign(['animalID']);
                    $table->dropForeign(['bookingID']);

                    // Drop the unique index
                    $table->dropUnique('unique_animal_booking');

                    // Recreate the foreign keys
                    $table->foreign('bookingID')
                        ->references('id')
                        ->on('booking')
                        ->onDelete('cascade');

                    $table->foreign('animalID')
                        ->references('id')
                        ->on('animal')
                        ->onDelete('cascade');
                } else {
                    // PostgreSQL and SQL Server can drop unique indexes directly
                    $table->dropUnique('unique_animal_booking');
                }
            });
        }

        // Check if index exists before dropping
        if ($this->indexExists('booking', 'idx_booking_schedule')) {
            Schema::table('booking', function (Blueprint $table) {
                $table->dropIndex('idx_booking_schedule');
            });
        }
    }

    /**
     * Check if an index exists on a table
     */
    private function indexExists(string $table, string $index): bool
    {
        $driver = DB::connection()->getDriverName();

        switch ($driver) {
            case 'mysql':
                $result = DB::select(
                    "SHOW INDEX FROM `{$table}` WHERE Key_name = ?",
                    [$index]
                );
                return !empty($result);

            case 'pgsql':
                $result = DB::select(
                    "SELECT indexname FROM pg_indexes WHERE tablename = ? AND indexname = ?",
                    [$table, $index]
                );
                return !empty($result);

            case 'sqlsrv':
                $result = DB::select(
                    "SELECT name FROM sys.indexes WHERE object_id = OBJECT_ID(?) AND name = ?",
                    [$table, $index]
                );
                return !empty($result);

            default:
                return false;
        }
    }
};
