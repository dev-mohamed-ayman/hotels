<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;
use Carbon\Carbon;

class ActivityLogController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view activity log')->only(['index', 'show', 'export']);
        $this->middleware('permission:delete activity log')->only(['destroy', 'bulkDelete']);
    }
    public function index(Request $request)
    {
        $query = Activity::with(['causer', 'subject'])
            ->latest();

        // Filter by user
        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id);
        }

        // Filter by model type
        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
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

        $activities = $query->paginate(20);

        // Get filter options
        $users = \App\Models\User::select('id', 'name')->get();
        $modelTypes = Activity::distinct()->pluck('subject_type')->filter();
        $events = Activity::distinct()->pluck('event')->filter();

        return view('admin.pages.activity-log.index', compact(
            'activities', 
            'users', 
            'modelTypes', 
            'events'
        ));
    }

    public function show(Activity $activity)
    {
        $activity->load(['causer', 'subject']);
        
        return view('admin.pages.activity-log.show', compact('activity'));
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();

        return redirect()->route('activity-log.index')
            ->with('success', __('activity.record_deleted_success'));
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:activity_log,id'
        ]);

        Activity::whereIn('id', $request->ids)->delete();

        return response()->json([
            'success' => true,
            'message' => __('activity.records_deleted_success')
        ]);
    }

    public function export(Request $request)
    {
        $query = Activity::with(['causer', 'subject'])
            ->latest();

        // Apply same filters as index
        if ($request->filled('user_id')) {
            $query->where('causer_id', $request->user_id);
        }

        if ($request->filled('subject_type')) {
            $query->where('subject_type', $request->subject_type);
        }

        if ($request->filled('event')) {
            $query->where('event', $request->event);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }

        $activities = $query->get();

        $filename = 'activity-log-' . now()->format('Y-m-d-H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($activities) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, [
                __('activity.date_time'),
                __('activity.user'),
                __('activity.activity'),
                __('activity.type'),
                __('activity.description'),
                'IP Address'
            ]);

            foreach ($activities as $activity) {
                fputcsv($file, [
                    $activity->created_at->format('Y-m-d H:i:s'),
                    $activity->causer?->name ?? __('activity.system'),
                    $activity->event,
                    class_basename($activity->subject_type ?? ''),
                    $activity->description,
                    $activity->properties['ip'] ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}