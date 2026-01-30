<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Hotel;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mpdf\Mpdf;

class WalletController extends Controller
{
    public function store(Request $request, Customer $customer)
    {
        return $this->processTransaction($request, $customer);
    }

    public function storeHotel(Request $request, Hotel $hotel)
    {
        return $this->processTransaction($request, $hotel);
    }

    private function processTransaction(Request $request, $model)
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

            $model->walletTransactions()->create([
                'amount' => $request->amount,
                'type' => $request->type,
                'currency_id' => $request->currency_id,
                'description' => $request->description,
                'reference' => $request->reference ?? 'MANUAL',
            ]);

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
        return $this->generatePdf($request, $customer, 'customer');
    }

    public function exportHotelWalletPdf(Request $request, Hotel $hotel)
    {
        return $this->generatePdf($request, $hotel, 'hotel');
    }

    private function generatePdf(Request $request, $model, $type)
    {
        $query = $model->walletTransactions()
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

        $balanceQuery = $model->walletTransactions()
            ->select(
                'currency_id',
                DB::raw('SUM(CASE WHEN type = "credit" THEN amount ELSE -amount END) as balance')
            )
            ->with('currency')
            ->groupBy('currency_id')
            ->reorder();

        if ($request->filled('currency_id')) {
            $balanceQuery->where('currency_id', $request->currency_id);
        }

        $balances = $balanceQuery->get();

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 16,
            'margin_bottom' => 16,
            'margin_header' => 9,
            'margin_footer' => 9,
        ]);

        $mpdf->SetDirectionality('rtl');
        $mpdf->autoScriptToLang = true;
        $mpdf->autoLangToFont = true;

        $html = view('admin.pdf.wallet_statement', compact('model', 'transactions', 'balances', 'type'))->render();

        $mpdf->WriteHTML($html);

        return $mpdf->Output('wallet-statement.pdf', 'I');
    }
}
