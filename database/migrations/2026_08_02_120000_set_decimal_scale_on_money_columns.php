<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Money columns that must accept fractional values, grouped by table.
     * Each entry is [column, nullable, default].
     */
    private array $columns = [
        'bookings' => [
            ['total_amount', false, null],
            ['net_amount', false, 0],
            ['child_price', false, 0],
            ['child_margin', false, 0],
            ['paid_amount', false, 0],
            ['hotel_paid_amount', false, 0],
        ],
        'booking_rooms' => [
            ['price', false, null],
            ['margin', false, 0],
            ['child_price', false, 0],
            ['child_margin', false, 0],
        ],
        'booking_adjustments' => [
            ['amount', false, null],
            ['net_rate', true, null],
            ['guest_rate', true, null],
            ['margin', true, null],
        ],
        'hotels' => [
            ['wallet', false, 0],
        ],
        'customers' => [
            ['wallet', false, 0],
        ],
        'wallet_transactions' => [
            ['amount', false, null],
        ],
    ];

    public function up(): void
    {
        $this->apply(
            (int) config('numbers.precision', 15),
            (int) config('numbers.decimals', 3)
        );
    }

    public function down(): void
    {
        // Previous schema stored two decimal places.
        $this->apply(10, 2);
    }

    private function apply(int $precision, int $scale): void
    {
        foreach ($this->columns as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            Schema::table($table, function (Blueprint $blueprint) use ($table, $columns, $precision, $scale) {
                foreach ($columns as [$column, $nullable, $default]) {
                    if (! Schema::hasColumn($table, $column)) {
                        continue;
                    }

                    $definition = $blueprint->decimal($column, $precision, $scale)->change();

                    if ($nullable) {
                        $definition->nullable();
                    }

                    if ($default !== null) {
                        $definition->default($default);
                    }
                }
            });
        }
    }
};
