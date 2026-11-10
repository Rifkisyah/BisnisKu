<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SalesRevenueController extends Controller
{
    private function transactionStatusColumn() {
        return (new Transaction())->getTable() . '.status';
    }

    private function getReportData(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->toDateString());
        $endDate   = $request->get('end_date', now()->toDateString());
        $limit     = $request->get('limit', 10);

        $startCarbon = Carbon::parse($startDate)->startOfDay();
        $endCarbon   = Carbon::parse($endDate)->endOfDay();

        // ── POS Transactions (only, no service) ───────────────────────────────
        $transactionQuery = Transaction::with('cashier', 'items.product')
            ->where('status', 'paid')
            ->whereBetween('transaction_date', [$startCarbon, $endCarbon])
            ->when($request->payment_method, fn($q, $pm) => $q->where('payment_method', $pm));

        $salesRevenue     = (clone $transactionQuery)->sum('total');
        $totalTransactions = (clone $transactionQuery)->count();
        $totalDiscount    = (clone $transactionQuery)->sum('discount');

        $transactions = (clone $transactionQuery)->latest('transaction_date')->limit($limit)->get();

        // ── Gross Profit & Net Profit ─────────────────────────────────────────
        $grossProfit = TransactionItem::whereHas('transaction',
                fn($q) => $q->where('status', 'paid')
                             ->whereBetween('transaction_date', [$startCarbon, $endCarbon]))
            ->join('products', 'transaction_items.product_code', '=', 'products.product_code')
            ->selectRaw('SUM((transaction_items.unit_price - products.purchase_price) * transaction_items.quantity) as gp')
            ->value('gp') ?? 0;
            
        $netProfit = $grossProfit - $totalDiscount;

        // ── Sales Trend (Daily) ───────────────────────────────────────────────
        $salesTrendDaily = Transaction::completed()
            ->inPeriod($startCarbon, $endCarbon)
            ->selectRaw('DATE(transaction_date) as date, SUM(total) as total, COUNT(*) as trx_count')
            ->groupByRaw('DATE(transaction_date)')
            ->orderBy('date')
            ->get();

        // ── Payment Method Breakdown ──────────────────────────────────────────
        $paymentBreakdown = Transaction::completed()
            ->inPeriod($startCarbon, $endCarbon)
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total) as total')
            ->groupBy('payment_method')
            ->get();

        // ── Cashier Breakdown ─────────────────────────────────────────────────
        $cashierBreakdown = Transaction::completed()
            ->inPeriod($startCarbon, $endCarbon)
            ->join('users', 'transactions.cashier_id', '=', 'users.id')
            ->selectRaw('users.username as cashier_name, COUNT(*) as count, SUM(total) as total')
            ->groupBy('users.id', 'users.username')
            ->get();

        // ── Debt Analysis ─────────────────────────────────────────────────────
        $debts         = Debt::with('payments')
            ->whereBetween('debt_date', [$startCarbon, $endCarbon])
            ->orderByDesc('debt_date')->get();
        $totalDebts    = $debts->whereNotIn('status', ['paid'])->count();
        $totalDebtAmt  = $debts->whereNotIn('status', ['paid'])->sum('remaining_amount');
        $overdueDebts  = $debts->filter(fn($d) => $d->isOverdue())->count();
        $paidDebtAmt   = $debts->sum('paid_amount');

        // Debt trend (monthly)
        $debtTrend = Debt::whereBetween('debt_date', [$startCarbon, $endCarbon])
            ->selectRaw('DATE_FORMAT(debt_date, "%Y-%m") as month, SUM(total_amount) as total, SUM(paid_amount) as paid')
            ->groupByRaw('DATE_FORMAT(debt_date, "%Y-%m")')
            ->orderBy('month')
            ->get();

        // Active debts table
        $activeDebts = Debt::with('payments')
            ->whereBetween('debt_date', [$startCarbon, $endCarbon])
            ->whereNotIn('status', ['paid'])
            ->orderBy('due_date')
            ->get();

        return compact(
            'transactions', 'salesRevenue', 'totalTransactions', 'grossProfit', 'netProfit',
            'startDate', 'endDate', 'limit',
            'salesTrendDaily',
            'paymentBreakdown', 'cashierBreakdown',
            'debts', 'totalDebts', 'totalDebtAmt', 'overdueDebts', 'paidDebtAmt',
            'debtTrend', 'activeDebts'
        );
    }

    public function index(Request $request)
    {
        $data = $this->getReportData($request);
        return view('reports.sales-revenue.index', $data);
    }

    public function exportPdf(Request $request)
    {
        $data = $this->getReportData($request);
        
        $data['chart_sales_trend'] = $request->input('chart_sales_trend');
        $data['chart_debt_trend'] = $request->input('chart_debt_trend');
        
        $data['storeName'] = \App\Models\Setting::get('store_name', 'Nama Toko');
        $data['storeAddress'] = \App\Models\Setting::get('store_address', '');
        $data['storePhone'] = \App\Models\Setting::get('store_phone', '');

        $pdf = Pdf::loadView('reports.pdf.sales-revenue', $data)->setPaper('a4', 'landscape');
        return $pdf->download('laporan-penjualan-pendapatan-' . now()->format('Y-m-d') . '.pdf');
    }

    public function transactions(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->toDateString());
        $endDate   = $request->get('end_date', now()->toDateString());

        $startCarbon = Carbon::parse($startDate)->startOfDay();
        $endCarbon   = Carbon::parse($endDate)->endOfDay();

        $transactions = Transaction::with('cashier')
            ->where($this->transactionStatusColumn(), 'paid')
            ->whereBetween('transaction_date', [$startCarbon, $endCarbon])
            ->when($request->payment_method, fn($q, $pm) => $q->where('payment_method', $pm))
            ->when($request->cashier_id, fn($q, $id) => $q->where('cashier_id', $id))
            ->latest('transaction_date')
            ->paginate(50)
            ->withQueryString();

        $cashiers = User::whereHas('role', fn($q) => $q->whereIn('name', ['kasir', 'owner']))->get();

        return view('reports.sales-revenue.details.transactions', compact('transactions', 'startDate', 'endDate', 'cashiers'));
    }

    public function debts(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->toDateString());
        $endDate   = $request->get('end_date', now()->toDateString());

        $startCarbon = Carbon::parse($startDate)->startOfDay();
        $endCarbon   = Carbon::parse($endDate)->endOfDay();

        $debts = Debt::with('payments')
            ->whereBetween('debt_date', [$startCarbon, $endCarbon])
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->orderBy('due_date')
            ->paginate(50)
            ->withQueryString();

        return view('reports.sales-revenue.details.debts', compact('debts', 'startDate', 'endDate'));
    }

    public function products(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->toDateString());
        $endDate   = $request->get('end_date', now()->toDateString());
        $startCarbon = Carbon::parse($startDate)->startOfDay();
        $endCarbon   = Carbon::parse($endDate)->endOfDay();

        $sort = $request->get('sort', 'desc') === 'asc' ? 'asc' : 'desc';

        $products = TransactionItem::whereHas('transaction', fn($q) => $q->where($this->transactionStatusColumn(), 'paid')
                ->whereBetween('transaction_date', [$startCarbon, $endCarbon]))
            ->join('products', 'transaction_items.product_code', '=', 'products.product_code')
            ->selectRaw('products.product_code, products.name, SUM(transaction_items.quantity) as total_qty, SUM(transaction_items.subtotal) as total_revenue')
            ->groupBy('products.product_code', 'products.name')
            ->orderBy('total_qty', $sort)
            ->paginate(50)
            ->withQueryString();

        return view('reports.sales-revenue.details.products', compact('products', 'startDate', 'endDate'));
    }

    public function categories(Request $request)
    {
        $startDate = $request->get('start_date', now()->subDays(30)->toDateString());
        $endDate   = $request->get('end_date', now()->toDateString());
        $startCarbon = Carbon::parse($startDate)->startOfDay();
        $endCarbon   = Carbon::parse($endDate)->endOfDay();

        $sort = $request->get('sort', 'desc') === 'asc' ? 'asc' : 'desc';

        $categories = TransactionItem::whereHas('transaction', fn($q) => $q->where($this->transactionStatusColumn(), 'paid')
                ->whereBetween('transaction_date', [$startCarbon, $endCarbon]))
            ->join('products', 'transaction_items.product_code', '=', 'products.product_code')
            ->join('categories', 'products.category_code', '=', 'categories.category_code')
            ->selectRaw('categories.category_code as id, categories.name, SUM(transaction_items.quantity) as total_qty, SUM(transaction_items.subtotal) as total_revenue')
            ->groupBy('categories.category_code', 'categories.name')
            ->orderBy('total_qty', $sort)
            ->paginate(50)
            ->withQueryString();

        return view('reports.sales-revenue.details.categories', compact('categories', 'startDate', 'endDate'));
    }
}
