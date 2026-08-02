<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'company_name',
        'address',
        'is_active',
        'wallet',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'wallet' => 'decimal:'.config('numbers.decimals', 3),
        ];
    }

    public function walletTransactions()
    {
        return $this->morphMany(WalletTransaction::class, 'transactionable')->orderBy('created_at', 'desc');
    }

    public function bankAccounts()
    {
        return $this->hasMany(HotelBankAccount::class);
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
