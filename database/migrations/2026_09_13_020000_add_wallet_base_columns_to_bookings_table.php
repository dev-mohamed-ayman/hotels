<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Wallet postings for bookings that predate the auto wallet sync are not
     * linked to the booking (they were entered by hand, if at all), so the
     * booking's own ledger would start from zero and re-post the whole
     * amount on the next edit. These columns remember the assumed starting
     * balance of such a booking: signed in ledger terms (debit adds, credit
     * deducts) — +what the hotel already received, -what the customer
     * already paid out.
     */
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->decimal('wallet_base_hotel', 12, 3)->default(0)->after('in_payment_list');
            $table->decimal('wallet_base_customer', 12, 3)->default(0)->after('wallet_base_hotel');
        });
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['wallet_base_hotel', 'wallet_base_customer']);
        });
    }
};
