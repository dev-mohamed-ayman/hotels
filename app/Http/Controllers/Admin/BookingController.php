<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Currency;
use App\Models\Customer;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Mpdf\Mpdf;

class BookingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view bookings')->only(['index', 'show']);
        $this->middleware('permission:create bookings')->only(['create', 'store']);
        $this->middleware('permission:edit bookings')->only(['edit', 'update', 'updateGuestPayment', 'updateHotelPayment']);
        $this->middleware('permission:delete bookings')->only(['destroy']);
        $this->middleware('permission:export bookings')->only([
            'downloadBankPdf',
            'downloadDetailedPdf',
            'downloadGuestPdf',
            'downloadNetRatePdf',
            'exportBankPdf',
            'exportDetailedPdf',
            'exportGuestPdf',
            'exportNetRatePdf',
        ]);
    }

    /**
     * Calculate payment status based solely on paid_amount vs net_amount.
     * Uses round(,2) to avoid floating-point == comparison bugs.
     *
     * Rules:
     *  paid == 0           → unpaid
     *  0 < paid < net      → partial
     *  paid == net         → paid
     *  paid > net          → overpaid
     */
    private function calcPaymentStatus(float $paidAmount, float $netAmount): string
    {
        $paid = round($paidAmount, 2);
        $net  = round($netAmount, 2);

        if ($paid <= 0) {
            return 'unpaid';
        } elseif ($paid < $net) {
            return 'partial';
        } elseif ($paid === $net) {
            return 'paid';
        } else {
            return 'overpaid';
        }
    }

    public function index(Request $request)
    {
        // Handle filter persistence
        $this->handleFilterPersistence($request);

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
            $query->where('payment_status', $request->payment_status);
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
            $query->where('code', 'like', '%'.$request->search.'%');
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'asc');

        // Validate sort_by column
        $allowedSortColumns = ['code', 'check_in', 'check_out', 'total_amount', 'paid_amount', 'status', 'created_at', 'updated_at'];
        if (! in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at';
        }

        // Validate sort_order
        if (! in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'asc';
        }

        $query->orderBy($sortBy, $sortOrder)->orderBy('code', 'asc');

        // Get total count before pagination for export
        $totalFilteredBookings = (clone $query)->count();

        // Get per page value from request, default to 10
        $perPage = $request->get('per_page', 10);
        // Validate per_page value (allow only specific values for security)
        $allowedPerPage = [10, 25, 50, 100];
        if (! in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        $bookings = $query->paginate($perPage)->withQueryString();

        // Get filter options
        $hotels = Hotel::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $currencies = Currency::where('is_active', true)->orderBy('code')->get();

        return view('admin.pages.bookings.index', compact('bookings', 'hotels', 'customers', 'currencies', 'totalFilteredBookings'));
    }

    /**
     * Handle filter persistence in session
     */
    private function handleFilterPersistence(Request $request)
    {
        // If this is a clear filters request
        if ($request->has('clear_filters')) {
            session()->forget('booking_filters');

            return;
        }

        // Get current filters from request
        $currentFilters = $request->only([
            'hotel_id',
            'customer_id',
            'payment_status',
            'check_in_from',
            'check_in_to',
            'check_out_from',
            'check_out_to',
            'currency_id',
            'search',
            'sort_by',
            'sort_order',
            'per_page',
        ]);

        // Remove empty values
        $currentFilters = array_filter($currentFilters, function ($value) {
            return $value !== null && $value !== '';
        });

        // If we have filters in the request, save them to session
        if (! empty($currentFilters)) {
            session(['booking_filters' => $currentFilters]);
        } // If no filters in request but we have saved filters, apply them
        elseif (session()->has('booking_filters') && empty($currentFilters) && ! $request->has('page')) {
            $savedFilters = session('booking_filters');
            $request->merge($savedFilters);
        }
    }

    public function show(string $id)
    {
        $booking = Booking::with(['customer', 'hotel', 'currency', 'rooms', 'adjustments'])->findOrFail($id);

        return view('admin.pages.bookings.show', compact('booking'));
    }

    public function create()
    {
        $customers = Customer::get();
        $hotels = Hotel::query()->where('is_active', true)->orderBy('name', 'asc')->get();
        $currencies = Currency::all();

        // Load nationalities from API
        $nationalities = [];
        try {
            $response = Http::timeout(5)->get('https://api.plannrcrm.com/api/v1/static/nationalities');
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['data']) && is_array($data['data'])) {
                    $nationalities = collect($data['data'])
                        ->sortBy('nationality')
                        ->values()
                        ->toArray();
                }
            }
        } catch (\Exception $e) {
            // If API fails, use empty array
            $nationalities = [];
        }

        return view('admin.pages.bookings.create', compact('customers', 'hotels', 'currencies', 'nationalities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'customer_id' => 'required|exists:customers,id',
            'client_name' => 'nullable|string|max:255',
            'hotel_id' => 'required|exists:hotels,id',
            'currency_id' => 'required|exists:currencies,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'option_date' => 'nullable|date',
            'payment_date' => 'nullable|date', // Keep for backward compatibility
            'meals_plan' => 'nullable|string|max:255',
            'rooms' => 'required|array|min:1',
            'rooms.*.room_type' => 'required|in:TPL,DBL,SGL,QUD',
            'rooms.*.room_count' => 'required|integer|min:1',
            'rooms.*.price' => 'required|numeric|min:0',
            'rooms.*.margin' => 'required|numeric|min:0',
            'rooms.*.child_count' => 'nullable|integer|min:0',
            'rooms.*.child_price' => 'nullable|numeric|min:0',
            'rooms.*.child_margin' => 'nullable|numeric|min:0',
            'additions' => 'nullable|array',
            'additions.*.net_rate' => 'required|numeric',
            'additions.*.guest_rate' => 'required|numeric',
            'additions.*.margin' => 'nullable|numeric',
            'additions.*.description' => 'required|string',
            'discounts' => 'nullable|array',
            'discounts.*.net_rate' => 'required|numeric',
            'discounts.*.guest_rate' => 'required|numeric',
            'discounts.*.margin' => 'nullable|numeric',
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

            // Calculate net rate (rooms + children + additions net_rate - discounts net_rate)
            $netRate = 0;
            $totalMargin = 0;

            foreach ($request->rooms as $room) {
                $roomCount = $room['room_count'] ?? 1;
                $roomNetRate = $room['price'] * $roomCount * $nights;
                $netRate += $roomNetRate;

                $roomMargin = $room['margin'] * $roomCount * $nights;
                $totalMargin += $roomMargin;

                // Child price and margin are multiplied by nights
                if (isset($room['child_count']) && $room['child_count'] > 0) {
                    $childPrice += ($room['child_count'] ?? 0) * ($room['child_price'] ?? 0) * $nights;
                    $childMargin += ($room['child_count'] ?? 0) * ($room['child_margin'] ?? 0) * $nights;
                }
            }

            // Add child price to net rate (child margin is added to total margin)
            $netRate += $childPrice;
            $totalMargin += $childMargin;

            // Add additions net_rate to net rate - NOT multiplied by nights
            if ($request->has('additions')) {
                foreach ($request->additions as $addition) {
                    $netRate += $addition['net_rate'] ?? 0;
                    $totalMargin += $addition['margin'] ?? 0; // Add addition margin
                }
            }

            // Subtract discounts net_rate from net rate - NOT multiplied by nights
            if ($request->has('discounts')) {
                foreach ($request->discounts as $discount) {
                    $netRate -= $discount['net_rate'] ?? 0;
                    $totalMargin += $discount['margin'] ?? 0; // Add discount margin (positive value)
                }
            }

            // Total amount = net rate + margin
            $totalAmount = $netRate + $totalMargin;

            // Validate paid_amount doesn't exceed total_amount - REMOVED to allow overpayment
            $paidAmount = $request->paid_amount ?? 0;
            // if ($paidAmount > $totalAmount) {
            //    return back()->withErrors(['paid_amount' => __('Paid amount cannot exceed total guest rate')])->withInput();
            // }

            // Recalculate payment status based on net rate only
            $newStatus = $this->calcPaymentStatus((float) $paidAmount, (float) $netRate);

            $booking = Booking::create([
                'code' => $request->code,
                'customer_id' => $request->customer_id,
                'client_name' => $request->client_name,
                'hotel_id' => $request->hotel_id,
                'currency_id' => $request->currency_id,
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'nights' => $nights,
                'option_date' => $request->option_date ?? $request->payment_date,
                'meals_plan' => $request->meals_plan,
                'status' => 'confirmed', // Default to confirmed for now
                'payment_status' => $newStatus,
                'total_amount' => $totalAmount,
                'child_price' => $childPrice,
                'child_margin' => $childMargin,
                'paid_amount' => $paidAmount,

                'net_amount' => $netRate,
                'notes' => $request->notes,
                'hotel_paid_amount' => 0,
            ]);

            // Update customer nationality if provided
            if ($request->filled('customer_nationality')) {
                $customer = Customer::find($request->customer_id);
                if ($customer) {
                    $customer->update(['nationality' => $request->customer_nationality]);
                }
            }

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
                    $netRate = $addition['net_rate'] ?? 0;
                    $guestRate = $addition['guest_rate'] ?? 0;
                    $margin = $guestRate - $netRate;

                    $booking->adjustments()->create([
                        'type' => 'addition',
                        'amount' => $guestRate, // Keep for backward compatibility
                        'net_rate' => $netRate,
                        'guest_rate' => $guestRate,
                        'margin' => $margin,
                        'description' => $addition['description'],
                    ]);
                }
            }

            // Save discounts
            if ($request->has('discounts')) {
                foreach ($request->discounts as $discount) {
                    $netRate = $discount['net_rate'] ?? 0;
                    $guestRate = $discount['guest_rate'] ?? 0;
                    $margin = $netRate - $guestRate;

                    $booking->adjustments()->create([
                        'type' => 'discount',
                        'amount' => $guestRate, // Keep for backward compatibility
                        'net_rate' => $netRate,
                        'guest_rate' => $guestRate,
                        'margin' => $margin,
                        'description' => $discount['description'],
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('bookings.index')->with('success', __('Booking created successfully'));
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', __('Error creating booking').': '.$e->getMessage())->withInput();
        }
    }

    public function edit(Booking $booking)
    {

        $customers = Customer::get();
        $hotels = Hotel::query()->where('is_active', true)->orderBy('name', 'asc')->get();
        $currencies = Currency::all();

        // Load nationalities from API
        $nationalities = [];
        try {
            $response = Http::timeout(5)->get('https://api.plannrcrm.com/api/v1/static/nationalities');
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['data']) && is_array($data['data'])) {
                    $nationalities = collect($data['data'])
                        ->sortBy('nationality')
                        ->values()
                        ->toArray();
                }
            }
        } catch (\Exception $e) {
            // If API fails, use empty array
            $nationalities = [];
        }

        // Load relationships
        $booking->load(['rooms', 'adjustments']);

        return view('admin.pages.bookings.edit', compact('booking', 'customers', 'hotels', 'currencies', 'nationalities'));
    }

    public function update(Request $request, Booking $booking)
    {

        $request->validate([
            'code' => 'required',
            'customer_id' => 'required|exists:customers,id',
            'client_name' => 'nullable|string|max:255',
            'customer_nationality' => 'nullable|string|max:255',
            'hotel_id' => 'required|exists:hotels,id',
            'currency_id' => 'required|exists:currencies,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
            'option_date' => 'nullable|date',
            'payment_date' => 'nullable|date', // Keep for backward compatibility
            'meals_plan' => 'nullable|string|max:255',
            'payment_status' => 'nullable|in:paid,unpaid,partial,revised,overpaid', // ignored, auto-calculated
            'rooms' => 'required|array|min:1',
            'rooms.*.room_type' => 'required|in:TPL,DBL,SGL,QUD',
            'rooms.*.room_count' => 'required|integer|min:1',
            'rooms.*.price' => 'required|numeric|min:0',
            'rooms.*.margin' => 'required|numeric|min:0',
            'rooms.*.child_count' => 'nullable|integer|min:0',
            'rooms.*.child_price' => 'nullable|numeric|min:0',
            'rooms.*.child_margin' => 'nullable|numeric|min:0',
            'additions' => 'nullable|array',
            'additions.*.net_rate' => 'required|numeric',
            'additions.*.guest_rate' => 'required|numeric',
            'additions.*.margin' => 'nullable|numeric',
            'additions.*.description' => 'required|string',
            'discounts' => 'nullable|array',
            'discounts.*.net_rate' => 'required|numeric',
            'discounts.*.guest_rate' => 'required|numeric',
            'discounts.*.margin' => 'nullable|numeric',
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

            // Calculate net rate (rooms + children + additions net_rate - discounts net_rate)
            $netRate = 0;
            $totalMargin = 0;

            foreach ($request->rooms as $room) {
                $roomCount = $room['room_count'] ?? 1;
                $roomNetRate = $room['price'] * $roomCount * $nights;
                $netRate += $roomNetRate;

                $roomMargin = $room['margin'] * $roomCount * $nights;
                $totalMargin += $roomMargin;

                // Child price and margin are multiplied by nights
                if (isset($room['child_count']) && $room['child_count'] > 0) {
                    $childPrice += ($room['child_count'] ?? 0) * ($room['child_price'] ?? 0) * $nights;
                    $childMargin += ($room['child_count'] ?? 0) * ($room['child_margin'] ?? 0) * $nights;
                }
            }

            // Add child price to net rate (child margin is added to total margin)
            $netRate += $childPrice;
            $totalMargin += $childMargin;

            // Add additions net_rate to net rate - NOT multiplied by nights
            if ($request->has('additions')) {
                foreach ($request->additions as $addition) {
                    $netRate += $addition['net_rate'] ?? 0;
                    $totalMargin += $addition['margin'] ?? 0; // Add addition margin
                }
            }

            // Subtract discounts net_rate from net rate - NOT multiplied by nights
            if ($request->has('discounts')) {
                foreach ($request->discounts as $discount) {
                    $netRate -= $discount['net_rate'] ?? 0;
                    $totalMargin += $discount['margin'] ?? 0; // Add discount margin (positive value)
                }
            }

            // Total amount = net rate + margin
            $totalAmount = $netRate + $totalMargin;

            $currentPaid = $booking->paid_amount;
            $newPaidAmount = $request->paid_amount ?? $currentPaid;

            // Note: We no longer auto-refund on booking edit.
            // The paid_amount stays as-is, and payment_status reflects the actual state.
            // If paid > net, status = overpaid. User can manually adjust paid_amount if needed.

            // Recalculate payment status based on net rate only
            $newStatus = $this->calcPaymentStatus((float) $newPaidAmount, (float) $netRate);

            $booking->update([
                'code' => $request->code,
                'customer_id' => $request->customer_id,
                'client_name' => $request->client_name,
                'hotel_id' => $request->hotel_id,
                'currency_id' => $request->currency_id,
                'check_in' => $request->check_in,
                'check_out' => $request->check_out,
                'nights' => $nights,
                'option_date' => $request->option_date ?? $request->payment_date,
                'meals_plan' => $request->meals_plan,
                'payment_status' => $newStatus,
                'total_amount' => $totalAmount,
                'child_price' => $childPrice,
                'child_margin' => $childMargin,
                'paid_amount' => $newPaidAmount,

                'net_amount' => $netRate,
                'notes' => $request->notes,
            ]);

            // Update customer nationality if provided
            if ($request->filled('customer_nationality')) {
                $customer = Customer::find($request->customer_id);
                if ($customer) {
                    $customer->update(['nationality' => $request->customer_nationality]);
                }
            }

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
                    $netRate = $addition['net_rate'] ?? 0;
                    $guestRate = $addition['guest_rate'] ?? 0;
                    $margin = $guestRate - $netRate;

                    $booking->adjustments()->create([
                        'type' => 'addition',
                        'amount' => $guestRate, // Keep for backward compatibility
                        'net_rate' => $netRate,
                        'guest_rate' => $guestRate,
                        'margin' => $margin,
                        'description' => $addition['description'],
                    ]);
                }
            }

            // Save discounts
            if ($request->has('discounts')) {
                foreach ($request->discounts as $discount) {
                    $netRate = $discount['net_rate'] ?? 0;
                    $guestRate = $discount['guest_rate'] ?? 0;
                    $margin = $netRate - $guestRate;

                    $booking->adjustments()->create([
                        'type' => 'discount',
                        'amount' => $guestRate, // Keep for backward compatibility
                        'net_rate' => $netRate,
                        'guest_rate' => $guestRate,
                        'margin' => $margin,
                        'description' => $discount['description'],
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('bookings.index')->with('success', __('Booking updated successfully'));
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', __('Error updating booking').': '.$e->getMessage())->withInput();
        }
    }

    public function updateGuestPayment(Request $request, Booking $booking)
    {
        $request->validate([
            'guest_paid_amount' => ['required', 'numeric', 'min:0'],
        ]);

        $currentPaidAmount = $booking->paid_amount;
        $newPaidAmount     = (float) $request->guest_paid_amount;
        $newStatus         = $this->calcPaymentStatus($newPaidAmount, (float) $booking->net_amount);

        try {
            DB::beginTransaction();

            // Update paid_amount AND payment_status together atomically
            $booking->update([
                'paid_amount'    => $newPaidAmount,
                'payment_status' => $newStatus,
            ]);

            // Record wallet transaction for the difference
            $amountJustPaid = $newPaidAmount - $currentPaidAmount;
            if ($amountJustPaid > 0) {
                $booking->customer->walletTransactions()->create([
                    'amount'      => $amountJustPaid,
                    'type'        => 'credit',
                    'currency_id' => $booking->currency_id,
                    'description' => __('Payment for Booking #:code (Guest Payment)', ['code' => $booking->code]),
                    'reference'   => $booking->code,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', __('Error updating guest payment: ').$e->getMessage());
        }

        return back()->with('success', __('Guest payment updated successfully.'));
    }

    public function updateHotelPayment(Request $request, Booking $booking)
    {
        $request->validate([
            'hotel_paid_amount' => ['required', 'numeric', 'min:0'],
        ]);

        $newHotelPaidAmount = (float) $request->hotel_paid_amount;
        $remainingAmount    = $booking->net_amount - $newHotelPaidAmount;

        // payment_status is based on hotel_paid_amount vs net_amount
        $newStatus = $this->calcPaymentStatus($newHotelPaidAmount, (float) $booking->net_amount);

        try {
            DB::beginTransaction();

            // Update hotel_paid_amount AND payment_status together atomically
            $booking->update([
                'hotel_paid_amount' => $newHotelPaidAmount,
                'payment_status'    => $newStatus,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', __('Error updating hotel payment: ').$e->getMessage());
        }

        return back()->with('success', __('Hotel payment updated successfully. New remaining: :amount', [
            'amount' => number_format($remainingAmount, 0).' '.$booking->currency->symbol,
        ]));
    }

    public function destroy(Booking $booking)
    {
        try {
            $booking->delete();

            return redirect()->route('bookings.index')->with('success', __('Booking deleted successfully'));
        } catch (\Exception $e) {
            return back()->with('error', __('Error deleting booking').': '.$e->getMessage());
        }
    }

    public function downloadBankPdf(Booking $booking)
    {
        $booking->load(['customer', 'hotel', 'currency', 'rooms', 'adjustments']);

        // Calculate totals for bank export (with margins - full price)
        $roomsTotal = 0;
        $roomsData = [];
        foreach ($booking->rooms as $room) {
            $roomPrice = ($room->price + $room->margin) * $booking->nights;
            $roomTotal = $roomPrice * $room->room_count;
            $roomsTotal += $roomTotal;
            $roomsData[] = [
                'room_type' => $room->room_type,
                'category' => $room->category,
                'room_count' => $room->room_count,
                'price' => ($room->price + $room->margin) * $booking->nights,
                'subtotal' => $roomTotal,
            ];
        }

        $childTotal = ($booking->child_price + $booking->child_margin) * $booking->nights;
        $additionsTotal = $booking->adjustments->where('type', 'addition')->sum('guest_rate');
        $discountsTotal = $booking->adjustments->where('type', 'discount')->sum('guest_rate');

        $totalAmount = $roomsTotal + $childTotal + $additionsTotal - $discountsTotal;

        $bankAccount = \App\Models\HotelBankAccount::where('hotel_id', $booking->hotel_id)
            ->where('currency_id', $booking->currency_id)
            ->first();

        $html = view('admin.pages.bookings.pdf.bank', compact(
            'booking',
            'roomsData',
            'roomsTotal',
            'childTotal',
            'additionsTotal',
            'discountsTotal',
            'totalAmount',
            'bankAccount'
        ))->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'default_font' => 'dejavusans',
        ]);

        $mpdf->WriteHTML($html);

        return $mpdf->Output('booking-'.$booking->code.'-bank.pdf', 'D');
    }

    public function downloadDetailedPdf(Booking $booking)
    {
        $booking->load(['customer', 'hotel', 'currency', 'rooms', 'adjustments']);

        // Calculate totals for detailed export (all details visible)
        $roomsTotal = 0;
        $totalMargin = 0;
        $roomsData = [];
        foreach ($booking->rooms as $room) {
            $roomNetRate = $room->price * $booking->nights;
            $roomMargin = $room->margin * $booking->nights;
            $roomPrice = $roomNetRate + $roomMargin;
            $roomTotal = $roomPrice * $room->room_count;
            $roomsTotal += $roomTotal;
            $totalMargin += $roomMargin * $room->room_count;

            $childCount = $room->child_count ?? 0;
            $childPrice = ($room->child_price ?? 0) * $booking->nights;

            $roomsData[] = [
                'room_type' => $room->room_type,
                'category' => $room->category,
                'room_count' => $room->room_count,
                'net_rate' => $roomNetRate,
                'margin' => $roomMargin,
                'price' => $roomPrice,
                'child_count' => $childCount,
                'child_price' => $childPrice,
                'subtotal' => $roomTotal,
            ];
        }

        // Calculate complete totals
        $childNetTotal = ($booking->child_price ?? 0) * $booking->nights;
        $childGuestTotal = (($booking->child_price ?? 0) + ($booking->child_margin ?? 0)) * $booking->nights;

        $additionsNetTotal = $booking->adjustments->where('type', 'addition')->sum('net_rate');
        $additionsGuestTotal = $booking->adjustments->where('type', 'addition')->sum('guest_rate');

        $discountsNetTotal = $booking->adjustments->where('type', 'discount')->sum('net_rate');
        $discountsGuestTotal = $booking->adjustments->where('type', 'discount')->sum('guest_rate');

        // Calculate totalNetRate: Rooms Net + Child Net + Additions Net - Discounts Net
        $totalNetRate = $booking->rooms->sum(function ($room) use ($booking) {
            return ($room->price * $room->room_count * $booking->nights) +
                (($room->child_price ?? 0) * ($room->child_count ?? 0) * $booking->nights);
        }) + $additionsNetTotal - $discountsNetTotal;

        // Calculate totalGuestRate: Rooms Guest + Child Guest + Additions Guest - Discounts Guest
        $totalGuestRate = $booking->rooms->sum(function ($room) use ($booking) {
            return (($room->price + $room->margin) * $room->room_count * $booking->nights) +
                ((($room->child_price ?? 0) + ($room->child_margin ?? 0)) * ($room->child_count ?? 0) * $booking->nights);
        }) + $additionsGuestTotal - $discountsGuestTotal;

        // Use calculated totals
        $totalAmount = $totalGuestRate; // For backward compat variable name if needed, but view should use specific ones

        $html = view('admin.pages.bookings.pdf.detailed', compact(
            'booking',
            'roomsData', // Can keep for robust data structure if view updated, but minimal view uses direct iteration
            'totalNetRate',
            'totalGuestRate',
            'childNetTotal',
            'childGuestTotal',
            'additionsNetTotal',
            'additionsGuestTotal',
            'discountsNetTotal',
            'discountsGuestTotal',
            'totalAmount'
        ))->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'default_font' => 'dejavusans',
        ]);

        $mpdf->WriteHTML($html);

        return $mpdf->Output('booking-'.$booking->code.'-detailed.pdf', 'D');
    }

    public function downloadGuestPdf(Booking $booking)
    {
        $booking->load(['customer', 'hotel', 'currency', 'rooms', 'adjustments']);

        // Calculate totals for guest export (with margins - full price for guest)
        $roomsTotal = 0;
        $roomsData = [];
        foreach ($booking->rooms as $room) {
            $roomPrice = ($room->price + $room->margin) * $booking->nights;
            $roomTotal = $roomPrice * $room->room_count;
            $roomsTotal += $roomTotal;
            $roomsData[] = [
                'room_type' => $room->room_type,
                'category' => $room->category,
                'room_count' => $room->room_count,
                'price' => $roomPrice,
                'subtotal' => $roomTotal,
            ];
        }

        // Calculate guest totals
        $childGuestTotal = (($booking->child_price ?? 0) + ($booking->child_margin ?? 0)) * $booking->nights;

        $additionsGuestTotal = $booking->adjustments->where('type', 'addition')->sum('guest_rate');
        $discountsGuestTotal = $booking->adjustments->where('type', 'discount')->sum('guest_rate');

        // Calculate totalGuestRate: Rooms Guest + Child Guest + Additions Guest - Discounts Guest
        $totalGuestRate = $booking->rooms->sum(function ($room) use ($booking) {
            return (($room->price + $room->margin) * $room->room_count * $booking->nights) +
                ((($room->child_price ?? 0) + ($room->child_margin ?? 0)) * ($room->child_count ?? 0) * $booking->nights);
        }) + $additionsGuestTotal - $discountsGuestTotal;

        $totalAmount = $totalGuestRate;

        $html = view('admin.pages.bookings.pdf.guest', compact(
            'booking',
            'roomsData',
            'totalGuestRate',
            'childGuestTotal',
            'additionsGuestTotal',
            'discountsGuestTotal',
            'totalAmount'
        ))->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'default_font' => 'dejavusans',
        ]);

        $mpdf->WriteHTML($html);

        return $mpdf->Output('booking-'.$booking->code.'-guest.pdf', 'D');
    }

    public function downloadNetRatePdf(Booking $booking)
    {
        $booking->load(['customer', 'hotel', 'currency', 'rooms', 'adjustments']);

        // Calculate totals for net rate export (without margins - base price only)
        $roomsNetTotal = 0;
        $roomsData = [];
        foreach ($booking->rooms as $room) {
            $roomNetRate = $room->price * $booking->nights;
            $roomTotal = $roomNetRate * $room->room_count;
            $roomsNetTotal += $roomTotal;

            $childCount = $room->child_count ?? 0;
            $childNetRate = ($room->child_price ?? 0) * $booking->nights;

            $roomsData[] = [
                'room_type' => $room->room_type,
                'category' => $room->category,
                'room_count' => $room->room_count,
                'net_rate' => $roomNetRate,
                'child_count' => $childCount,
                'child_net_rate' => $childNetRate,
                'net_subtotal' => $roomTotal,
            ];
        }

        // Calculate net totals
        $childNetTotal = ($booking->child_price ?? 0) * $booking->nights;

        $additionsNetTotal = $booking->adjustments->where('type', 'addition')->sum('net_rate');
        $discountsNetTotal = $booking->adjustments->where('type', 'discount')->sum('net_rate');

        // Calculate totalNetRate: Rooms Net + Child Net + Additions Net - Discounts Net
        $totalNetRate = $booking->rooms->sum(function ($room) use ($booking) {
            return ($room->price * $room->room_count * $booking->nights) +
                (($room->child_price ?? 0) * ($room->child_count ?? 0) * $booking->nights);
        }) + $additionsNetTotal - $discountsNetTotal;

        $html = view('admin.pages.bookings.pdf.netrate', compact(
            'booking',
            'roomsData',
            'totalNetRate',
            'childNetTotal',
            'additionsNetTotal',
            'discountsNetTotal'
        ))->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'default_font' => 'dejavusans',
        ]);

        $mpdf->WriteHTML($html);

        return $mpdf->Output('booking-'.$booking->code.'-netrate.pdf', 'D');
    }

    /**
     * Get filtered bookings query (reusable for export)
     */
    private function getFilteredBookingsQuery(Request $request, $notes = false)
    {
        $query = Booking::with(['customer', 'hotel', 'currency', 'rooms', 'adjustments']);

        if ($notes) {
            $query->whereNotNull('notes');
        }

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
            $query->where('payment_status', $request->payment_status);
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
            $query->where('code', 'like', '%'.$request->search.'%');
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        // Validate sort_by column
        $allowedSortColumns = ['code', 'check_in', 'check_out', 'total_amount', 'paid_amount', 'status', 'created_at', 'updated_at'];
        if (! in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at';
        }

        // Validate sort_order
        if (! in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        $query->orderBy($sortBy, $sortOrder)->orderBy('code', $sortOrder);

        return $query;
    }

    public function duplicate(Booking $booking)
    {
        try {
            DB::beginTransaction();

            // Replicate the booking (basic data only)
            $newBooking = $booking->replicate([
                'code',
                'created_at',
                'updated_at',
                'net_amount',
                'total_amount',
                'paid_amount',
                'hotel_paid_amount',
                'status',
            ]);

            // Set new code (append -copy)
            $newBooking->code = $booking->code;

            // Reset amounts
            $newBooking->net_amount = 0;
            $newBooking->total_amount = 0;
            $newBooking->paid_amount = 0;
            $newBooking->hotel_paid_amount = 0;
            $newBooking->payment_status = 'unpaid';

            $newBooking->save();

            // Note: We are NOT copying rooms or adjustments as per "basic data only" request.
            // However, this might result in an empty booking. The user can then edit it.
            // If the user wants to copy rooms but reset prices, we would need to implement that.
            // Given "basic data only", we assume customer, hotel, dates, etc.

            DB::commit();

            return redirect()->route('bookings.index', $newBooking->id)
                ->with('success', __('Booking duplicated successfully. Please add rooms and details.'));

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', __('Error duplicating booking').': '.$e->getMessage());
        }
    }

    public function togglePaymentStatus(Booking $booking)
    {
        // If current status is revised, switch to auto-calculate (which might be paid, partial, or unpaid)
        if ($booking->payment_status === 'revised') {
            // Auto calculate based on net amount only (paid_amount vs net_amount)
            $newStatus = $this->calcPaymentStatus((float) $booking->paid_amount, (float) $booking->net_amount);
            $booking->update(['payment_status' => $newStatus]);
            $message = __('Booking status set to Auto Calculate (:status)', ['status' => ucfirst($newStatus)]);
        } else {
            // Switch to revised
            $booking->update(['payment_status' => 'revised']);
            $message = __('Booking status set to Revised');
        }

        return back()->with('success', $message);
    }

    public function exportBankPdf(Request $request)
    {
        $bookings = $this->getFilteredBookingsQuery($request)->get();

        if ($bookings->isEmpty()) {
            return back()->with('error', __('No bookings found to export'));
        }

        $bookingsData = [];
        $totalAmount = 0;
        $totalBookingsCount = $bookings->count();

        foreach ($bookings as $booking) {
            $netExtras = $booking->adjustments->where('type', 'addition')->sum('net_rate');
            $netReducts = $booking->adjustments->where('type', 'discount')->sum('net_rate');

            // Calculate totals for ALL rooms in the booking (same as export-netrate)
            $bookingTotal = 0;
            foreach ($booking->rooms as $room) {
                $bookingTotal += $room->price * $room->room_count * $booking->nights;
                $bookingTotal += $room->child_price * $room->child_count * $booking->nights;
            }
            $bookingTotal += $netExtras - $netReducts;
            $totalAmount += $bookingTotal;

            $bankAccount = \App\Models\HotelBankAccount::where('hotel_id', $booking->hotel_id)
                ->where('currency_id', $booking->currency_id)
                ->first();

            $bookingsData[] = [
                'booking' => $booking,
                'total' => $bookingTotal,
                'bank_account' => $bankAccount,
            ];
        }
        //        dd($bookingsData[0]);

        $html = view('admin.pages.bookings.pdf.export-bank', compact('bookingsData', 'totalAmount', 'totalBookingsCount'))->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'P',
            'margin_left' => 7,
            'margin_right' => 7,
            'margin_top' => 7,
            'margin_bottom' => 7,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'default_font' => '',
        ]);

        $mpdf->WriteHTML($html);

        return $mpdf->Output('bookings-bank-export.pdf', 'D');
    }

    public function exportDetailedPdf(Request $request)
    {
        $bookings = $this->getFilteredBookingsQuery($request)->get();
        $bookingNotes = $this->getFilteredBookingsQuery($request, true)->get();

        if ($bookings->isEmpty()) {
            return back()->with('error', __('No bookings found to export'));
        }

        $groupedBookings = $bookings->groupBy(function ($booking) {
            return $booking->code.'|'.
                $booking->hotel->name.'|'.
                $booking->meals_plan.'|'.
                $booking->check_in->format('Y-m-d').'|'.
                $booking->check_out->format('Y-m-d').'|'.
                $booking->nights;
        });

        $html = view('admin.pages.bookings.pdf.export-detailed', [
            'bookings' => $bookings,
            'groupedBookings' => $groupedBookings,
            'bookingNotes' => $bookingNotes,
        ])->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'L',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'default_font' => '',
        ]);

        $mpdf->WriteHTML($html);

        return $mpdf->Output('bookings-detailed-export.pdf', 'D');
    }

    public function exportGuestPdf(Request $request)
    {
        $bookings = $this->getFilteredBookingsQuery($request)->get();
        $bookingNotes = $this->getFilteredBookingsQuery($request, true)->get();

        if ($bookings->isEmpty()) {
            return back()->with('error', __('No bookings found to export'));
        }

        $groupedBookings = $bookings->groupBy(function ($booking) {
            return $booking->code.'|'.
                $booking->hotel->name.'|'.
                $booking->meals_plan.'|'.
                $booking->check_in->format('Y-m-d').'|'.
                $booking->check_out->format('Y-m-d').'|'.
                $booking->nights;
        });

        $html = view('admin.pages.bookings.pdf.export-guest', [
            'bookings' => $bookings,
            'groupedBookings' => $groupedBookings,
            'bookingNotes' => $bookingNotes,
        ])->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'L',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'default_font' => '',
        ]);

        $mpdf->WriteHTML($html);

        return $mpdf->Output('bookings-guest-export.pdf', 'D');
    }

    public function exportNetRatePdf(Request $request)
    {
        $bookings = $this->getFilteredBookingsQuery($request)->get();
        $bookingNotes = $this->getFilteredBookingsQuery($request, true)->get();

        if ($bookings->isEmpty()) {
            return back()->with('error', __('No bookings found to export'));
        }

        $groupedBookings = $bookings->groupBy(function ($booking) {
            return $booking->code.'|'.
                $booking->hotel->name.'|'.
                $booking->meals_plan.'|'.
                $booking->check_in->format('Y-m-d').'|'.
                $booking->check_out->format('Y-m-d').'|'.
                $booking->nights;
        });

        $html = view('admin.pages.bookings.pdf.export-netrate', [
            'bookings' => $bookings,
            'groupedBookings' => $groupedBookings,
            'bookingNotes' => $bookingNotes,
        ])->render();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'orientation' => 'L',
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'autoScriptToLang' => true,
            'autoLangToFont' => true,
            'default_font' => '',
        ]);

        $mpdf->WriteHTML($html);

        return $mpdf->Output('bookings-netrate-export.pdf', 'D');
    }
}
