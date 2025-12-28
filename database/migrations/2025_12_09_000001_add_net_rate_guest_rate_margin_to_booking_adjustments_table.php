<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('booking_adjustments', function (Blueprint $table) {
            $table->decimal('net_rate', 10, 2)->nullable()->after('amount');
            $table->decimal('guest_rate', 10, 2)->nullable()->after('net_rate');
            $table->decimal('margin', 10, 2)->nullable()->after('guest_rate');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_adjustments', function (Blueprint $table) {
            $table->dropColumn(['net_rate', 'guest_rate', 'margin']);
        });
    }
};













