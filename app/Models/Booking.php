<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use App\Traits\LogsActivity;

class Booking extends Model
{
    use LogsActivity;
    
    protected $guarded = [];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'option_date' => 'date',
        'payment_date' => 'date',
        'paid_amount' => 'float',
        'net_amount' => 'float',
        'total_amount' => 'float',
        'hotel_paid_amount' => 'float',
        'child_price' => 'float',
        'child_margin' => 'float',
        'in_payment_list' => 'boolean',
        'wallet_base_hotel' => 'float',
        'wallet_base_customer' => 'float',
    ];

    /**
     * A paid booking has nothing left on the payment list: drop it the moment
     * its status becomes paid, wherever that status was written from.
     *
     * Hotel payments are mirrored into the wallets: every change to
     * hotel_paid_amount re-syncs the booking's postings on the hotel and the
     * customer wallets (see syncWallets()).
     */
    protected static function booted(): void
    {
        static::created(function (Booking $booking) {
            if ((float) $booking->hotel_paid_amount > 0) {
                $booking->syncWallets();
            }
        });

        static::updated(function (Booking $booking) {
            if ($booking->wasChanged('payment_status')
                && $booking->payment_status === 'paid'
                && $booking->in_payment_list) {
                $booking->update(['in_payment_list' => false]);
            }

            if ($booking->wasChanged('hotel_paid_amount')) {
                $booking->syncWallets((float) $booking->getOriginal('hotel_paid_amount'));
            }
        });
    }

    /**
     * Bring the hotel and customer wallets in line with this booking.
     *
     * Sync is target-based: what the booking's own postings (plus any legacy
     * baseline it started from) should total is compared against what has
     * actually been posted, and only the difference is entered — either
     * direction, so corrections and rollbacks reverse themselves.
     *
     * - Hotel wallet   -> receives exactly hotel_paid_amount (debit).
     * - Customer wallet -> is debited in step with the hotel payment while
     *   the hotel is still owed money; the payment that settles the hotel
     *   tops the debit up to the full guest price (total_amount), because a
     *   settled hotel means the booking money is fully in hand.
     *
     * $priorHotelPaid is what the booking already had paid when a sync is
     * triggered by an update. For pre-existing bookings whose payments never
     * produced linked postings, it is captured once as the wallet baseline
     * so later edits only post the real difference.
     */
    public function syncWallets(?float $priorHotelPaid = null): void
    {
        $decimals = (int) config('numbers.decimals', 3);

        $net = round((float) $this->net_amount, $decimals);
        $total = round((float) $this->total_amount, $decimals);
        $hotelPaid = round((float) $this->hotel_paid_amount, $decimals);

        $this->captureLegacyBaseline($priorHotelPaid, $net, $total, $decimals);

        $hotelTarget = max(0.0, $hotelPaid);

        $settled = $hotelPaid > 0 && $net > 0 && $hotelPaid >= $net;
        $customerTarget = $settled ? max($total, $hotelPaid) : max(0.0, $hotelPaid);

        if ($this->hotel) {
            $this->postWalletDelta(
                $this->hotel,
                Hotel::class,
                'wallet_base_hotel',
                $hotelTarget,
                $decimals,
                'Booking '.$this->code.' — hotel payment',
            );
        }

        if ($this->customer) {
            // Customer postings are money taken out: a credit, i.e. negative
            // in ledger terms.
            $this->postWalletDelta(
                $this->customer,
                Customer::class,
                'wallet_base_customer',
                -$customerTarget,
                $decimals,
                'Booking '.$this->code.' — customer collection',
            );
        }
    }

    /**
     * For bookings that carried hotel payments before any wallet posting
     * existed for them, freeze what the wallets must be assumed to already
     * reflect, so the sync only ever posts deltas on top of it.
     */
    private function captureLegacyBaseline(
        ?float $priorHotelPaid,
        float $net,
        float $total,
        int $decimals,
    ): void {
        if ($priorHotelPaid === null || round($priorHotelPaid, $decimals) <= 0) {
            return;
        }

        if ($this->wallet_base_hotel != 0 || $this->wallet_base_customer != 0) {
            return;
        }

        $alreadyLinked = WalletTransaction::query()->where('booking_id', $this->id)->exists();

        if ($alreadyLinked || ! $this->exists) {
            return;
        }

        $priorSettled = $net > 0 && $priorHotelPaid >= $net;

        $this->forceFill([
            'wallet_base_hotel' => round($priorHotelPaid, $decimals),
            'wallet_base_customer' => -round($priorSettled ? $total : $priorHotelPaid, $decimals),
        ])->save();
    }

    /**
     * Post the difference between what a wallet should hold for this booking
     * and what it already holds (linked postings plus the legacy baseline).
     * Both sides are in signed ledger terms (debit adds, credit deducts), so
     * a positive delta is a debit and a negative one is a credit.
     */
    private function postWalletDelta(
        Model $holder,
        string $holderType,
        string $baseColumn,
        float $signedTarget,
        int $decimals,
        string $description,
    ): void {
        $posted = (float) $this->{$baseColumn}
            + (float) WalletTransaction::query()
                ->where('booking_id', $this->id)
                ->where('transactionable_type', $holderType)
                ->reorder()
                ->selectRaw(WalletTransaction::balanceExpression())
                ->value('balance');

        $delta = round($signedTarget - $posted, $decimals);

        if (abs($delta) < (1 / 10 ** max(1, $decimals))) {
            return;
        }

        $holder->walletTransactions()->create([
            'booking_id' => $this->id,
            'currency_id' => $this->currency_id,
            'description' => $description,
            'amount' => abs($delta),
            'type' => $delta > 0 ? 'debit' : 'credit',
        ]);
    }

    /**
     * Client name shortened for exports: "Mohamed Ayman" => "M. Ayman".
     * Falls back to the legacy client_name column, then to the customer name.
     */
    public function getShortClientNameAttribute(): ?string
    {
        $fullName = trim(trim((string) $this->client_first_name).' '.trim((string) $this->client_last_name));

        if ($fullName === '') {
            $fullName = trim((string) $this->client_name);
        }

        if ($fullName === '') {
            $customer = $this->customer;
            $customerName = trim((string) ($customer->name ?? ''));

            // A corporate customer's name is a company, not a person: keep it as it is.
            if (($customer->type ?? null) === 'corporate') {
                return $customerName !== '' ? $customerName : null;
            }

            $fullName = $customerName;
        }

        $parts = preg_split('/\s+/', $fullName, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        // Drop a leading title so "Mr. Edet Cyril" shortens to "E. Cyril".
        $titles = ['mr', 'mrs', 'ms', 'miss', 'mister', 'dr', 'prof', 'eng', 'sir', 'madam'];
        if (count($parts) >= 2 && in_array(mb_strtolower(rtrim($parts[0], '.')), $titles, true)) {
            array_shift($parts);
        }

        if ($parts === []) {
            return null;
        }

        if (count($parts) === 1) {
            return $parts[0];
        }

        $first = array_shift($parts);

        return mb_substr($first, 0, 1).'. '.implode(' ', $parts);
    }

    /**
     * The single source of truth for the payment_status column.
     *
     * Base rules, comparing what the customer paid against the net amount:
     *  paid == 0       -> unpaid
     *  0 < paid < net  -> partial
     *  paid == net     -> paid
     *  paid > net      -> overpaid
     *
     * On top of that, a booking that is still owing money after its payment
     * deadline (option_date) has passed is "missed". Settled bookings are
     * never missed, and a booking with no deadline can never miss one.
     *
     * Amounts are rounded to the configured precision first so that float
     * representation noise cannot flip an == comparison.
     *
     * Note "revised" is deliberately absent: it is a manual override set by
     * the user and never derived, so it survives this calculation untouched.
     */
    public static function derivePaymentStatus(float $paidAmount, float $netAmount, \DateTimeInterface|string|null $optionDate = null): string
    {
        $decimals = (int) config('numbers.decimals', 3);

        $paid = round($paidAmount, $decimals);
        $net  = round($netAmount, $decimals);

        if ($paid <= 0) {
            $status = 'unpaid';
        } elseif ($paid < $net) {
            $status = 'partial';
        } elseif ($paid === $net) {
            $status = 'paid';
        } else {
            $status = 'overpaid';
        }

        if (in_array($status, ['unpaid', 'partial'], true) && static::deadlineHasPassed($optionDate)) {
            return 'missed';
        }

        return $status;
    }

    /**
     * A deadline counts as passed only once the day itself is over, so a
     * booking due today is still on time for the whole of today.
     *
     * Accepts a raw form string as well as a cast date, so controllers can
     * ask about a deadline the user just typed but has not saved yet.
     */
    public static function deadlineHasPassed(\DateTimeInterface|string|null $optionDate): bool
    {
        if ($optionDate instanceof \DateTimeInterface) {
            $deadline = Carbon::instance($optionDate);
        } elseif (is_string($optionDate) && trim($optionDate) !== '') {
            $deadline = Carbon::parse(trim($optionDate));
        } else {
            return false;
        }

        return $deadline->startOfDay()->lt(Carbon::today());
    }

    /**
     * Bring every booking's payment_status in line with today's date.
     *
     * Crossing midnight is the only thing that can invalidate a stored
     * payment_status without the application seeing it: every payment edit
     * already runs through derivePaymentStatus() at write time. So this only
     * has to reconcile the deadline, in two passes:
     *
     *  1. bookings still owing money after their deadline  -> missed
     *  2. missed bookings whose deadline was pushed back   -> back to base
     *
     * Both passes write through the query builder rather than through model
     * instances. That keeps updated_at untouched (so a sweep does not reshuffle
     * the "recently updated" sort order) and keeps a routine, unattended
     * housekeeping pass out of the activity log, where it would otherwise be
     * attributed to whichever user happened to load the first page of the day.
     *
     * Returns the row counts for each pass.
     */
    public static function sweepMissed(): array
    {
        $today = Carbon::today();

        $missed = static::query()
            ->whereIn('payment_status', ['unpaid', 'partial'])
            ->whereNotNull('option_date')
            ->whereDate('option_date', '<', $today)
            ->toBase()
            ->update(['payment_status' => 'missed']);

        // A deadline that is no longer in the past means the booking is owing
        // money again rather than having missed anything. Recompute per row so
        // the paid-vs-net rules stay in one place.
        $recovered = 0;

        static::query()
            ->where('payment_status', 'missed')
            ->where(fn ($query) => $query
                ->whereNull('option_date')
                ->orWhereDate('option_date', '>=', $today))
            ->each(function (self $booking) use (&$recovered) {
                $status = static::derivePaymentStatus(
                    (float) $booking->paid_amount,
                    (float) $booking->net_amount,
                    $booking->option_date,
                );

                static::query()->whereKey($booking->getKey())
                    ->toBase()
                    ->update(['payment_status' => $status]);

                $recovered++;
            });

        return ['missed' => $missed, 'recovered' => $recovered];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(BookingRoom::class);
    }

    public function adjustments(): HasMany
    {
        return $this->hasMany(BookingAdjustment::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(BookingHistory::class)->orderBy('created_at', 'desc');
    }
}