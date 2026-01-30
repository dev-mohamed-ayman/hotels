<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Customer extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'name',
        'nationality',
        'phone_1',
        'phone_2',
        'email',
        'address',
        'notes',
        'type',
        'status',
        'priority',
        'source',
        'wallet',
    ];

    protected $casts = [
        'type' => 'string',
        'status' => 'string',
        'priority' => 'string',
        'source' => 'string',
        'wallet' => 'decimal:2',
    ];

    public function hotels()
    {
        return $this->belongsToMany(Hotel::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }

    public function latestFollowUp()
    {
        return $this->hasOne(FollowUp::class)->latestOfMany();
    }

    public function walletTransactions()
    {
        return $this->morphMany(WalletTransaction::class, 'transactionable')->orderBy('created_at', 'desc');
    }
}
