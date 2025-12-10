<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        /**
         * Step 1: Create base tables (no FKs yet)
         */
        Schema::create('medical', function (Blueprint $table) {
            $table->id();
            $table->string('treatment_type', 100)->nullable();
            $table->text('diagnosis')->nullable();
            $table->text('action')->nullable();
            $table->text('remarks')->nullable();
            $table->decimal('costs', 10, 2)->nullable();
            $table->unsignedBigInteger('vetID')->nullable();
            $table->unsignedBigInteger('animalID')->nullable();
            $table->timestamps();

            // Add indexes for foreign key columns
            $table->index('vetID');
            $table->index('animalID');
        });

        Schema::create('category', function (Blueprint $table) {
            $table->id();
            $table->string('main', 255)->nullable();
            $table->string('sub', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->string('item_name', 255);
            $table->integer('quantity')->default(0);
            $table->string('brand', 255)->nullable();
            $table->decimal('weight', 10, 2)->nullable();
            $table->string('status', 50)->nullable();
            $table->unsignedBigInteger('slotID')->nullable();
            $table->unsignedBigInteger('categoryID')->nullable();
            $table->timestamps();

            // Add indexes for foreign key columns
            $table->index('slotID');
            $table->index('categoryID');
        });

        Schema::create('booking', function (Blueprint $table) {
            $table->id();
            $table->date('appointment_date')->nullable();
            $table->time('appointment_time');
            $table->string('status', 50)->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('userID')->nullable();
            $table->timestamps();

            // Add index for foreign key column
            $table->index('userID');
        });

        Schema::create('transaction', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 10, 2)->nullable();
            $table->string('status', 50)->nullable();
            $table->text('remarks')->nullable();
            $table->string('type', 50)->nullable();
            $table->string('bill_code', 100)->nullable();
            $table->string('reference_no', 100)->nullable();
            $table->unsignedBigInteger('userID')->nullable();
            $table->timestamps();

            // Add index for foreign key column
            $table->index('userID');
        });

        Schema::create('adoption', function (Blueprint $table) {
            $table->id();
            $table->decimal('fee', 10, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->unsignedBigInteger('bookingID')->nullable();
            $table->unsignedBigInteger('transactionID')->nullable();
            $table->timestamps();

            // Add indexes for foreign key columns
            $table->index('bookingID');
            $table->index('transactionID');
        });

        /**
         * Step 2: Add foreign key constraints
         */
        // Detect database driver
        $driver = DB::connection()->getDriverName();

        Schema::table('medical', function (Blueprint $table) {
            $table->foreign('vetID')
                ->references('id')
                ->on('vet')
                ->onDelete('set null');

            $table->foreign('animalID')
                ->references('id')
                ->on('animal')
                ->onDelete('cascade');
        });

        Schema::table('inventory', function (Blueprint $table) {
            $table->foreign('slotID')
                ->references('id')
                ->on('slot')
                ->onDelete('set null');

            $table->foreign('categoryID')
                ->references('id')
                ->on('category')
                ->onDelete('set null');
        });

        Schema::table('booking', function (Blueprint $table) {
            $table->foreign('userID')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::table('transaction', function (Blueprint $table) {
            $table->foreign('userID')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });

        Schema::table('adoption', function (Blueprint $table) use ($driver) {
            $table->foreign('bookingID')
                ->references('id')
                ->on('booking')
                ->onDelete('cascade');

            // Use NO ACTION for SQL Server to avoid multiple cascade paths
            // (booking->userID and transaction->userID both cascade from users)
            if ($driver === 'sqlsrv') {
                $table->foreign('transactionID')
                    ->references('id')
                    ->on('transaction')
                    ->onDelete('no action');
            } else {
                $table->foreign('transactionID')
                    ->references('id')
                    ->on('transaction')
                    ->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('adoption', function (Blueprint $table) {
            $table->dropForeign(['bookingID']);
            $table->dropForeign(['transactionID']);
        });

        Schema::table('transaction', function (Blueprint $table) {
            $table->dropForeign(['userID']);
        });

        Schema::table('booking', function (Blueprint $table) {
            $table->dropForeign(['userID']);
        });

        Schema::table('inventory', function (Blueprint $table) {
            $table->dropForeign(['slotID']);
            $table->dropForeign(['categoryID']);
        });

        Schema::table('medical', function (Blueprint $table) {
            $table->dropForeign(['vetID']);
            $table->dropForeign(['animalID']);
        });

        /**
         * Drop tables
         */
        Schema::dropIfExists('adoption');
        Schema::dropIfExists('transaction');
        Schema::dropIfExists('booking');
        Schema::dropIfExists('inventory');
        Schema::dropIfExists('category');
        Schema::dropIfExists('medical');
    }
};
