<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use App\Models\Hotel;
use App\Models\HotelBankAccount;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view hotels')->only(['index', 'show']);
        $this->middleware('permission:create hotels')->only(['create', 'store', 'quickCreate']);
        $this->middleware('permission:edit hotels')->only(['edit', 'update']);
        $this->middleware('permission:delete hotels')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Hotel::with('bankAccounts.currency');

        // Search by name or address
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('address', 'like', '%' . $request->search . '%');
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        // Validate sort_by column
        $allowedSortColumns = ['name', 'address', 'is_active', 'created_at', 'updated_at'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at';
        }

        // Validate sort_order
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        $query->orderBy($sortBy, $sortOrder);

        $hotels = $query->paginate(10)->withQueryString();
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
            'company_name' => 'nullable|string|max:255',
            'address' => 'required|string|max:500',
            'is_active' => 'boolean',
            'bank_accounts' => 'nullable|array',
            'bank_accounts.*.currency_id' => 'required|exists:currencies,id',
            'bank_accounts.*.bank_name' => 'required|string|max:255',
            'bank_accounts.*.account_number' => 'required|string|max:255',
        ]);

        $hotel = Hotel::create([
            'name' => $validated['name'],
            'company_name' => $validated['company_name'] ?? null,
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

    public function show(Request $request, string $id)
    {
        $hotel = Hotel::with(['bankAccounts.currency', 'customers'])->findOrFail($id);
        $currencies = Currency::where('is_active', true)->get();

        $query = $hotel->walletTransactions()->with('currency')->orderBy('created_at', 'desc');

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('currency_id')) {
            $query->where('currency_id', $request->currency_id);
        }

        $walletTransactions = $query->paginate(10)->withQueryString();

        return view('admin.pages.hotels.show', compact('hotel', 'currencies', 'walletTransactions'));
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
            'company_name' => 'nullable|string|max:255',
            'address' => 'required|string|max:500',
            'is_active' => 'boolean',
            'bank_accounts' => 'nullable|array',
            'bank_accounts.*.currency_id' => 'required|exists:currencies,id',
            'bank_accounts.*.bank_name' => 'required|string|max:255',
            'bank_accounts.*.account_number' => 'required|string|max:255',
        ]);

        $hotel->update([
            'name' => $validated['name'],
            'company_name' => $validated['company_name'] ?? null,
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

    /**
     * Quick create a hotel with just the name (for use in select dropdowns)
     */
    public function quickCreate(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $hotel = Hotel::create([
            'name' => $validated['name'],
            'address' => '', // Empty address, can be edited later
            'is_active' => true, // Set as active by default
        ]);

        return response()->json([
            'success' => true,
            'hotel' => [
                'id' => $hotel->id,
                'name' => $hotel->name,
            ]
        ]);
    }
}
