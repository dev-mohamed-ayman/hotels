<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;

class WalletController extends Controller
{
    public function store(Request $request, Customer $customer)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:credit,debit',
            'currency_id' => 'required|exists:currencies,id',
            'description' => 'nullable|string|max:255',
            'reference' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Create transaction
            $customer->walletTransactions()->create([
                'amount' => $request->amount,
                'type' => $request->type,
                'currency_id' => $request->currency_id,
                'description' => $request->description,
                'reference' => $request->reference ?? 'MANUAL',
            ]);

            // Note: We are not updating a single 'wallet' column on the customer table
            // because we support multiple currencies. The balance is calculated dynamically.

            DB::commit();

            return back()->with('success', __('Transaction successful'));

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request, WalletTransaction $transaction)
    {
        $request->validate([
            'created_at' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:credit,debit',
            'currency_id' => 'required|exists:currencies,id',
            'description' => 'nullable|string|max:255',
            'reference' => 'nullable|string|max:255',
        ]);

        $transaction->update([
            'created_at' => $request->created_at,
            'amount' => $request->amount,
            'type' => $request->type,
            'currency_id' => $request->currency_id,
            'description' => $request->description,
            'reference' => $request->reference,
        ]);

        return back()->with('success', __('Transaction updated successfully'));
    }

    public function destroy(WalletTransaction $transaction)
    {
        $transaction->delete();

        return back()->with('success', __('Transaction deleted successfully'));
    }

    public function exportWalletPdf(Request $request, Customer $customer)
    {
        $query = $customer->walletTransactions()
            ->with('currency')
            ->orderBy('created_at', 'desc');

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('currency_id')) {
            $query->where('currency_id', $request->currency_id);
        }

        $transactions = $query->get();

        $balanceQuery = $customer->walletTransactions()
            ->select('currency_id', DB::raw('SUM(CASE WHEN type = "debit" THEN amount ELSE -amount END) as balance'))
            ->with('currency')
            ->groupBy('currency_id')
            ->reorder();

        if ($request->filled('date_from')) {
            $balanceQuery->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $balanceQuery->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('currency_id')) {
            $balanceQuery->where('currency_id', $request->currency_id);
        }

        $balances = $balanceQuery->get();

        $html = view('admin.pages.customers.pdf.wallet', compact('customer', 'transactions', 'balances'))->render();

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

        return $mpdf->Output('wallet-statement-'.$customer->name.'.pdf', 'D');
    }
}
