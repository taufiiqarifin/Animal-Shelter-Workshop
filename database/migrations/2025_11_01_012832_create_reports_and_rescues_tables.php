<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Step 1: Create tables first (without foreign keys)
        Schema::create('report', function (Blueprint $table) {
            $table->id();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('report_status', 50)->nullable();
            $table->text('description')->nullable();
            $table->unsignedBigInteger('userID')->nullable();
            $table->timestamps();

            // Add index for foreign key column
            $table->index('userID');
        });

        Schema::create('rescue', function (Blueprint $table) {
            $table->id();
            $table->string('status', 50)->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('reportID')->nullable();
            $table->unsignedBigInteger('caretakerID')->nullable();
            $table->timestamps();

            // Add indexes for foreign key columns
            $table->index('reportID');
            $table->index('caretakerID');
        });

        // Step 2: Add foreign key constraints (after tables exist)
        Schema::table('report', function (Blueprint $table) {
            $table->foreign('userID')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        // Detect database driver
        $driver = DB::connection()->getDriverName();

        Schema::table('rescue', function (Blueprint $table) use ($driver) {
            $table->foreign('reportID')
                ->references('id')
                ->on('report')
                ->onDelete('cascade');

            // Use NO ACTION for SQL Server to avoid multiple cascade paths
            if ($driver === 'sqlsrv') {
                $table->foreign('caretakerID')
                    ->references('id')
                    ->on('users')
                    ->onDelete('no action');
            } else {
                $table->foreign('caretakerID')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        // Drop foreign keys first
        Schema::table('rescue', function (Blueprint $table) {
            $table->dropForeign(['reportID']);
            $table->dropForeign(['caretakerID']);
        });

        Schema::table('report', function (Blueprint $table) {
            $table->dropForeign(['userID']);
        });

        // Then drop the tables
        Schema::dropIfExists('rescue');
        Schema::dropIfExists('report');
    }
};
