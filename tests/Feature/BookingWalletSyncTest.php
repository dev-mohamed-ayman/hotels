<?php

use App\Models\Booking;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Hotel;
use App\Models\WalletTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * The wallet scenario for hotel payments:
 * a 1,200 booking whose hotel gets 1,000 must move 1,200 out of the
 * customer wallet once (and only once) the hotel side is settled,
 * with partial hotel payments debiting the customer in step.
 */
uses(RefreshDatabase::class);

beforeEach(function () {
    $this->currency = Currency::create(['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$']);
    $this->customer = Customer::create(['name' => 'Acme Tours', 'phone_1' => '01000000000']);
    $this->hotel = Hotel::create(['name' => 'Grand Plaza', 'address' => 'Cairo']);
});

function walletBooking(array $overrides = []): Booking
{
    $booking = new Booking(array_merge([
        'code' => 'TST 001',
        'customer_id' => test()->customer->id,
        'hotel_id' => test()->hotel->id,
        'currency_id' => test()->currency->id,
        'check_in' => now()->addDays(7)->toDateString(),
        'check_out' => now()->addDays(9)->toDateString(),
        'nights' => 2,
        'net_amount' => 1000,
        'total_amount' => 1200,
        'paid_amount' => 0,
        'hotel_paid_amount' => 0,
        'status' => 'confirmed',
        'payment_status' => 'unpaid',
    ], $overrides));

    $booking->save();

    return $booking;
}

function walletBalance($holder): float
{
    return (float) WalletTransaction::query()
        ->where('transactionable_type', get_class($holder))
        ->where('transactionable_id', $holder->id)
        ->selectRaw(WalletTransaction::balanceExpression())
        ->value('balance');
}

test('an unpaid booking moves no money in either wallet', function () {
    walletBooking();

    expect(WalletTransaction::count())->toBe(0);
});

test('a partial hotel payment debits the customer by the same amount', function () {
    $booking = walletBooking();

    $booking->update(['hotel_paid_amount' => 600]);

    expect(walletBalance($this->hotel))->toBe(600.0)
        ->and(walletBalance($this->customer))->toBe(-600.0);
});

test('the settlement payment tops the customer debit up to the full guest price', function () {
    $booking = walletBooking();

    $booking->update(['hotel_paid_amount' => 600]);
    $booking->update(['hotel_paid_amount' => 1000]);

    // Hotel received 600 + 400 = 1000 (its full net).
    expect(walletBalance($this->hotel))->toBe(1000.0)
        // Customer was debited 600 + 600 = 1200 (the full guest price).
        ->and(walletBalance($this->customer))->toBe(-1200.0)
        // The settlement posts one top-up credit of 600 on the customer side.
        ->and(WalletTransaction::where('transactionable_type', Customer::class)
            ->where('type', 'credit')->pluck('amount')->map(fn ($a) => (float) $a)->all())->toBe([600.0, 600.0])
        ->and(WalletTransaction::where('transactionable_type', Hotel::class)
            ->where('type', 'debit')->pluck('amount')->map(fn ($a) => (float) $a)->all())->toBe([600.0, 400.0]);
});

test('paying the hotel in one go debits the customer for the full guest price', function () {
    $booking = walletBooking();

    $booking->update(['hotel_paid_amount' => 1000]);

    expect(walletBalance($this->hotel))->toBe(1000.0)
        ->and(walletBalance($this->customer))->toBe(-1200.0);
});

test('reducing the hotel payment reverses the postings', function () {
    $booking = walletBooking();

    $booking->update(['hotel_paid_amount' => 600]);
    $booking->update(['hotel_paid_amount' => 1000]);
    $booking->update(['hotel_paid_amount' => 800]);

    // Hotel back to 800, customer back to the partial rule: debited 800.
    expect(walletBalance($this->hotel))->toBe(800.0)
        ->and(walletBalance($this->customer))->toBe(-800.0);
});

test('sync is idempotent: saving the same amount posts nothing new', function () {
    $booking = walletBooking();

    $booking->update(['hotel_paid_amount' => 600]);
    $count = WalletTransaction::count();

    $booking->syncWallets();
    $booking->syncWallets();

    expect(WalletTransaction::count())->toBe($count);
});

test('overpaying the hotel never debits the customer beyond the guest price', function () {
    $booking = walletBooking();

    $booking->update(['hotel_paid_amount' => 1100]);

    expect(walletBalance($this->hotel))->toBe(1100.0)
        ->and(walletBalance($this->customer))->toBe(-1200.0);
});

test('a pre-feature booking with untracked payments posts only the real difference', function () {
    $booking = walletBooking();

    // Payments that happened before the wallet sync existed, with no linked
    // postings on either wallet.
    $booking->forceFill(['hotel_paid_amount' => 600])->saveQuietly();
    $booking = $booking->fresh();

    // Later edits post just the movement from that point on.
    $booking->update(['hotel_paid_amount' => 800]);

    expect(walletBalance($this->hotel))->toBe(200.0)
        ->and(walletBalance($this->customer))->toBe(-200.0);

    $booking->update(['hotel_paid_amount' => 1000]);

    // The settlement tops the customer up to the remaining 400 to reach
    // 1200 on top of the 600 baseline assumed for the manual era.
    expect(walletBalance($this->hotel))->toBe(400.0)
        ->and(walletBalance($this->customer))->toBe(-600.0)
        ->and((float) $booking->fresh()->wallet_base_hotel)->toBe(600.0)
        ->and((float) $booking->fresh()->wallet_base_customer)->toBe(-600.0);
});

test('the baseline is captured once and later syncs ignore the prior amount', function () {
    $booking = walletBooking();

    $booking->forceFill(['hotel_paid_amount' => 600])->saveQuietly();
    $booking = $booking->fresh();

    $booking->update(['hotel_paid_amount' => 800]);
    $count = WalletTransaction::count();

    $booking->update(['hotel_paid_amount' => 800.01]);
    $booking->syncWallets();

    expect(WalletTransaction::count())->toBe($count + 2)
        ->and(walletBalance($this->hotel))->toBe(200.01);
});
