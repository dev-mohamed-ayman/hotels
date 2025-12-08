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
    public function index(Request $request)
    {
        $query = Booking::with(['customer', 'hotel', 'currency', 'rooms']);

        // Filter by hotel
        if ($request->filled('hotel_id')) {
            $query->where('hotel_id', $request->hotel_id);
        }

        // Filter by customer
        if ($request->filled('customer_id')) {
            $query->where('customer_id', $request->customer_id);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            switch ($request->payment_status) {
                case 'paid':
                    $query->whereRaw('paid_amount >= total_amount');
                    break;
                case 'unpaid':
                    $query->where('paid_amount', 0);
                    break;
                case 'partial':
                    $query->whereRaw('paid_amount > 0 AND paid_amount < total_amount');
                    break;
            }
        }

        // Filter by check-in date range
        if ($request->filled('check_in_from')) {
            $query->whereDate('check_in', '>=', $request->check_in_from);
        }
        if ($request->filled('check_in_to')) {
            $query->whereDate('check_in', '<=', $request->check_in_to);
        }

        // Filter by check-out date range
        if ($request->filled('check_out_from')) {
            $query->whereDate('check_out', '>=', $request->check_out_from);
        }
        if ($request->filled('check_out_to')) {
            $query->whereDate('check_out', '<=', $request->check_out_to);
        }

        // Filter by currency
        if ($request->filled('currency_id')) {
            $query->where('currency_id', $request->currency_id);
        }

        // Search by booking code
        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        // Validate sort_by column
        $allowedSortColumns = ['code', 'check_in', 'check_out', 'total_amount', 'paid_amount', 'status', 'created_at', 'updated_at'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at';
        }

        // Validate sort_order
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        $query->orderBy($sortBy, $sortOrder);

        $bookings = $query->paginate(10)->withQueryString();

        // Get filter options
        $hotels = Hotel::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $currencies = Currency::where('is_active', true)->orderBy('code')->get();

        return view('admin.pages.bookings.index', compact('bookings', 'hotels', 'customers', 'currencies'));
    }

    public function show(string $id)
    {
        $booking = Booking::with(['customer', 'hotel', 'currency', 'rooms', 'adjustments'])->findOrFail($id);
        return view('admin.pages.bookings.show', compact('booking'));
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
            'code' => 'required',
            'customer_id' => 'required|exists:customers,id',
            'hotel_id' => 'required|exists:hotels,id',
            'currency_id' => 'required|exists:currencies,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'option_date' => 'nullable|date',
            'payment_date' => 'nullable|date', // Keep for backward compatibility
            'rooms' => 'required|array|min:1',
            'rooms.*.room_type' => 'required|in:TPL,DBL,SGL,QUD',
            'rooms.*.room_count' => 'required|integer|min:1',
            'rooms.*.price' => 'required|numeric|min:0',
            'rooms.*.margin' => 'required|numeric|min:0',
            'rooms.*.child_count' => 'nullable|integer|min:0',
            'rooms.*.child_price' => 'nullable|numeric|min:0',
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
            $childPrice = 0;
            $childMargin = 0;

            foreach ($request->rooms as $room) {
                $roomCount = $room['room_count'] ?? 1;
                $roomTotal = ($room['price'] + $room['margin']) * $roomCount;
                $totalAmount += $roomTotal;

                // Child price and margin are added once per booking, not multiplied by room count
                if (isset($room['child_count']) && $room['child_count'] > 0) {
                    $childPrice += ($room['child_count'] ?? 0) * ($room['child_price'] ?? 0);
                    $childMargin += ($room['child_count'] ?? 0) * ($room['child_margin'] ?? 0);
                }
            }

            // Add child totals to total amount
            $totalAmount += $childPrice + $childMargin;

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
                'option_date' => $request->option_date ?? $request->payment_date,
                'status' => 'confirmed', // Default to confirmed for now
                'total_amount' => $totalAmount,
                'child_price' => $childPrice,
                'child_margin' => $childMargin,
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
                    'child_price' => $room['child_price'] ?? 0,
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
            'code' => 'required',
            'customer_id' => 'required|exists:customers,id',
            'hotel_id' => 'required|exists:hotels,id',
            'currency_id' => 'required|exists:currencies,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'option_date' => 'nullable|date',
            'payment_date' => 'nullable|date', // Keep for backward compatibility
            'rooms' => 'required|array|min:1',
            'rooms.*.room_type' => 'required|in:TPL,DBL,SGL,QUD',
            'rooms.*.room_count' => 'required|integer|min:1',
            'rooms.*.price' => 'required|numeric|min:0',
            'rooms.*.margin' => 'required|numeric|min:0',
            'rooms.*.child_count' => 'nullable|integer|min:0',
            'rooms.*.child_price' => 'nullable|numeric|min:0',
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
            $childPrice = 0;
            $childMargin = 0;

            foreach ($request->rooms as $room) {
                $roomCount = $room['room_count'] ?? 1;
                $roomTotal = ($room['price'] + $room['margin']) * $roomCount;
                $totalAmount += $roomTotal;

                // Child price and margin are added once per booking, not multiplied by room count
                if (isset($room['child_count']) && $room['child_count'] > 0) {
                    $childPrice += ($room['child_count'] ?? 0) * ($room['child_price'] ?? 0);
                    $childMargin += ($room['child_count'] ?? 0) * ($room['child_margin'] ?? 0);
                }
            }

            // Add child totals to total amount
            $totalAmount += $childPrice + $childMargin;

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
                'option_date' => $request->option_date ?? $request->payment_date,
                'total_amount' => $totalAmount,
                'child_price' => $childPrice,
                'child_margin' => $childMargin,
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
                    'child_price' => $room['child_price'] ?? 0,
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
