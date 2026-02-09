<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Hotel;
use App\Models\Currency;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view customers')->only(['index', 'show']);
        $this->middleware('permission:create customers')->only(['create', 'store']);
        $this->middleware('permission:edit customers')->only(['edit', 'update']);
        $this->middleware('permission:delete customers')->only(['destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Customer::with(['latestFollowUp', 'walletTransactions.currency']);

        // Search by name or phone
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                    ->orWhere('phone_1', 'like', '%' . $request->search . '%')
                    ->orWhere('phone_2', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by priority
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Filter by source
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        // Validate sort_by column
        $allowedSortColumns = ['name', 'email', 'phone_1', 'type', 'status', 'priority', 'created_at', 'updated_at'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'created_at';
        }

        // Validate sort_order
        if (!in_array($sortOrder, ['asc', 'desc'])) {
            $sortOrder = 'desc';
        }

        $query->orderBy($sortBy, $sortOrder);

        $customers = $query->paginate(10)->withQueryString();
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
            'phone_1' => 'required|string|max:255|unique:customers,phone_1',
            'phone_2' => 'nullable|string|max:255|unique:customers,phone_2',
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

        // Create default follow-up with status 'none'
        $customer->followUps()->create([
            'status' => 'none',
            'notes' => null,
        ]);

        return redirect()->route('customers.index')->with('success', __('Customer created successfully'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        $customer = Customer::with(['hotels', 'bookings.hotel', 'bookings.currency', 'bookings.rooms', 'followUps'])
            ->findOrFail($id);

        // Wallet Transactions with Filters
        $walletQuery = $customer->walletTransactions()->with('currency')->orderBy('created_at', 'desc');

        if ($request->filled('date_from')) {
            $walletQuery->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $walletQuery->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('currency_id')) {
            $walletQuery->where('currency_id', $request->currency_id);
        }

        $walletTransactions = $walletQuery->get(); // Or paginate if list is long, but get() is fine for now as per previous code

        // Calculate statistics
        $totalBookings = $customer->bookings->count();
        $totalAmount = $customer->bookings->sum('total_amount');
        $paidAmount = $customer->bookings->sum('paid_amount');
        $pendingAmount = $totalAmount - $paidAmount;

        // Get recent bookings (last 10)
        $recentBookings = $customer->bookings()
            ->with(['hotel', 'currency'])
            ->latest()
            ->take(10)
            ->get();

        // Get latest follow-up
        $latestFollowUp = $customer->latestFollowUp;

        $currencies = Currency::where('is_active', true)->get();

        return view('admin.pages.customers.show', compact(
            'customer',
            'walletTransactions',
            'totalBookings',
            'totalAmount',
            'paidAmount',
            'pendingAmount',
            'recentBookings',
            'latestFollowUp',
            'currencies'
        ));
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
            'phone_1' => 'required|string|max:255|unique:customers,phone_1,'.$customer->id,
            'phone_2' => 'nullable|string|max:255|unique:customers,phone_2,'.$customer->id,
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
