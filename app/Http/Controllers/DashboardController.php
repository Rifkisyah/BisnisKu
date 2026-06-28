<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ServiceRepair;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\ProductClusteringService;
use App\Services\SmaForecastService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->isKasir()) {
            return $this->kasirDashboard();
        }

        if ($user->isTeknisi()) {
            return $this->teknisiDashboard();
        }

        return $this->ownerDashboard($request);
    }

    private function ownerDashboard(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : now()->startOfDay();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : now()->endOfDay();

        // 1. SALES REVENUE METRICS
        $totalRevenue = Transaction::where('status', 'completed')->whereBetween('transaction_date', [$startDate, $endDate])->sum('total');
        $transactionCount = Transaction::where('status', 'completed')->whereBetween('transaction_date', [$startDate, $endDate])->count();
        
        $salesTrend = Transaction::where('status', 'completed')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw('DATE(transaction_date) as date, SUM(total) as total, COUNT(*) as count')
            ->groupByRaw('DATE(transaction_date)')->orderBy('date')->get();

        $topProducts = TransactionItem::whereHas('transaction', fn($q) => $q->where('status', 'completed')->whereBetween('transaction_date', [$startDate, $endDate]))
            ->join('products', 'transaction_items.product_code', '=', 'products.product_code')
            ->selectRaw('products.name, SUM(transaction_items.quantity) as total_qty, SUM(transaction_items.subtotal) as total_revenue')
            ->groupBy('products.name')->orderByDesc('total_revenue')->limit(5)->get();

        // 2. SERVICE ANALYSIS METRICS
        $serviceRevenue = ServiceRepair::whereIn('status', ['completed', 'picked_up'])->whereBetween('completion_date', [$startDate, $endDate])->sum('total_cost');
        $activeRepairs = ServiceRepair::whereNotIn('status', ['completed', 'canceled', 'picked_up'])->count();
        
        $repairTrend = ServiceRepair::whereIn('status', ['completed', 'picked_up'])
            ->whereBetween('completion_date', [$startDate, $endDate])
            ->selectRaw('DATE(completion_date) as date, COUNT(*) as ticket_count, SUM(total_cost) as total_revenue')
            ->groupByRaw('DATE(completion_date)')->orderBy('date')->get();

        // 3. BUSINESS PERFORMANCE METRICS (K-Means & SMA)
        $clusteringService = new ProductClusteringService();
        $clusterResults = $clusteringService->cluster($startDate, $endDate);
        
        $smaService = new SmaForecastService();
        $diffDays = (int) $startDate->diffInDays($endDate) ?: 1;
        $smaResults = $smaService->calculateAll($diffDays, $endDate);
        $restockRecommendations = array_filter($smaResults, fn($r) => $r['needs_restock']);

        $productCount = Product::active()->count();

        return view('dashboard.owner', compact(
            'totalRevenue', 'transactionCount', 'salesTrend', 'topProducts',
            'serviceRevenue', 'activeRepairs', 'repairTrend',
            'clusterResults', 'smaResults', 'restockRecommendations', 'productCount',
            'startDate', 'endDate'
        ));
    }

    private function kasirDashboard()
    {
        $todayTransactions = Transaction::where('cashier_id', auth()->id())
            ->whereDate('transaction_date', today())
            ->where('status', 'completed')
            ->count();

        $todayRevenue = Transaction::where('cashier_id', auth()->id())
            ->whereDate('transaction_date', today())
            ->where('status', 'completed')
            ->sum('total');

        $recentTransactions = Transaction::where('cashier_id', auth()->id())
            ->where('status', 'completed')
            ->latest('transaction_date')
            ->limit(10)
            ->get();

        return view('dashboard.kasir', compact('todayTransactions', 'todayRevenue', 'recentTransactions'));
    }

    private function teknisiDashboard()
    {
        $myActiveRepairs = ServiceRepair::where('technician_id', auth()->id())
            ->whereNotIn('status', ['completed', 'canceled', 'picked_up'])
            ->latest()
            ->get();

        $myCompletedToday = ServiceRepair::where('technician_id', auth()->id())
            ->whereIn('status', ['completed', 'picked_up'])
            ->whereDate('completion_date', today())
            ->count();

        $totalActiveRepairs = ServiceRepair::where('technician_id', auth()->id())
            ->whereNotIn('status', ['completed', 'canceled', 'picked_up'])
            ->count();

        return view('dashboard.teknisi', compact('myActiveRepairs', 'myCompletedToday', 'totalActiveRepairs'));
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : now()->startOfDay();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : now()->endOfDay();

        $totalRevenue = Transaction::where('status', 'completed')->whereBetween('transaction_date', [$startDate, $endDate])->sum('total');
        $transactionCount = Transaction::where('status', 'completed')->whereBetween('transaction_date', [$startDate, $endDate])->count();
        
        $topProducts = TransactionItem::whereHas('transaction', fn($q) => $q->where('status', 'completed')->whereBetween('transaction_date', [$startDate, $endDate]))
            ->join('products', 'transaction_items.product_code', '=', 'products.product_code')
            ->selectRaw('products.name, SUM(transaction_items.quantity) as total_qty, SUM(transaction_items.subtotal) as total_revenue')
            ->groupBy('products.name')->orderByDesc('total_revenue')->limit(5)->get();

        $serviceRevenue = ServiceRepair::whereIn('status', ['completed', 'picked_up'])->whereBetween('completion_date', [$startDate, $endDate])->sum('total_cost');
        $activeRepairs = ServiceRepair::whereNotIn('status', ['completed', 'canceled', 'picked_up'])->count();
        
        $clusteringService = new ProductClusteringService();
        $clusterResults = $clusteringService->cluster($startDate, $endDate);
        
        $smaService = new SmaForecastService();
        $diffDays = (int) $startDate->diffInDays($endDate) ?: 1;
        $smaResults = $smaService->calculateAll($diffDays, $endDate);
        $restockRecommendations = array_filter($smaResults, fn($r) => $r['needs_restock']);

        $productCount = Product::active()->count();

        $salesTrend = Transaction::where('status', 'completed')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw('DATE(transaction_date) as date, SUM(total) as total')
            ->groupBy('date')->orderBy('date')->get();

        $repairTrend = ServiceRepair::whereIn('status', ['completed', 'picked_up'])
            ->whereBetween('completion_date', [$startDate, $endDate])
            ->selectRaw('DATE(completion_date) as date, SUM(total_cost) as total_revenue')
            ->groupBy('date')->orderBy('date')->get();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('dashboard.pdf', compact(
            'totalRevenue', 'transactionCount', 'topProducts',
            'serviceRevenue', 'activeRepairs',
            'clusterResults', 'restockRecommendations', 'smaResults', 'productCount',
            'startDate', 'endDate', 'salesTrend', 'repairTrend'
        ));

        return $pdf->download('dashboard-report-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.pdf');
    }
}
