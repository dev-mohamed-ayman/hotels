<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Hotel;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['customer', 'hotel', 'currency'])->latest()->paginate(10);
        return view('admin.pages.bookings.index', compact('bookings'));
    }

    public function create()
    {
        $customers = Customer::get();
        $hotels = Hotel::where('is_active', true)->get();
        $currencies = Currency::all();
        return view('admin.pages.bookings.create', compact('customers', 'hotels', 'currencies'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:bookings,code',
            'customer_id' => 'required|exists:customers,id',
            'hotel_id' => 'required|exists:hotels,id',
            'currency_id' => 'required|exists:currencies,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'payment_date' => 'nullable|date',
            'rooms' => 'required|array|min:1',
            'rooms.*.room_type' => 'required|in:TPL,DBL,SGL,QUD',
            'rooms.*.room_count' => 'required|integer|min:1',
            'rooms.*.price' => 'required|numeric|min:0',
            'rooms.*.margin' => 'required|numeric|min:0',
            'rooms.*.child_count' => 'nullable|integer|min:0',
            'rooms.*.child_margin' => 'nullable|numeric|min:0',
            'additions' => 'nullable|array',
            'additions.*.amount' => 'required|numeric|min:0',
            'additions.*.description' => 'required|string',
            'discounts' => 'nullable|array',
            'discounts.*.amount' => 'required|numeric|min:0',
            'discounts.*.description' => 'required|string',
            'paid_amount' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Calculate nights
            $checkIn = \Carbon\Carbon::parse($request->check_in);
            $checkOut = \Carbon\Carbon::parse($request->check_out);
            $nights = $checkIn->diffInDays($checkOut);

            // Calculate totals
            $totalAmount = 0;
            foreach ($request->rooms as $room) {
                $roomCount = $room['room_count'] ?? 1;
                $roomTotal = ($room['price'] + $room['margin']) * $roomCount;
                $childTotal = ($room['child_count'] ?? 0) * ($room['child_margin'] ?? 0) * $roomCount;
                $totalAmount += $roomTotal + $childTotal;
            }

            // Add additions
            if ($request->has('additions')) {
                foreach ($request->additions as $addition) {
                    $totalAmount += $addition['amount'];
                }
            }

            // Subtract discounts
            if ($request->has('discounts')) {
                foreach ($request->discounts as $discount) {
                    $totalAmount -= $discount['amount'];
                }
            }

            $booking = Booking::create([
                'code' => $request->code,
                'customer_id' => $request->customer_id,
                'hotel_id' => $request->hotel_id,
                'currency_id' => $request->currency_id,
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'nights' => $nights,
                'payment_date' => $request->payment_date,
                'status' => 'confirmed', // Default to confirmed for now
                'total_amount' => $totalAmount,
                'paid_amount' => $request->paid_amount ?? 0,
                'notes' => $request->notes,
            ]);

            foreach ($request->rooms as $room) {
                $booking->rooms()->create([
                    'room_type' => $room['room_type'],
                    'room_count' => $room['room_count'] ?? 1,
                    'category' => $room['category'] ?? null,
                    'price' => $room['price'],
                    'margin' => $room['margin'],
                    'child_count' => $room['child_count'] ?? 0,
                    'child_margin' => $room['child_margin'] ?? 0,
                ]);
            }

            // Save additions
            if ($request->has('additions')) {
                foreach ($request->additions as $addition) {
                    $booking->adjustments()->create([
                        'type' => 'addition',
                        'amount' => $addition['amount'],
                        'description' => $addition['description'],
                    ]);
                }
            }

            // Save discounts
            if ($request->has('discounts')) {
                foreach ($request->discounts as $discount) {
                    $booking->adjustments()->create([
                        'type' => 'discount',
                        'amount' => $discount['amount'],
                        'description' => $discount['description'],
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('bookings.index')->with('success', 'Booking created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error creating booking: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Booking $booking)
    {
        // Check if check_in date has not passed yet
        if ($booking->check_in < now()) {
            return redirect()->route('bookings.index')->with('error', 'Cannot edit booking. Check-in date has already passed.');
        }

        $customers = Customer::get();
        $hotels = Hotel::where('is_active', true)->get();
        $currencies = Currency::all();

        // Load relationships
        $booking->load(['rooms', 'adjustments']);

        return view('admin.pages.bookings.edit', compact('booking', 'customers', 'hotels', 'currencies'));
    }

    public function update(Request $request, Booking $booking)
    {
        // Check if check_in date has not passed yet
        if ($booking->check_in < now()) {
            return redirect()->route('bookings.index')->with('error', 'Cannot update booking. Check-in date has already passed.');
        }

        $request->validate([
            'code' => 'required|unique:bookings,code,' . $booking->id,
            'customer_id' => 'required|exists:customers,id',
            'hotel_id' => 'required|exists:hotels,id',
            'currency_id' => 'required|exists:currencies,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'payment_date' => 'nullable|date',
            'rooms' => 'required|array|min:1',
            'rooms.*.room_type' => 'required|in:TPL,DBL,SGL,QUD',
            'rooms.*.room_count' => 'required|integer|min:1',
            'rooms.*.price' => 'required|numeric|min:0',
            'rooms.*.margin' => 'required|numeric|min:0',
            'rooms.*.child_count' => 'nullable|integer|min:0',
            'rooms.*.child_margin' => 'nullable|numeric|min:0',
            'additions' => 'nullable|array',
            'additions.*.amount' => 'required|numeric|min:0',
            'additions.*.description' => 'required|string',
            'discounts' => 'nullable|array',
            'discounts.*.amount' => 'required|numeric|min:0',
            'discounts.*.description' => 'required|string',
            'paid_amount' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Calculate nights
            $checkIn = \Carbon\Carbon::parse($request->check_in);
            $checkOut = \Carbon\Carbon::parse($request->check_out);
            $nights = $checkIn->diffInDays($checkOut);

            // Calculate totals
            $totalAmount = 0;
            foreach ($request->rooms as $room) {
                $roomCount = $room['room_count'] ?? 1;
                $roomTotal = ($room['price'] + $room['margin']) * $roomCount;
                $childTotal = ($room['child_count'] ?? 0) * ($room['child_margin'] ?? 0) * $roomCount;
                $totalAmount += $roomTotal + $childTotal;
            }

            // Add additions
            if ($request->has('additions')) {
                foreach ($request->additions as $addition) {
                    $totalAmount += $addition['amount'];
                }
            }

            // Subtract discounts
            if ($request->has('discounts')) {
                foreach ($request->discounts as $discount) {
                    $totalAmount -= $discount['amount'];
                }
            }

            $booking->update([
                'code' => $request->code,
                'customer_id' => $request->customer_id,
                'hotel_id' => $request->hotel_id,
                'currency_id' => $request->currency_id,
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'nights' => $nights,
                'payment_date' => $request->payment_date,
                'total_amount' => $totalAmount,
                'paid_amount' => $request->paid_amount ?? $booking->paid_amount,
                'notes' => $request->notes,
            ]);

            // Delete old rooms and adjustments
            $booking->rooms()->delete();
            $booking->adjustments()->delete();

            // Add new rooms
            foreach ($request->rooms as $room) {
                $booking->rooms()->create([
                    'room_type' => $room['room_type'],
                    'room_count' => $room['room_count'] ?? 1,
                    'category' => $room['category'] ?? null,
                    'price' => $room['price'],
                    'margin' => $room['margin'],
                    'child_count' => $room['child_count'] ?? 0,
                    'child_margin' => $room['child_margin'] ?? 0,
                ]);
            }

            // Save additions
            if ($request->has('additions')) {
                foreach ($request->additions as $addition) {
                    $booking->adjustments()->create([
                        'type' => 'addition',
                        'amount' => $addition['amount'],
                        'description' => $addition['description'],
                    ]);
                }
            }

            // Save discounts
            if ($request->has('discounts')) {
                foreach ($request->discounts as $discount) {
                    $booking->adjustments()->create([
                        'type' => 'discount',
                        'amount' => $discount['amount'],
                        'description' => $discount['description'],
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('bookings.index')->with('success', 'Booking updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating booking: ' . $e->getMessage())->withInput();
        }
    }


    public function updatePayment(Request $request, Booking $booking)
    {
        $remainingAmount = $booking->total_amount - $booking->paid_amount;

        $request->validate([
            'payment_amount' => [
                'required',
                'numeric',
                'min:0.01',
                'max:' . $remainingAmount,
            ],
        ], [
            'payment_amount.max' => __('Payment amount cannot exceed remaining amount of :amount', [
                'amount' => number_format($remainingAmount, 2) . ' ' . $booking->currency->code
            ]),
        ]);

        $booking->update([
            'paid_amount' => $booking->paid_amount + $request->payment_amount
        ]);

        return back()->with('success', __('Payment updated successfully.'));
    }

    public function destroy(Booking $booking)
    {
        // Check if check_in date has not passed yet
        if ($booking->check_in < now()) {
            return back()->with('error', 'Cannot delete booking. Check-in date has already passed.');
        }

        try {
            $booking->delete();
            return redirect()->route('bookings.index')->with('success', 'Booking deleted successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error deleting booking: ' . $e->getMessage());
        }
    }
}
