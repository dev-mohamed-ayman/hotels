<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Booking;

class FixBookingPaymentStatuses extends Command
{
    protected $signature = 'bookings:fix-payment-statuses';
    protected $description = 'Recalculate payment_status for all bookings based on paid_amount vs net_amount';

    public function handle()
    {
        $bookings = Booking::all();
        $bar = $this->output->createProgressBar(count($bookings));
        $bar->start();

        $fixed = 0;
        foreach ($bookings as $booking) {
            $paid = round((float) $booking->paid_amount, 2);
            $net  = round((float) $booking->net_amount,  2);
            if ($paid <= 0) {
                $newStatus = 'unpaid';
            } elseif ($paid < $net) {
                $newStatus = 'partial';
            } elseif ($paid === $net) {
                $newStatus = 'paid';
            } else {
                $newStatus = 'overpaid';
            }

            if ($booking->payment_status !== $newStatus) {
                $booking->update(['payment_status' => $newStatus]);
                $fixed++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Done! Fixed {$fixed} bookings.");
    }
}
