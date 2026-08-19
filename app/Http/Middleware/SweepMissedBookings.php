<?php

namespace App\Http\Middleware;

use App\Models\Booking;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps the "missed" payment status current without a cron scheduler, which
 * this application's shared host does not offer.
 *
 * Every payment edit already re-derives payment_status at write time, so the
 * only event that can leave the column stale is the calendar rolling over to a
 * new day. This runs the sweep on the first request of each day and does
 * nothing for the rest of it.
 *
 * Cache::add() is atomic on the database cache store this app uses (it is an
 * insert against a unique key), so two simultaneous first requests cannot both
 * win the claim.
 */
class SweepMissedBookings
{
    public function handle(Request $request, Closure $next): Response
    {
        // Only signed-in users ever see a payment status, so guest traffic
        // (the login page, asset requests) should not pay for the daily claim.
        if (Auth::check()) {
            $this->sweepOncePerDay();
        }

        return $next($request);
    }

    private function sweepOncePerDay(): void
    {
        $key = 'bookings:missed-swept:'.today()->toDateString();

        // Nothing in here may take a page down with it: this is unattended
        // housekeeping riding along on somebody else's request. Note the cache
        // store is the database too, so even claiming the day can fail.
        try {
            // Whoever wins the claim runs the sweep; everyone else skips
            // straight through. The key is date-stamped, so it re-arms itself
            // at midnight.
            if (! Cache::add($key, true, now()->addDay())) {
                return;
            }
        } catch (\Throwable $e) {
            Log::error('Could not claim the missed-bookings sweep: '.$e->getMessage(), ['exception' => $e]);

            return;
        }

        try {
            Booking::sweepMissed();
        } catch (\Throwable $e) {
            // Release the claim so the next request retries rather than
            // leaving the statuses stale for a whole day.
            rescue(fn () => Cache::forget($key), report: false);

            Log::error('Failed to sweep missed bookings: '.$e->getMessage(), ['exception' => $e]);
        }
    }
}
