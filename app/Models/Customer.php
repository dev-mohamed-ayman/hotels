<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone_1',
        'phone_2',
        'email',
        'address',
        'notes',
        'type',
        'status',
        'priority',
        'source',
    ];

    protected $casts = [
        'type' => 'string',
        'status' => 'string',
        'priority' => 'string',
        'source' => 'string',
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
}
