<?php

namespace App\Observers;

use App\Models\WalletTransaction;

class WalletTransactionObserver
{
    /**
     * Handle the WalletTransaction "created" event.
     */
    public function created(WalletTransaction $walletTransaction): void
    {
        $holder = $walletTransaction->transactionable;
        if (!$holder) return;

        if ($walletTransaction->type === 'credit') {
            $holder->increment('wallet', $walletTransaction->amount);
        } else {
            $holder->decrement('wallet', $walletTransaction->amount);
        }
    }

    /**
     * Handle the WalletTransaction "updated" event.
     */
    public function updated(WalletTransaction $walletTransaction): void
    {
        $holder = $walletTransaction->transactionable;
        if (!$holder) return;

        // Revert old transaction
        $originalAmount = $walletTransaction->getOriginal('amount');
        $originalType = $walletTransaction->getOriginal('type');

        if ($originalType === 'credit') {
            $holder->decrement('wallet', $originalAmount);
        } else {
            $holder->increment('wallet', $originalAmount);
        }

        // Apply new transaction
        if ($walletTransaction->type === 'credit') {
            $holder->increment('wallet', $walletTransaction->amount);
        } else {
            $holder->decrement('wallet', $walletTransaction->amount);
        }
    }

    /**
     * Handle the WalletTransaction "deleted" event.
     */
    public function deleted(WalletTransaction $walletTransaction): void
    {
        $holder = $walletTransaction->transactionable;
        if (!$holder) return;

        if ($walletTransaction->type === 'credit') {
            $holder->decrement('wallet', $walletTransaction->amount);
        } else {
            $holder->increment('wallet', $walletTransaction->amount);
        }
    }
}
