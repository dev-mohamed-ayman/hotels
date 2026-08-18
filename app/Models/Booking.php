<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
    ];

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