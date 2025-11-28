<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Hotel;
use App\Models\HotelBankAccount;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function index()
    {
        $hotels = Hotel::with('bankAccounts.currency')->latest()->paginate(10);
        return view('admin.pages.hotels.index', compact('hotels'));
    }

    public function create()
    {
        $currencies = Currency::where('is_active', true)->get();
        return view('admin.pages.hotels.create', compact('currencies'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'is_active' => 'boolean',
            'bank_accounts' => 'nullable|array',
            'bank_accounts.*.currency_id' => 'required|exists:currencies,id',
            'bank_accounts.*.bank_name' => 'required|string|max:255',
            'bank_accounts.*.account_number' => 'required|string|max:255',
        ]);

        $hotel = Hotel::create([
            'name' => $validated['name'],
            'address' => $validated['address'],
            'is_active' => $request->has('is_active') ? $request->is_active : false,
        ]);

        if (!empty($validated['bank_accounts'])) {
            foreach ($validated['bank_accounts'] as $account) {
                $hotel->bankAccounts()->create($account);
            }
        }

        return redirect()->route('hotels.index')->with('success', __('Hotel created successfully'));
    }

    public function edit(string $id)
    {
        $hotel = Hotel::with('bankAccounts')->findOrFail($id);
        $currencies = Currency::where('is_active', true)->get();
        return view('admin.pages.hotels.edit', compact('hotel', 'currencies'));
    }

    public function update(Request $request, string $id)
    {
        $hotel = Hotel::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'is_active' => 'boolean',
            'bank_accounts' => 'nullable|array',
            'bank_accounts.*.currency_id' => 'required|exists:currencies,id',
            'bank_accounts.*.bank_name' => 'required|string|max:255',
            'bank_accounts.*.account_number' => 'required|string|max:255',
        ]);

        $hotel->update([
            'name' => $validated['name'],
            'address' => $validated['address'],
            'is_active' => $request->has('is_active') ? $request->is_active : false,
        ]);

        // Delete existing bank accounts and recreate
        $hotel->bankAccounts()->delete();

        if (!empty($validated['bank_accounts'])) {
            foreach ($validated['bank_accounts'] as $account) {
                $hotel->bankAccounts()->create($account);
            }
        }

        return redirect()->route('hotels.index')->with('success', __('Hotel updated successfully'));
    }

    public function destroy(string $id)
    {
        $hotel = Hotel::findOrFail($id);
        $hotel->delete();

        return redirect()->route('hotels.index')->with('success', __('Hotel deleted successfully'));
    }
}
