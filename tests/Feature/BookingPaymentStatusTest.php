<?php

use App\Models\Booking;

/**
 * Booking::derivePaymentStatus() is the single source of truth for the
 * payment_status column. It layers the "missed the payment deadline" rule
 * on top of the paid-vs-net rules.
 */

// --- Base rules: paid vs net, with no deadline in play -----------------------

test('no payment is unpaid', function () {
    expect(Booking::derivePaymentStatus(0, 100))->toBe('unpaid');
});

test('payment below the net amount is partial', function () {
    expect(Booking::derivePaymentStatus(40, 100))->toBe('partial');
});

test('payment matching the net amount is paid', function () {
    expect(Booking::derivePaymentStatus(100, 100))->toBe('paid');
});

test('payment above the net amount is overpaid', function () {
    expect(Booking::derivePaymentStatus(150, 100))->toBe('overpaid');
});

// --- The missed rule ---------------------------------------------------------

test('unpaid booking past its option date is missed', function () {
    expect(Booking::derivePaymentStatus(0, 100, today()->subDay()))->toBe('missed');
});

test('partially paid booking past its option date is missed', function () {
    expect(Booking::derivePaymentStatus(40, 100, today()->subDay()))->toBe('missed');
});

test('fully paid booking past its option date is never missed', function () {
    expect(Booking::derivePaymentStatus(100, 100, today()->subDay()))->toBe('paid');
});

test('overpaid booking past its option date is never missed', function () {
    expect(Booking::derivePaymentStatus(150, 100, today()->subDay()))->toBe('overpaid');
});

// --- Deadline boundaries -----------------------------------------------------

test('option date falling today has not passed yet', function () {
    expect(Booking::derivePaymentStatus(0, 100, today()))->toBe('unpaid');
});

test('option date in the future leaves the status alone', function () {
    expect(Booking::derivePaymentStatus(0, 100, today()->addDay()))->toBe('unpaid');
});

test('booking without an option date can never be missed', function () {
    expect(Booking::derivePaymentStatus(0, 100, null))->toBe('unpaid');
});

// --- Recovery ----------------------------------------------------------------

test('paying an overdue booking in full clears the missed status', function () {
    $overdue = today()->subMonth();

    expect(Booking::derivePaymentStatus(0, 100, $overdue))->toBe('missed');
    expect(Booking::derivePaymentStatus(100, 100, $overdue))->toBe('paid');
});

// --- Rounding ----------------------------------------------------------------

test('amounts equal within the configured precision count as paid not missed', function () {
    // 3 decimals configured: these differ only in the 4th decimal place.
    expect(Booking::derivePaymentStatus(99.9999, 100.0, today()->subDay()))->toBe('paid');
});

// --- Raw form input ----------------------------------------------------------

test('option date given as a form date string is understood', function () {
    expect(Booking::derivePaymentStatus(0, 100, today()->subDay()->toDateString()))->toBe('missed');
    expect(Booking::derivePaymentStatus(0, 100, today()->addDay()->toDateString()))->toBe('unpaid');
});

test('an empty option date string is treated as no deadline', function () {
    expect(Booking::derivePaymentStatus(0, 100, ''))->toBe('unpaid');
});
