<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\DebtPayment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DebtController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->get('start_date', now()->toDateString());
        $endDate = $request->get('end_date', now()->toDateString());

        $query = Debt::whereBetween('debt_date', [$startDate, \Carbon\Carbon::parse($endDate)->endOfDay()])
            ->when($request->search, fn($q, $s) => $q->where('debtor_name', 'like', "%{$s}%")->orWhere('debt_code', 'like', "%{$s}%"))
            ->when($request->status, fn($q, $st) => $q->where('status', $st));

        $debts = $query->latest()->paginate(15)->withQueryString();

        if ($request->get('export') === 'pdf') {
            $allDebts = $query->get();
            $pdf = Pdf::loadView('debts.pdf', ['debts' => $allDebts, 'startDate' => $startDate, 'endDate' => $endDate]);
            return $pdf->download('laporan-hutang.pdf');
        }

        return view('debts.index', compact('debts', 'startDate', 'endDate'));
    }

    public function create()
    {
        return view('debts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'debtor_name'    => 'required|string|min:3|max:100',
            'debtor_contact' => 'nullable|string|min:7|max:20',
            'debtor_address' => 'nullable|string|max:300',
            'total_amount'   => 'required|numeric|min:1',
            'debt_date'      => 'required|date',
            'due_date'       => 'nullable|date|after_or_equal:debt_date',
            'notes'          => 'nullable|string|max:500',
        ]);
        $validated['debt_code'] = Debt::generateCode();
        $validated['remaining_amount'] = $validated['total_amount'];
        $validated['status'] = 'unpaid';
        Debt::create($validated);
        return redirect()->route('debts.index')->with('success', __('messages.created', ['item' => __('messages.debt')]));
    }

    public function show(Debt $debt)
    {
        $debt->load('payments', 'transaction.items.product');
        return view('debts.show', compact('debt'));
    }

    public function addPayment(Request $request, Debt $debt)
    {
        $validated = $request->validate([
            'amount'         => 'required|numeric|min:1|max:' . $debt->remaining_amount,
            'payment_date'   => 'required|date',
            'payment_method' => 'required|in:cash,qris',
            'notes'          => 'nullable|string|max:500',
        ]);
        DB::transaction(function () use ($validated, $debt) {
            $validated['debt_code'] = $debt->debt_code;
            DebtPayment::create($validated);
            $debt->remaining_amount -= $validated['amount'];
            $debt->status = $debt->remaining_amount <= 0 ? 'paid' : 'partial';
            $debt->save();
        });
        return back()->with('success', __('messages.created', ['item' => __('messages.payment')]));
    }

    public function destroy(Debt $debt)
    {
        $debt->delete();
        return redirect()->route('debts.index')->with('success', __('messages.deleted', ['item' => __('messages.debt')]));
    }
}
