<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('animal_booking', function (Blueprint $table) {
            // Add composite unique index to prevent duplicate bookings
            // This ensures an animal can't have multiple bookings at same date/time
            $table->unique(['animalID', 'bookingID'], 'unique_animal_booking');
        });

        // Optional: Add index on booking date and time for better query performance
        Schema::table('booking', function (Blueprint $table) {
            $table->index(['appointment_date', 'appointment_time', 'status']);
        });
    }

    public function down()
    {
        Schema::table('animal_booking', function (Blueprint $table) {
            $table->dropUnique('unique_animal_booking');
        });

        Schema::table('booking', function (Blueprint $table) {
            $table->dropIndex(['appointment_date', 'appointment_time', 'status']);
        });
    }
};
