<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Hotel;
use App\Models\Currency;
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
        $this->middleware('permission:edit bookings')->only(['edit', 'update', 'updatePayment']);
        $this->middleware('permission:delete bookings')->only(['destroy']);
        $this->middleware('permission:export bookings')->only([
            'downloadBankPdf',
            'downloadDetailedPdf',
            'downloadGuestPdf',
            'downloadNetRatePdf',
            'exportBankPdf',
            'exportDetailedPdf',
            'exportGuestPdf',
            'exportNetRatePdf'
        ]);
    }

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

        // Get total count before pagination for export
        $totalFilteredBookings = (clone $query)->count();

        // Get per page value from request, default to 10
        $perPage = $request->get('per_page', 10);
        // Validate per_page value (allow only specific values for security)
        $allowedPerPage = [10, 25, 50, 100];
        if (!in_array($perPage, $allowedPerPage)) {
            $perPage = 10;
        }

        $bookings = $query->paginate($perPage)->withQueryString();

        // Get filter options
        $hotels = Hotel::orderBy('name')->get();
        $customers = Customer::orderBy('name')->get();
        $currencies = Currency::where('is_active', true)->orderBy('code')->get();

        return view('admin.pages.bookings.index', compact('bookings', 'hotels', 'customers', 'currencies', 'totalFilteredBookings'));
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
                    $totalMargin -= $discount['margin'] ?? 0; // Subtract discount margin
                }
            }

            // Total amount = net rate + margin
            $totalAmount = $netRate + $totalMargin;

            // Validate paid_amount doesn't exceed total_amount
            $paidAmount = $request->paid_amount ?? 0;
            if ($paidAmount > $totalAmount) {
                return back()->withErrors(['paid_amount' => __('Paid amount cannot exceed total guest rate')])->withInput();
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
                'meals_plan' => $request->meals_plan,
                'status' => 'confirmed', // Default to confirmed for now
                'total_amount' => $totalAmount,
                'child_price' => $childPrice,
                'child_margin' => $childMargin,
                'paid_amount' => $request->paid_amount ?? 0,
                'notes' => $request->notes,
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
                    $margin = $guestRate - $netRate;

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
            return back()->with('error', __('Error creating booking') . ': ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Booking $booking)
    {
        // Check if check_in date has not passed yet
        if ($booking->check_in < now()) {
            return redirect()->route('bookings.index')->with('error', __('Cannot edit booking. Check-in date has already passed.'));
        }

        $customers = Customer::get();
        $hotels = Hotel::where('is_active', true)->get();
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
        // Check if check_in date has not passed yet
        if ($booking->check_in < now()) {
            return redirect()->route('bookings.index')->with('error', __('Cannot update booking. Check-in date has already passed.'));
        }

        $request->validate([
            'code' => 'required',
            'customer_id' => 'required|exists:customers,id',
            'customer_nationality' => 'nullable|string|max:255',
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
                    $totalMargin -= $discount['margin'] ?? 0; // Subtract discount margin
                }
            }

            // Total amount = net rate + margin
            $totalAmount = $netRate + $totalMargin;

            // Validate paid_amount doesn't exceed total_amount
            $paidAmount = $request->paid_amount ?? $booking->paid_amount;
            if ($paidAmount > $totalAmount) {
                return back()->withErrors(['paid_amount' => __('Paid amount cannot exceed total guest rate')])->withInput();
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
                'meals_plan' => $request->meals_plan,
                'total_amount' => $totalAmount,
                'child_price' => $childPrice,
                'child_margin' => $childMargin,
                'paid_amount' => $request->paid_amount ?? $booking->paid_amount,
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
                    $margin = $guestRate - $netRate;

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
            return back()->with('error', __('Error updating booking') . ': ' . $e->getMessage())->withInput();
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
                'amount' => number_format($remainingAmount, 2) . ' ' . $booking->currency->symbol
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
            return back()->with('error', __('Cannot delete booking. Check-in date has already passed.'));
        }

        try {
            $booking->delete();
            return redirect()->route('bookings.index')->with('success', __('Booking deleted successfully'));
        } catch (\Exception $e) {
            return back()->with('error', __('Error deleting booking') . ': ' . $e->getMessage());
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
        return $mpdf->Output('booking-' . $booking->code . '-bank.pdf', 'D');
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

        // Net Rates: Rooms Net + Child Net + Additions Net - Discounts Net
        $totalNetRate = $booking->rooms->sum(function ($room) use ($booking) {
            return ($room->price * $room->room_count * $booking->nights) +
                (($room->child_price ?? 0) * ($room->child_count ?? 0) * $booking->nights);
        }) + $additionsNetTotal - $discountsNetTotal;

        // Guest Rates: Rooms Guest + Child Guest + Additions Guest - Discounts Guest
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
        return $mpdf->Output('booking-' . $booking->code . '-detailed.pdf', 'D');
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

        // Guest Rates
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
        return $mpdf->Output('booking-' . $booking->code . '-guest.pdf', 'D');
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

        // Net Rates
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
        return $mpdf->Output('booking-' . $booking->code . '-netrate.pdf', 'D');
    }

    /**
     * Get filtered bookings query (reusable for export)
     */
    private function getFilteredBookingsQuery(Request $request)
    {
        $query = Booking::with(['customer', 'hotel', 'currency', 'rooms', 'adjustments']);

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

        return $query;
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
            $roomsTotal = 0;
            foreach ($booking->rooms as $room) {
                $roomPrice = ($room->price + $room->margin) * $booking->nights;
                $roomTotal = $roomPrice * $room->room_count;
                $roomsTotal += $roomTotal;
            }

            $childTotal = ($booking->child_price + $booking->child_margin) * $booking->nights;
            $additionsTotal = $booking->adjustments->where('type', 'addition')->sum('guest_rate');
            $discountsTotal = $booking->adjustments->where('type', 'discount')->sum('guest_rate');

            $bookingTotal = $roomsTotal + $childTotal + $additionsTotal - $discountsTotal;
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

        $html = view('admin.pages.bookings.pdf.export-bank', compact('bookingsData', 'totalAmount', 'totalBookingsCount'))->render();

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
        return $mpdf->Output('bookings-bank-export.pdf', 'D');
    }

    public function exportDetailedPdf(Request $request)
    {
        $bookings = $this->getFilteredBookingsQuery($request)->get();

        if ($bookings->isEmpty()) {
            return back()->with('error', __('No bookings found to export'));
        }

        $bookingsData = [];
        $totalBookingsCount = $bookings->count();

        foreach ($bookings as $booking) {
            $roomsData = [];
            foreach ($booking->rooms as $room) {
                $roomNetRate = $room->price * $room->room_count * $booking->nights;
                $roomMargin = $room->margin * $room->room_count * $booking->nights;
                $roomGuestRate = ($room->price + $room->margin) * $room->room_count * $booking->nights;

                $childCount = $room->child_count ?? 0;
                $childNetRate = ($room->child_price ?? 0) * $childCount * $booking->nights;
                $childMargin = ($room->child_margin ?? 0) * $childCount * $booking->nights;
                $childGuestRate = (($room->child_price ?? 0) + ($room->child_margin ?? 0)) * $childCount * $booking->nights;

                $roomsData[] = [
                    'room_type' => $room->room_type,
                    'category' => $room->category,
                    'room_count' => $room->room_count,
                    'net_rate' => $roomNetRate,
                    'margin' => $roomMargin,
                    'guest_rate' => $roomGuestRate,
                    'child_count' => $childCount,
                    'child_net_rate' => $childNetRate,
                    'child_margin' => $childMargin,
                    'child_guest_rate' => $childGuestRate,
                ];
            }

            $childNetTotal = ($booking->child_price ?? 0) * $booking->nights;
            $childGuestTotal = (($booking->child_price ?? 0) + ($booking->child_margin ?? 0)) * $booking->nights;

            $additionsNetTotal = $booking->adjustments->where('type', 'addition')->sum('net_rate');
            $additionsGuestTotal = $booking->adjustments->where('type', 'addition')->sum('guest_rate');

            $discountsNetTotal = $booking->adjustments->where('type', 'discount')->sum('net_rate');
            $discountsGuestTotal = $booking->adjustments->where('type', 'discount')->sum('guest_rate');

            $totalNetRate = $booking->rooms->sum(function ($room) use ($booking) {
                return ($room->price * $room->room_count * $booking->nights) +
                    (($room->child_price ?? 0) * ($room->child_count ?? 0) * $booking->nights);
            }) + $additionsNetTotal - $discountsNetTotal;

            $totalGuestRate = $booking->rooms->sum(function ($room) use ($booking) {
                return (($room->price + $room->margin) * $room->room_count * $booking->nights) +
                    ((($room->child_price ?? 0) + ($room->child_margin ?? 0)) * ($room->child_count ?? 0) * $booking->nights);
            }) + $additionsGuestTotal - $discountsGuestTotal;

            $bookingsData[] = [
                'booking' => $booking,
                'roomsData' => $roomsData,
                'childNetTotal' => $childNetTotal,
                'childGuestTotal' => $childGuestTotal,
                'additionsNetTotal' => $additionsNetTotal,
                'additionsGuestTotal' => $additionsGuestTotal,
                'discountsNetTotal' => $discountsNetTotal,
                'discountsGuestTotal' => $discountsGuestTotal,
                'totalNetRate' => $totalNetRate,
                'totalGuestRate' => $totalGuestRate,
            ];
        }

        $html = view('admin.pages.bookings.pdf.export-detailed', compact('bookingsData', 'totalBookingsCount'))->render();

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
            'default_font' => 'dejavusans',
        ]);

        $mpdf->WriteHTML($html);
        return $mpdf->Output('bookings-detailed-export.pdf', 'D');
    }

    public function exportGuestPdf(Request $request)
    {
        $bookings = $this->getFilteredBookingsQuery($request)->get();

        if ($bookings->isEmpty()) {
            return back()->with('error', __('No bookings found to export'));
        }

        $bookingsData = [];
        $totalBookingsCount = $bookings->count();

        foreach ($bookings as $booking) {
            $roomsData = [];
            foreach ($booking->rooms as $room) {
                $roomGuestRate = ($room->price + $room->margin) * $room->room_count * $booking->nights;
                $childCount = $room->child_count ?? 0;
                $childGuestRate = (($room->child_price ?? 0) + ($room->child_margin ?? 0)) * $childCount * $booking->nights;

                $roomsData[] = [
                    'room_type' => $room->room_type,
                    'category' => $room->category,
                    'room_count' => $room->room_count,
                    'guest_rate' => $roomGuestRate,
                    'child_count' => $childCount,
                    'child_guest_rate' => $childGuestRate,
                ];
            }

            $childGuestTotal = (($booking->child_price ?? 0) + ($booking->child_margin ?? 0)) * $booking->nights;
            $additionsGuestTotal = $booking->adjustments->where('type', 'addition')->sum('guest_rate');
            $discountsGuestTotal = $booking->adjustments->where('type', 'discount')->sum('guest_rate');

            $totalGuestRate = $booking->rooms->sum(function ($room) use ($booking) {
                return (($room->price + $room->margin) * $room->room_count * $booking->nights) +
                    ((($room->child_price ?? 0) + ($room->child_margin ?? 0)) * ($room->child_count ?? 0) * $booking->nights);
            }) + $additionsGuestTotal - $discountsGuestTotal;

            $bookingsData[] = [
                'booking' => $booking,
                'roomsData' => $roomsData,
                'childGuestTotal' => $childGuestTotal,
                'additionsGuestTotal' => $additionsGuestTotal,
                'discountsGuestTotal' => $discountsGuestTotal,
                'totalGuestRate' => $totalGuestRate,
            ];
        }

        $html = view('admin.pages.bookings.pdf.export-guest', compact('bookingsData', 'totalBookingsCount'))->render();

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
            'default_font' => 'dejavusans',
        ]);

        $mpdf->WriteHTML($html);
        return $mpdf->Output('bookings-guest-export.pdf', 'D');
    }

    public function exportNetRatePdf(Request $request)
    {
        $bookings = $this->getFilteredBookingsQuery($request)->get();

        if ($bookings->isEmpty()) {
            return back()->with('error', __('No bookings found to export'));
        }

        $bookingsData = [];
        $totalBookingsCount = $bookings->count();

        foreach ($bookings as $booking) {
            $roomsData = [];
            foreach ($booking->rooms as $room) {
                $roomNetRate = $room->price * $room->room_count * $booking->nights;
                $childCount = $room->child_count ?? 0;
                $childNetRate = ($room->child_price ?? 0) * $childCount * $booking->nights;

                $roomsData[] = [
                    'room_type' => $room->room_type,
                    'category' => $room->category,
                    'room_count' => $room->room_count,
                    'net_rate' => $roomNetRate,
                    'child_count' => $childCount,
                    'child_net_rate' => $childNetRate,
                ];
            }

            $childNetTotal = ($booking->child_price ?? 0) * $booking->nights;
            $additionsNetTotal = $booking->adjustments->where('type', 'addition')->sum('net_rate');
            $discountsNetTotal = $booking->adjustments->where('type', 'discount')->sum('net_rate');

            $totalNetRate = $booking->rooms->sum(function ($room) use ($booking) {
                return ($room->price * $room->room_count * $booking->nights) +
                    (($room->child_price ?? 0) * ($room->child_count ?? 0) * $booking->nights);
            }) + $additionsNetTotal - $discountsNetTotal;

            $bookingsData[] = [
                'booking' => $booking,
                'roomsData' => $roomsData,
                'childNetTotal' => $childNetTotal,
                'additionsNetTotal' => $additionsNetTotal,
                'discountsNetTotal' => $discountsNetTotal,
                'totalNetRate' => $totalNetRate,
            ];
        }

        $html = view('admin.pages.bookings.pdf.export-netrate', compact('bookingsData', 'totalBookingsCount'))->render();

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
            'default_font' => 'dejavusans',
        ]);

        $mpdf->WriteHTML($html);
        return $mpdf->Output('bookings-netrate-export.pdf', 'D');
    }
}
