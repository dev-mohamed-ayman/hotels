<?php

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;

class MarkMissedBookings extends Command
{
    protected $signature = 'bookings:mark-missed {--dry-run : Report what would change without writing anything}';

    protected $description = 'Mark bookings still owing money after their option date as missed, and un-mark any whose option date moved back into the future';

    public function handle(): int
    {
        if ($this->option('dry-run')) {
            return $this->report();
        }

        $counts = Booking::sweepMissed();

        $this->info("Marked {$counts['missed']} booking(s) as missed.");
        $this->info("Recovered {$counts['recovered']} booking(s) whose option date is no longer past.");

        return self::SUCCESS;
    }

    /**
     * Show the bookings each pass would touch, so the sweep can be inspected
     * against real data before it writes.
     */
    private function report(): int
    {
        $today = today();

        $toMiss = Booking::query()
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->whereNotNull('option_date')
            ->whereDate('option_date', '<', $today)
            ->get(['code', 'option_date', 'payment_status', 'paid_amount', 'net_amount']);

        $toRecover = Booking::query()
            ->where('payment_status', 'missed')
            ->where(fn ($query) => $query
                ->whereNull('option_date')
                ->orWhereDate('option_date', '>=', $today))
            ->get(['code', 'option_date', 'payment_status', 'paid_amount', 'net_amount']);

        $this->info("Would mark as missed: {$toMiss->count()}");
        $this->table(
            ['Code', 'Option date', 'Current', 'Paid', 'Net'],
            $toMiss->take(20)->map(fn (Booking $b) => [
                $b->code,
                optional($b->option_date)->toDateString(),
                $b->payment_status,
                $b->paid_amount,
                $b->net_amount,
            ])->all(),
        );

        $this->info("Would recover: {$toRecover->count()}");
        $this->table(
            ['Code', 'Option date', 'Current', 'Paid', 'Net', 'Becomes'],
            $toRecover->take(20)->map(fn (Booking $b) => [
                $b->code,
                optional($b->option_date)->toDateString(),
                $b->payment_status,
                $b->paid_amount,
                $b->net_amount,
                Booking::derivePaymentStatus((float) $b->paid_amount, (float) $b->net_amount, $b->option_date),
            ])->all(),
        );

        return self::SUCCESS;
    }
}
