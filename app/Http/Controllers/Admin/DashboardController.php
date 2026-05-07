<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\Customer;
use App\Models\Hotel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view dashboard')->only(['index']);
    }

    public function index()
    {
        $user = Auth::user();

        // Bookings Statistics (only if user can view bookings)
        $totalBookings = $user->can('view bookings') ? Booking::count() : 0;
        $confirmedBookings = $user->can('view bookings') ? Booking::where('status', 'confirmed')->count() : 0;
        $pendingBookings = $user->can('view bookings') ? Booking::where('status', 'pending')->count() : 0;
        $cancelledBookings = $user->can('view bookings') ? Booking::where('status', 'cancelled')->count() : 0;

        // Financial Statistics (only if user can view bookings)
        $totalAmount = $user->can('view bookings') ? Booking::sum('net_amount') : 0;
        $paidAmount = $user->can('view bookings') ? Booking::sum('paid_amount') : 0;

        // Recent Bookings (only if user can view bookings)
        // Show bookings starting within 2 days and exclude fully paid ones
        $recentBookings = $user->can('view bookings')
            ? Booking::with(['customer', 'hotel', 'currency', 'rooms'])
                ->whereBetween('check_in', [now()->startOfDay(), now()->addDays(2)->endOfDay()])
                ->where(function ($query) {
                    $query->where('hotel_paid_amount', 0)
                        ->orWhereRaw('hotel_paid_amount < net_amount');
                })
                ->orderBy('check_in', 'asc')
                ->take(10)
                ->get()
            : collect();

        // All bookings starting within 2 days (only if user can view bookings)
        $upcomingBookings = $user->can('view bookings')
            ? Booking::with(['customer', 'hotel', 'currency', 'rooms'])
                ->whereBetween('check_in', [now()->startOfDay(), now()->addDays(2)->endOfDay()])
                ->get()
            : collect();

        // Upcoming Option Dates (Next 7 days)
        $upcomingOptionDates = $user->can('view bookings')
            ? Booking::with(['customer', 'hotel', 'currency', 'rooms'])
                ->whereBetween('option_date', [now()->startOfDay(), now()->addDays(7)->endOfDay()])
                ->where(function ($query) {
                    $query->where('hotel_paid_amount', 0)
                        ->orWhereRaw('hotel_paid_amount < net_amount');
                })
                ->orderBy('option_date', 'asc')
                ->get()
            : collect();

        // Payment Status Statistics (only if user can view bookings)
        $paidBookings = $user->can('view bookings') ? Booking::whereRaw('paid_amount = net_amount')->count() : 0;
        $unpaidBookings = $user->can('view bookings') ? Booking::where('paid_amount', 0)->count() : 0;
        $partialBookings = $user->can('view bookings') ? Booking::whereRaw('paid_amount > 0 AND paid_amount < net_amount')->count() : 0;
        $revisedBookings = $user->can('view bookings') ? Booking::where('payment_status', 'revised')->count() : 0;
        $overpaidBookings = $user->can('view bookings') ? Booking::whereRaw('paid_amount > net_amount')->count() : 0;

        // Hotels Sales Statistics (only if user can view hotels and bookings)
        $hotelsSales = ($user->can('view hotels') && $user->can('view bookings'))
            ? Hotel::get()
                ->map(function ($hotel) {
                    $bookings = Booking::where('hotel_id', $hotel->id)->get();
                    $roomsCount = BookingRoom::whereHas('booking', function ($q) use ($hotel) {
                        $q->where('hotel_id', $hotel->id);
                    })->sum('room_count');

                    return [
                        'id' => $hotel->id,
                        'name' => $hotel->name,
                        'total_sales' => $bookings->sum('net_amount'),
                        'paid_sales' => $bookings->sum('paid_amount'),
                        'bookings_count' => $bookings->count(),
                        'rooms_count' => $roomsCount,
                    ];
                })
                ->sortByDesc('total_sales')
                ->take(10)
            : collect();

        // Most Used Booking Codes (only if user can view bookings)
        $mostUsedCodes = $user->can('view bookings')
            ? Booking::select('code', DB::raw('COUNT(*) as usage_count'))
                ->groupBy('code')
                ->orderBy('usage_count', 'desc')
                ->take(10)
                ->get()
            : collect();

        // Top Customers by Bookings (only if user can view customers and bookings)
        $topCustomers = ($user->can('view customers') && $user->can('view bookings'))
            ? Customer::withCount('bookings')
                ->get()
                ->map(function ($customer) {
                    return [
                        'id' => $customer->id,
                        'name' => $customer->name,
                        'bookings_count' => $customer->bookings_count,
                        'total_revenue' => $customer->bookings->sum('net_amount'),
                    ];
                })
                ->sortByDesc('bookings_count')
                ->take(10)
                ->values()
            : collect();

        // Top Customers by Revenue (only if user can view customers and bookings)
        $topCustomersByRevenue = ($user->can('view customers') && $user->can('view bookings'))
            ? Customer::get()
                ->map(function ($customer) {
                    return [
                        'id' => $customer->id,
                        'name' => $customer->name,
                        'total_revenue' => $customer->bookings->sum('net_amount'),
                        'bookings_count' => $customer->bookings->count(),
                    ];
                })
                ->sortByDesc('total_revenue')
                ->take(10)
                ->values()
            : collect();

        // Top Hotels by Room Nights Production (only if user can view hotels and bookings)
        $topHotelsByRoomNights = ($user->can('view hotels') && $user->can('view bookings'))
            ? Hotel::get()
                ->map(function ($hotel) {
                    $bookings = Booking::where('hotel_id', $hotel->id)->get();
                    $roomNightsProduction = 0;

                    foreach ($bookings as $booking) {
                        $rooms = BookingRoom::where('booking_id', $booking->id)->get();
                        foreach ($rooms as $room) {
                            $roomNightsProduction += $room->room_count * $booking->nights;
                        }
                    }

                    return [
                        'id' => $hotel->id,
                        'name' => $hotel->name,
                        'room_nights_production' => $roomNightsProduction,
                    ];
                })
                ->sortByDesc('room_nights_production')
                ->take(8)
                ->values()
            : collect();

        return view('admin.pages.dashboard', compact(
            'totalBookings',
            'confirmedBookings',
            'pendingBookings',
            'cancelledBookings',
            'totalAmount',
            'paidAmount',
            'recentBookings',
            'upcomingBookings',
            'paidBookings',
            'unpaidBookings',
            'partialBookings',
            'revisedBookings',
            'overpaidBookings',
            'hotelsSales',
            'mostUsedCodes',
            'topCustomers',
            'topCustomersByRevenue',
            'topHotelsByRoomNights',
            'upcomingOptionDates',
        ));
    }

    public function logout()
    {
        $user = Auth::user();

        // Log logout activity
        activity()
            ->causedBy($user)
            ->withProperties([
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ])
            ->log(__('activity.logout_by', ['name' => $user->name]));

        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login')->with('success', __('You have been logged out'));
    }
}
