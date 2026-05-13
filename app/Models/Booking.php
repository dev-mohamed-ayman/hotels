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