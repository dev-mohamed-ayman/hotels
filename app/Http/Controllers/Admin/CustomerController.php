<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Hotel;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $customers = Customer::with('hotels')->latest()->paginate(10);
        return view('admin.pages.customers.index', compact('customers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $hotels = Hotel::where('is_active', true)->get();
        return view('admin.pages.customers.create', compact('hotels'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_1' => 'required|string|max:255',
            'phone_2' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'type' => 'required|in:individual,corporate',
            'status' => 'required|in:potential,cancelled,active',
            'priority' => 'required|in:low,medium,high,urgent',
            'source' => 'nullable|in:phone,website,social_media,referral,direct_visit',
            'hotels' => 'nullable|array',
            'hotels.*' => 'exists:hotels,id',
        ]);

        $customer = Customer::create([
            'name' => $validated['name'],
            'phone_1' => $validated['phone_1'],
            'phone_2' => $validated['phone_2'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'type' => $validated['type'],
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'source' => $validated['source'] ?? null,
        ]);

        if (!empty($validated['hotels'])) {
            $customer->hotels()->sync($validated['hotels']);
        }

        return redirect()->route('customers.index')->with('success', __('Customer created successfully'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $customer = Customer::with('hotels')->findOrFail($id);
        $hotels = Hotel::where('is_active', true)->get();
        return view('admin.pages.customers.edit', compact('customer', 'hotels'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $customer = Customer::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone_1' => 'required|string|max:255',
            'phone_2' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'type' => 'required|in:individual,corporate',
            'status' => 'required|in:potential,cancelled,active',
            'priority' => 'required|in:low,medium,high,urgent',
            'source' => 'nullable|in:phone,website,social_media,referral,direct_visit',
            'hotels' => 'nullable|array',
            'hotels.*' => 'exists:hotels,id',
        ]);

        $customer->update([
            'name' => $validated['name'],
            'phone_1' => $validated['phone_1'],
            'phone_2' => $validated['phone_2'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'type' => $validated['type'],
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'source' => $validated['source'] ?? null,
        ]);

        if (!empty($validated['hotels'])) {
            $customer->hotels()->sync($validated['hotels']);
        } else {
            $customer->hotels()->detach();
        }

        return redirect()->route('customers.index')->with('success', __('Customer updated successfully'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return redirect()->route('customers.index')->with('success', __('Customer deleted successfully'));
    }
}
