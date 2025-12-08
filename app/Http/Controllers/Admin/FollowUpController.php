<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\FollowUp;
use Illuminate\Http\Request;

class FollowUpController extends Controller
{
    /**
     * Store a newly created follow-up.
     */
    public function store(Request $request, string $customerId)
    {
        $validated = $request->validate([
            'status' => 'required|in:none,in_progress,awaiting_replay,completed,canceled',
            'notes' => 'nullable|string',
        ]);

        $customer = Customer::findOrFail($customerId);

        $followUp = $customer->followUps()->create([
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'followUp' => $followUp->load('customer'),
            'message' => __('Follow-up created successfully'),
        ]);
    }

    /**
     * Update the latest follow-up status.
     */
    public function updateLatest(Request $request, string $customerId)
    {
        $validated = $request->validate([
            'status' => 'required|in:none,in_progress,awaiting_replay,completed,canceled',
        ]);

        $customer = Customer::findOrFail($customerId);
        $latestFollowUp = $customer->latestFollowUp;

        if ($latestFollowUp) {
            $latestFollowUp->update([
                'status' => $validated['status'],
            ]);

            return response()->json([
                'success' => true,
                'followUp' => $latestFollowUp->fresh(),
                'message' => __('Follow-up status updated successfully'),
            ]);
        }

        // If no follow-up exists, create one
        $followUp = $customer->followUps()->create([
            'status' => $validated['status'],
        ]);

        return response()->json([
            'success' => true,
            'followUp' => $followUp,
            'message' => __('Follow-up created successfully'),
        ]);
    }

    /**
     * Get all follow-ups for a customer.
     */
    public function index(string $customerId)
    {
        $customer = Customer::findOrFail($customerId);
        $followUps = $customer->followUps()->latest()->get();

        return response()->json([
            'success' => true,
            'followUps' => $followUps,
        ]);
    }
}
