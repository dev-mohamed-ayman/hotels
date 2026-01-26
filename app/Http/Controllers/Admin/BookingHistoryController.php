<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;

class BookingHistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view bookings')->only(['index', 'show']);
        $this->middleware('permission:delete bookings')->only(['destroy', 'bulkDelete']);
    }

    /**
     * Display a listing of booking activity logs.
     */
    public function index(Request $request)
    {
        $query = Activity::with(['causer', 'subject'])
            ->where('subject_type', Booking::class)
            ->orderBy('created_at', 'desc');

        // Filter by booking
        if ($request->filled('booking_id')) {
            $query->where('subject_id', $request->booking_id);
        }

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id);
        }

        // Filter by event type
        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search in description
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $activities = $query->paginate(50);

        // Get filter options
        $bookings = Booking::select('id', 'code')->orderBy('code')->get();
        $users = \App\Models\User::select('id', 'name')->get();
        $events = ['created', 'updated', 'deleted'];

        return view('admin.pages.booking-history.index', compact(
            'activities',
            'bookings',
            'users',
            'events'
        ));
    }

    /**
     * Display activity logs for a specific booking.
     */
    public function show(Booking $booking)
    {
        $activities = $booking->activities()
            ->with('causer')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.pages.booking-history.show', compact('booking', 'activities'));
    }

    /**
     * Delete a single activity log.
     */
    public function destroy($id)
    {
        $activity = Activity::findOrFail($id);
        
        // Ensure it's a booking activity
        if ($activity->subject_type !== Booking::class) {
            return redirect()->back()
                ->with('error', __('This is not a booking activity record'));
        }

        $activity->delete();

        return redirect()->back()
            ->with('success', __('Booking history record deleted successfully'));
    }

    /**
     * Delete all booking history records.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:activity_log,id'
        ]);

        // Delete only booking activities
        Activity::whereIn('id', $request->ids)
            ->where('subject_type', Booking::class)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => __('Booking history records deleted successfully')
        ]);
    }

    /**
     * Delete all booking history for a specific booking.
     */
    public function deleteAllForBooking(Booking $booking)
    {
        $booking->activities()->delete();

        return redirect()->route('booking-history.index')
            ->with('success', __('All history for booking :code has been deleted', ['code' => $booking->code]));
    }
}
