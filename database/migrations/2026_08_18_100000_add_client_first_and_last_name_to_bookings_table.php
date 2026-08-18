<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('client_first_name')->nullable()->after('client_name');
            $table->string('client_last_name')->nullable()->after('client_first_name');
        });

        // Backfill the new columns by splitting the existing client_name
        // on the first space (first word = first name, rest = last name).
        DB::table('bookings')
            ->whereNotNull('client_name')
            ->where('client_name', '!=', '')
            ->orderBy('id')
            ->chunkById(200, function ($bookings) {
                foreach ($bookings as $booking) {
                    $name = trim(preg_replace('/\s+/', ' ', $booking->client_name));

                    if ($name === '') {
                        continue;
                    }

                    $parts = explode(' ', $name);
                    $first = array_shift($parts);
                    $last = implode(' ', $parts);

                    DB::table('bookings')->where('id', $booking->id)->update([
                        'client_first_name' => $first,
                        'client_last_name' => $last !== '' ? $last : null,
                    ]);
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['client_first_name', 'client_last_name']);
        });
    }
};
