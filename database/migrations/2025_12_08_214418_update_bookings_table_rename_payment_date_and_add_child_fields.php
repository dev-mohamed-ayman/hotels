<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For SQLite compatibility, we need to recreate the column
        if (DB::getDriverName() === 'sqlite') {
            // SQLite doesn't support renameColumn, so we'll add new column and copy data
            Schema::table('bookings', function (Blueprint $table) {
                $table->date('option_date')->nullable()->after('nights');
            });

            // Copy data from payment_date to option_date
            DB::statement('UPDATE bookings SET option_date = payment_date');

            // Drop old column (SQLite limitation - need to recreate table)
            // We'll keep both columns for now and handle in application code
        } else {
            Schema::table('bookings', function (Blueprint $table) {
                $table->renameColumn('payment_date', 'option_date');
            });
        }

        // Add child price and margin fields
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('child_price', 10, 2)->default(0)->after('total_amount');
            $table->decimal('child_margin', 10, 2)->default(0)->after('child_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['child_price', 'child_margin']);
        });

        if (DB::getDriverName() !== 'sqlite') {
            Schema::table('bookings', function (Blueprint $table) {
                $table->renameColumn('option_date', 'payment_date');
            });
        }
    }
};
