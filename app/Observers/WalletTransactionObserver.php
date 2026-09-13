<?php

namespace App\Observers;

use App\Models\WalletTransaction;

class WalletTransactionObserver
{
    /**
     * Handle the WalletTransaction "created" event.
     *
     * Debit adds to the wallet, credit deducts — the same convention the
     * ledger helpers (signed_amount, balanceSum) and the UI use.
     */
    public function created(WalletTransaction $walletTransaction): void
    {
        $holder = $walletTransaction->transactionable;
        if (!$holder) return;

        if ($walletTransaction->type === 'credit') {
            $holder->decrement('wallet', $walletTransaction->amount);
        } else {
            $holder->increment('wallet', $walletTransaction->amount);
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
            $holder->increment('wallet', $originalAmount);
        } else {
            $holder->decrement('wallet', $originalAmount);
        }

        // Apply new transaction
        if ($walletTransaction->type === 'credit') {
            $holder->decrement('wallet', $walletTransaction->amount);
        } else {
            $holder->increment('wallet', $walletTransaction->amount);
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
            $holder->increment('wallet', $walletTransaction->amount);
        } else {
            $holder->decrement('wallet', $walletTransaction->amount);
        }
    }
}
