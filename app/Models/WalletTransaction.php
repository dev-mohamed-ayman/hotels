<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;

class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transactionable_id',
        'transactionable_type',
        'currency_id',
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

    /**
     * The amount as it affects the wallet: a debit adds, a credit deducts.
     */
    public function getSignedAmountAttribute(): float
    {
        return $this->type === 'credit'
            ? -(float) $this->amount
            : (float) $this->amount;
    }

    /**
     * SQL sum turning transactions into a balance (debit adds, credit deducts).
     */
    public static function balanceSum(): string
    {
        return 'SUM(CASE WHEN type = "debit" THEN amount ELSE -amount END)';
    }

    /**
     * The balance sum aliased for a select, defaulting to 0 when there are no rows.
     */
    public static function balanceExpression(string $alias = 'balance'): string
    {
        return 'COALESCE('.static::balanceSum().', 0) as '.$alias;
    }

    /**
     * Set a `running_balance` attribute on every given transaction.
     *
     * Balances run per currency in chronological order and start from the balance
     * the wallet already held before the oldest transaction of the set, so a
     * filtered or paginated list still shows the true balance after each row.
     *
     * @return array<int, float> the opening balance per currency id
     */
    public static function attachRunningBalance($holder, $transactions): array
    {
        $items = static::chronological($transactions);

        if ($items->isEmpty()) {
            return [];
        }

        $opening = static::openingBalances($holder, $items);
        $running = $opening;

        foreach ($items as $transaction) {
            $currencyId = (int) $transaction->currency_id;
            $running[$currencyId] = ($running[$currencyId] ?? 0) + $transaction->signed_amount;
            $transaction->setAttribute('running_balance', $running[$currencyId]);
        }

        return $opening;
    }

    /**
     * Balance per currency held before the oldest transaction of the given set.
     *
     * @return array<int, float>
     */
    public static function openingBalances($holder, $transactions): array
    {
        $opening = [];

        foreach (static::chronological($transactions)->groupBy('currency_id') as $currencyId => $group) {
            $first = $group->first();

            $opening[(int) $currencyId] = (float) $holder->walletTransactions()
                ->where('currency_id', $currencyId)
                ->where(function ($query) use ($first) {
                    $query->where('created_at', '<', $first->created_at)
                        ->orWhere(function ($query) use ($first) {
                            $query->where('created_at', $first->created_at)
                                ->where('id', '<', $first->id);
                        });
                })
                ->reorder()
                ->selectRaw(static::balanceExpression())
                ->value('balance');
        }

        return $opening;
    }

    /**
     * The given transactions, oldest first. Accepts a collection or a paginator.
     */
    public static function chronological($transactions): Collection
    {
        if (is_object($transactions) && method_exists($transactions, 'getCollection')) {
            $transactions = $transactions->getCollection();
        }

        return collect($transactions)
            ->sortBy([['created_at', 'asc'], ['id', 'asc']])
            ->values();
    }
}
