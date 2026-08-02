<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transactionable_id',
        'transactionable_type',
        'currency_id',
        'reference',
        'description',
        'amount',
        'type', // debit, credit
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:'.config('numbers.decimals', 3),
        ];
    }

    public function transactionable()
    {
        return $this->morphTo();
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }
}
