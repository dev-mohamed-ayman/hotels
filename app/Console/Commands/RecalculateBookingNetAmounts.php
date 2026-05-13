<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RecalculateBookingNetAmounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:recalculate-net-amounts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate net_amount for all bookings based on rooms and adjustments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $bookings = \App\Models\Booking::with(['rooms', 'adjustments'])->get();
        $bar = $this->output->createProgressBar(count($bookings));
        $bar->start();

        foreach ($bookings as $booking) {
            $netAmount = 0;
            $nights = $booking->nights;

            // Calculate rooms net amount
            foreach ($booking->rooms as $room) {
                // Room Price (Net) * Count * Nights
                $netAmount += ($room->price * $room->room_count * $nights);

                // Child Price (Net) * Count * Nights
                // Note: child_price in DB is usually the net price if separate from margin
                $childCount = $room->child_count ?? 0;
                $childPrice = $room->child_price ?? 0;
                $netAmount += ($childPrice * $childCount * $nights);
            }

            // Calculate Adjustments
            $additions = $booking->adjustments->where('type', 'addition')->sum('net_rate');
            $discounts = $booking->adjustments->where('type', 'discount')->sum('net_rate');

            $netAmount += $additions;
            $netAmount -= $discounts;

            // Recalculate payment_status based on new net_amount (use round() to avoid float == bugs)
            $paid = round((float) $booking->paid_amount, 2);
            $net  = round((float) $netAmount,            2);
            if ($paid <= 0) {
                $paymentStatus = 'unpaid';
            } elseif ($paid < $net) {
                $paymentStatus = 'partial';
            } elseif ($paid === $net) {
                $paymentStatus = 'paid';
            } else {
                $paymentStatus = 'overpaid';
            }

            $booking->update([
                'net_amount' => $netAmount,
                'payment_status' => $paymentStatus,
            ]);

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('All bookings have been recalculated.');
    }
}
