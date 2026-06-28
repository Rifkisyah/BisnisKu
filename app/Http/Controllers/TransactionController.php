<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', now()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        $query = Transaction::with('cashier', 'items.product')
            ->whereBetween('transaction_date', [$startDate, \Carbon\Carbon::parse($endDate)->endOfDay()]);

        if (auth()->user()->isKasir()) {
            $query->where('cashier_id', auth()->id());
        }

        $query->when($request->search, fn($q, $s) => $q->where('transaction_code', 'like', "%{$s}%"))
              ->when($request->payment_method, fn($q, $pm) => $q->where('payment_method', $pm));

        if ($request->get('export') === 'pdf') {
            $transactions = $query->latest('transaction_date')->get();
            $totalRevenue = $transactions->sum('total');
            $pdf = Pdf::loadView('transactions.pdf', compact('transactions', 'totalRevenue', 'startDate', 'endDate'));
            return $pdf->download('laporan-transaksi.pdf');
        }

        $transactions = $query->latest('transaction_date')->paginate(15)->withQueryString();
        return view('transactions.index', compact('transactions', 'startDate', 'endDate'));
    }

    public function show(Transaction $transaction)
    {
        if (auth()->user()->isKasir() && $transaction->cashier_id !== auth()->id()) { abort(403); }
        $transaction->load('items.product', 'cashier');
        return view('transactions.show', compact('transaction'));
    }

    public function receipt(Transaction $transaction)
    {
        $transaction->load('items.product', 'cashier');
        return view('transactions.receipt', compact('transaction'));
    }

    public function receiptPdf(Transaction $transaction)
    {
        $transaction->load('items.product', 'cashier');
        $pdf = Pdf::loadView('transactions.receipt-pdf', compact('transaction'));
        return $pdf->download("struk-{$transaction->transaction_code}.pdf");
    }

    public function cancel(Transaction $transaction)
    {
        if (auth()->user()->isKasir()) { abort(403); }
        $transaction->update(['status' => 'canceled']);
        foreach ($transaction->items as $item) {
            $item->product->increment('stock', $item->quantity);
        }
        return back()->with('success', __('messages.transaction_canceled'));
    }

    public function destroy(Transaction $transaction)
    {
        if (auth()->user()->isKasir()) { abort(403); }
        $transaction->delete();
        return redirect()->route('transactions.index')
            ->with('success', __('messages.deleted', ['item' => __('messages.transaction')]));
    }
}
