<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\Customer;
use App\Models\Hotel;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Bookings Statistics
        $totalBookings = Booking::count();
        $confirmedBookings = Booking::where('status', 'confirmed')->count();
        $pendingBookings = Booking::where('status', 'pending')->count();
        $cancelledBookings = Booking::where('status', 'cancelled')->count();

        // Room Nights Production (عدد الغرف × الليالي)
        $roomNightsProduction = Booking::with('rooms')
            ->get()
            ->sum(function ($booking) {
                $totalRooms = $booking->rooms->sum('room_count');
                return $totalRooms * $booking->nights;
            });

        // Financial Statistics
        $totalAmount = Booking::sum('total_amount');
        $paidAmount = Booking::sum('paid_amount');
        $pendingAmount = $totalAmount - $paidAmount;

        // Customers Statistics
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::where('status', 'active')->count();
        $potentialCustomers = Customer::where('status', 'potential')->count();
        $cancelledCustomers = Customer::where('status', 'cancelled')->count();

        // Hotels Statistics
        $totalHotels = Hotel::count();
        $activeHotels = Hotel::where('is_active', true)->count();
        $inactiveHotels = Hotel::where('is_active', false)->count();

        // Recent Bookings
        $recentBookings = Booking::with(['customer', 'hotel', 'currency'])
            ->latest()
            ->take(5)
            ->get();

        // Monthly Bookings (Last 6 months)
        $monthlyBookings = Booking::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Top Hotels by Bookings
        $topHotels = Hotel::withCount('bookings')
            ->orderBy('bookings_count', 'desc')
            ->take(5)
            ->get();

        // Payment Status Statistics
        $paidBookings = Booking::whereRaw('paid_amount >= total_amount')->count();
        $unpaidBookings = Booking::where('paid_amount', 0)->count();
        $partialBookings = Booking::whereRaw('paid_amount > 0 AND paid_amount < total_amount')->count();

        // Hotels Sales Statistics
        $hotelsSales = Hotel::get()
            ->map(function($hotel) {
                $bookings = Booking::where('hotel_id', $hotel->id)->get();
                $roomsCount = BookingRoom::whereHas('booking', function($q) use ($hotel) {
                    $q->where('hotel_id', $hotel->id);
                })->sum('room_count');
                
                return [
                    'id' => $hotel->id,
                    'name' => $hotel->name,
                    'total_sales' => $bookings->sum('total_amount'),
                    'paid_sales' => $bookings->sum('paid_amount'),
                    'bookings_count' => $bookings->count(),
                    'rooms_count' => $roomsCount,
                ];
            })
            ->sortByDesc('total_sales')
            ->take(10);

        // Total Rooms Statistics
        $totalRooms = BookingRoom::sum('room_count');
        $roomsByType = BookingRoom::select('room_type', DB::raw('SUM(room_count) as total'))
            ->groupBy('room_type')
            ->get()
            ->pluck('total', 'room_type');

        // Most Used Booking Codes
        $mostUsedCodes = Booking::select('code', DB::raw('COUNT(*) as usage_count'))
            ->groupBy('code')
            ->orderBy('usage_count', 'desc')
            ->take(10)
            ->get();

        // Top Customers by Bookings
        $topCustomers = Customer::withCount('bookings')
            ->get()
            ->map(function($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'bookings_count' => $customer->bookings_count,
                    'total_revenue' => $customer->bookings->sum('total_amount'),
                ];
            })
            ->sortByDesc('bookings_count')
            ->take(10)
            ->values();

        // Top Customers by Revenue
        $topCustomersByRevenue = Customer::get()
            ->map(function($customer) {
                return [
                    'id' => $customer->id,
                    'name' => $customer->name,
                    'total_revenue' => $customer->bookings->sum('total_amount'),
                    'bookings_count' => $customer->bookings->count(),
                ];
            })
            ->sortByDesc('total_revenue')
            ->take(10)
            ->values();

        // Currency Statistics
        $currencyStats = Currency::get()
            ->map(function($currency) {
                $bookings = Booking::where('currency_id', $currency->id)->get();
                return [
                    'id' => $currency->id,
                    'code' => $currency->code,
                    'symbol' => $currency->symbol,
                    'bookings_count' => $bookings->count(),
                    'total_amount' => $bookings->sum('total_amount'),
                    'paid_amount' => $bookings->sum('paid_amount'),
                ];
            });

        // Room Type Distribution
        $roomTypeDistribution = BookingRoom::select('room_type', DB::raw('SUM(room_count) as count'))
            ->groupBy('room_type')
            ->get();

        // Average Booking Value
        $averageBookingValue = Booking::avg('total_amount') ?? 0;

        // Average Nights per Booking
        $averageNights = Booking::avg('nights') ?? 0;

        // Total Rooms by Hotel (detailed)
        $hotelsWithRooms = Hotel::get()
            ->map(function($hotel) {
                $roomsCount = BookingRoom::whereHas('booking', function($q) use ($hotel) {
                    $q->where('hotel_id', $hotel->id);
                })->sum('room_count');
                
                return [
                    'id' => $hotel->id,
                    'name' => $hotel->name,
                    'rooms_count' => $roomsCount,
                    'bookings_count' => $hotel->bookings->count(),
                ];
            })
            ->sortByDesc('rooms_count')
            ->take(10);

        return view('admin.pages.dashboard', compact(
            'totalBookings',
            'confirmedBookings',
            'pendingBookings',
            'cancelledBookings',
            'roomNightsProduction',
            'totalAmount',
            'paidAmount',
            'pendingAmount',
            'totalCustomers',
            'activeCustomers',
            'potentialCustomers',
            'cancelledCustomers',
            'totalHotels',
            'activeHotels',
            'inactiveHotels',
            'recentBookings',
            'monthlyBookings',
            'topHotels',
            'paidBookings',
            'unpaidBookings',
            'partialBookings',
            'hotelsSales',
            'totalRooms',
            'roomsByType',
            'mostUsedCodes',
            'topCustomers',
            'topCustomersByRevenue',
            'currencyStats',
            'roomTypeDistribution',
            'averageBookingValue',
            'averageNights',
            'hotelsWithRooms'
        ));
    }

    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login')->with('success', __('You have been logged out'));
    }
}
