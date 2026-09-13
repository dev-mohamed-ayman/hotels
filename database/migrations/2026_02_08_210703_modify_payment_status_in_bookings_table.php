<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ENUM via raw ALTER is MySQL-only; other drivers keep the plain
        // string column from the original create migration.
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE bookings MODIFY COLUMN payment_status ENUM('paid', 'unpaid', 'partial', 'revised', 'overpaid') NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement("ALTER TABLE bookings MODIFY COLUMN payment_status ENUM('paid', 'unpaid', 'partial', 'revised') NULL");
    }
};
