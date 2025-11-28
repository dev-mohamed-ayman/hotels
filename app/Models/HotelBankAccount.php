<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HotelBankAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'currency_id',
        'bank_name',
        'account_number',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
