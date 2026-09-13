<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The wallet-transaction observer keeps a `wallet` running total on the
     * holder, but only hotels ever got the column — customers had it added
     * out of band, so fresh installs were missing it.
     */
    public function up(): void
    {
        if (!Schema::hasColumn('customers', 'wallet')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->decimal('wallet', 10, 2)->default(0);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('customers', 'wallet')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn('wallet');
            });
        }
    }
};
