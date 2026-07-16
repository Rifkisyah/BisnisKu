<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ServiceRepair;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->isKasir()) {
            return $this->kasirDashboard($request);
        }

        if ($user->isTeknisi()) {
            return $this->teknisiDashboard($request);
        }

        if ($user->role->name === 'gudang') {
            return $this->gudangDashboard($request);
        }

        return $this->ownerDashboard($request);
    }

    private function ownerDashboard(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : now()->startOfDay();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : now()->endOfDay();

        // 1. SALES REVENUE METRICS
        $totalRevenue = Transaction::where('status', 'paid')->whereBetween('transaction_date', [$startDate, $endDate])->sum('total');
        $transactionCount = Transaction::where('status', 'paid')->whereBetween('transaction_date', [$startDate, $endDate])->count();
        
        $salesTrend = Transaction::where('status', 'paid')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw('DATE(transaction_date) as date, SUM(total) as total, COUNT(*) as count')
            ->groupByRaw('DATE(transaction_date)')->orderBy('date')->get();

        $topProducts = TransactionItem::whereHas('transaction', fn($q) => $q->where('status', 'paid')->whereBetween('transaction_date', [$startDate, $endDate]))
            ->join('products', 'transaction_items.product_code', '=', 'products.product_code')
            ->selectRaw('products.name, SUM(transaction_items.quantity) as total_qty, SUM(transaction_items.subtotal) as total_revenue')
            ->groupBy('products.name')->orderByDesc('total_revenue')->limit(10)->get();

        // Payment method breakdown
        $paymentBreakdown = Transaction::where('status', 'paid')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total) as total')
            ->groupBy('payment_method')
            ->orderByDesc('count')
            ->get();

        // 2. SERVICE ANALYSIS METRICS
        $serviceRevenue = ServiceRepair::whereIn('status', ['completed', 'picked_up'])->whereBetween('completion_date', [$startDate, $endDate])->sum('total_cost');
        $activeRepairs = ServiceRepair::whereNotIn('status', ['completed', 'canceled', 'picked_up'])->count();
        
        $repairTrend = ServiceRepair::whereIn('status', ['completed', 'picked_up'])
            ->whereBetween('completion_date', [$startDate, $endDate])
            ->selectRaw('DATE(completion_date) as date, COUNT(*) as ticket_count, SUM(total_cost) as total_revenue')
            ->groupByRaw('DATE(completion_date)')->orderBy('date')->get();

        // Service monthly trend for chart
        $serviceTrend = ServiceRepair::whereIn('status', ['completed', 'picked_up'])
            ->whereBetween('completion_date', [$startDate, $endDate])
            ->selectRaw("DATE_FORMAT(completion_date, '%Y-%m') as month, COUNT(*) as count, SUM(total_cost) as revenue")
            ->groupByRaw("DATE_FORMAT(completion_date, '%Y-%m')")
            ->orderBy('month')
            ->get();

        // Top spareparts used
        $topSpareparts = \App\Models\ServiceRepairItem::whereHas('serviceRepair', fn($q) =>
                $q->whereIn('status', ['completed', 'picked_up'])->whereBetween('completion_date', [$startDate, $endDate]))
            ->whereNotNull('component_code')
            ->join('products', 'service_repair_items.component_code', '=', 'products.product_code')
            ->selectRaw('products.name, service_repair_items.component_code, SUM(service_repair_items.quantity) as total_qty, SUM(service_repair_items.subtotal) as total_cost')
            ->groupBy('products.name', 'service_repair_items.component_code')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // 3. POPULAR PRODUCTS (SQL query based on frequency & qty)
        $clusterResults = Product::active()->physical()
            ->leftJoin('transaction_items', 'products.product_code', '=', 'transaction_items.product_code')
            ->leftJoin('transactions', function($join) use ($startDate, $endDate) {
                $join->on('transaction_items.transaction_code', '=', 'transactions.transaction_code')
                     ->where('transactions.status', 'paid')
                     ->whereBetween('transactions.transaction_date', [$startDate, $endDate]);
            })
            ->selectRaw('products.product_code, products.name as product_name, 
                         COALESCE(SUM(transaction_items.quantity), 0) as total_qty_sold, 
                         COALESCE(SUM(transaction_items.subtotal), 0) as total_revenue')
            ->groupBy('products.product_code', 'products.name')
            ->get()
            ->map(function ($item) {
                return [
                    'product_code'          => $item->product_code,
                    'product_name'          => $item->product_name,
                    'total_qty_sold'        => (int) $item->total_qty_sold,
                    'total_revenue'         => (float) $item->total_revenue,
                ];
            })
            ->toArray();

        // Sort by total_revenue + total_qty_sold
        usort($clusterResults, function ($a, $b) {
            $scoreA = ($a['total_revenue'] ?? 0) * 2 + ($a['total_qty_sold'] ?? 0);
            $scoreB = ($b['total_revenue'] ?? 0) * 2 + ($b['total_qty_sold'] ?? 0);
            return $scoreB <=> $scoreA;
        });

        // 4. STOCK PROCUREMENT DATA
        $procurements = \App\Models\ProductPurchase::whereBetween('purchase_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->with(['items', 'creator'])
            ->orderByDesc('purchase_date')
            ->get();

        $totalProcurementValue = $procurements->sum('total');
        $receivedProcurements  = $procurements->where('status', 'received')->count();
        $pendingProcurements   = $procurements->whereIn('status', ['draft', 'ordered', 'partial_received'])->count();

        $productCount = Product::active()->count();

        $smaService = new \App\Services\SmaForecastService();
        $diffDays = (int) $startDate->diffInDays($endDate);
        if ($diffDays === 0) $diffDays = 1;
        $smaResults = $smaService->calculateAll($diffDays, $endDate, null);

        return view('dashboard.owner', compact(
            'totalRevenue', 'transactionCount', 'salesTrend', 'topProducts', 'paymentBreakdown',
            'serviceRevenue', 'activeRepairs', 'repairTrend', 'serviceTrend', 'topSpareparts',
            'clusterResults', 'smaResults',
            'procurements', 'totalProcurementValue', 'receivedProcurements', 'pendingProcurements',
            'productCount', 'startDate', 'endDate'
        ));
    }

    private function kasirDashboard(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : now()->startOfDay();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : now()->endOfDay();

        $filteredTransactions = Transaction::where('cashier_id', auth()->id())
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->count();

        $filteredRevenue = Transaction::where('cashier_id', auth()->id())
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->where('status', 'paid')
            ->sum('total');

        $recentTransactions = Transaction::where('cashier_id', auth()->id())
            ->where('status', 'paid')
            ->latest('transaction_date')
            ->limit(5)
            ->get();
            
        $productCount = \App\Models\Product::active()->count();

        // Metrik Hutang
        $unpaidDebts = \App\Models\Debt::where('status', '!=', 'paid')
            ->whereBetween('debt_date', [$startDate, $endDate])
            ->count();
            
        $totalUnpaidDebtAmount = \App\Models\Debt::where('status', '!=', 'paid')
            ->whereBetween('debt_date', [$startDate, $endDate])
            ->sum('remaining_amount');

        // Metrik Layanan Perbaikan
        $activeRepairs = \App\Models\ServiceRepair::whereNotIn('status', ['completed', 'canceled', 'picked_up'])
            ->whereBetween('start_date', [$startDate, $endDate])
            ->count();

        return view('dashboard.kasir', compact(
            'filteredTransactions', 'filteredRevenue', 'recentTransactions', 'productCount',
            'unpaidDebts', 'totalUnpaidDebtAmount', 'activeRepairs', 'startDate', 'endDate'
        ));
    }

        private function teknisiDashboard(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : now()->startOfDay();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : now()->endOfDay();

        $activeRepairs = ServiceRepair::where('technician_id', auth()->id())
            ->whereNotIn('status', ['completed', 'canceled', 'picked_up'])
            ->count();

        $completedRepairs = ServiceRepair::where('technician_id', auth()->id())
            ->whereIn('status', ['completed', 'picked_up'])
            ->whereBetween('completion_date', [$startDate, $endDate])
            ->count();

        $totalRevenue = ServiceRepair::where('technician_id', auth()->id())
            ->whereIn('status', ['completed', 'picked_up'])
            ->whereBetween('completion_date', [$startDate, $endDate])
            ->sum('total_cost');

        $recentRepairs = ServiceRepair::where('technician_id', auth()->id())
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest('created_at')
            ->limit(10)
            ->get();

        return view('dashboard.teknisi', compact('activeRepairs', 'completedRepairs', 'totalRevenue', 'recentRepairs', 'startDate', 'endDate'));
    }

        private function gudangDashboard(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : now()->startOfDay();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : now()->endOfDay();

        $totalProducts = \App\Models\Product::count();
        $lowStockProducts = \App\Models\Product::where('stock', '<=', 5)->orderBy('stock', 'asc')->limit(10)->get();
        
        $activeProcurements = \App\Models\ProductPurchase::whereNotIn('status', ['received', 'cancelled'])
            ->whereBetween('created_at', [$startDate, $endDate])->count();
        $recentProcurements = \App\Models\ProductPurchase::whereBetween('created_at', [$startDate, $endDate])
            ->latest()->limit(10)->get();
        
        $totalSuppliers = \App\Models\Supplier::count();

        return view('dashboard.gudang', compact(
            'totalProducts', 'lowStockProducts', 'activeProcurements', 'recentProcurements', 'totalSuppliers',
            'startDate', 'endDate'
        ));
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : now()->startOfDay();
        $endDate = $request->get('end_date') ? Carbon::parse($request->get('end_date'))->endOfDay() : now()->endOfDay();

        $totalRevenue = Transaction::where('status', 'paid')->whereBetween('transaction_date', [$startDate, $endDate])->sum('total');
        $transactionCount = Transaction::where('status', 'paid')->whereBetween('transaction_date', [$startDate, $endDate])->count();
        
        $topProducts = TransactionItem::whereHas('transaction', fn($q) => $q->where('status', 'paid')->whereBetween('transaction_date', [$startDate, $endDate]))
            ->join('products', 'transaction_items.product_code', '=', 'products.product_code')
            ->selectRaw('products.name, SUM(transaction_items.quantity) as total_qty, SUM(transaction_items.subtotal) as total_revenue')
            ->groupBy('products.name')->orderByDesc('total_revenue')->limit(10)->get();

        // Payment method breakdown
        $paymentBreakdown = Transaction::where('status', 'paid')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total) as total')
            ->groupBy('payment_method')
            ->orderByDesc('count')
            ->get();

        $serviceRevenue = ServiceRepair::whereIn('status', ['completed', 'picked_up'])->whereBetween('completion_date', [$startDate, $endDate])->sum('total_cost');
        $activeRepairs = ServiceRepair::whereNotIn('status', ['completed', 'canceled', 'picked_up'])->count();
        
        // Service monthly trend
        $serviceTrend = ServiceRepair::whereIn('status', ['completed', 'picked_up'])
            ->whereBetween('completion_date', [$startDate, $endDate])
            ->selectRaw("DATE_FORMAT(completion_date, '%Y-%m') as month, COUNT(*) as count, SUM(total_cost) as revenue")
            ->groupByRaw("DATE_FORMAT(completion_date, '%Y-%m')")
            ->orderBy('month')
            ->get();

        // Top spareparts used
        $topSpareparts = \App\Models\ServiceRepairItem::whereHas('serviceRepair', fn($q) =>
                $q->whereIn('status', ['completed', 'picked_up'])->whereBetween('completion_date', [$startDate, $endDate]))
            ->whereNotNull('component_code')
            ->join('products', 'service_repair_items.component_code', '=', 'products.product_code')
            ->selectRaw('products.name, service_repair_items.component_code, SUM(service_repair_items.quantity) as total_qty, SUM(service_repair_items.subtotal) as total_cost')
            ->groupBy('products.name', 'service_repair_items.component_code')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // 3. POPULAR PRODUCTS
        $clusterResults = Product::active()->physical()
            ->leftJoin('transaction_items', 'products.product_code', '=', 'transaction_items.product_code')
            ->leftJoin('transactions', function($join) use ($startDate, $endDate) {
                $join->on('transaction_items.transaction_code', '=', 'transactions.transaction_code')
                     ->where('transactions.status', 'paid')
                     ->whereBetween('transactions.transaction_date', [$startDate, $endDate]);
            })
            ->selectRaw('products.product_code, products.name as product_name, 
                         COALESCE(SUM(transaction_items.quantity), 0) as total_qty_sold, 
                         COALESCE(SUM(transaction_items.subtotal), 0) as total_revenue')
            ->groupBy('products.product_code', 'products.name')
            ->get()
            ->map(function ($item) {
                return [
                    'product_code'          => $item->product_code,
                    'product_name'          => $item->product_name,
                    'total_qty_sold'        => (int) $item->total_qty_sold,
                    'total_revenue'         => (float) $item->total_revenue,
                ];
            })
            ->toArray();

        // Sort by popularity score
        usort($clusterResults, function ($a, $b) {
            $scoreA = ($a['total_revenue'] ?? 0) * 2 + ($a['total_qty_sold'] ?? 0);
            $scoreB = ($b['total_revenue'] ?? 0) * 2 + ($b['total_qty_sold'] ?? 0);
            return $scoreB <=> $scoreA;
        });

        // 4. STOCK PROCUREMENT DATA
        $procurements = \App\Models\ProductPurchase::whereBetween('purchase_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->with(['items', 'creator'])
            ->orderByDesc('purchase_date')
            ->get();

        $totalProcurementValue = $procurements->sum('total');
        $receivedProcurements  = $procurements->where('status', 'received')->count();
        $pendingProcurements   = $procurements->whereIn('status', ['draft', 'ordered', 'partial_received'])->count();

        $productCount = Product::active()->count();

        $salesTrend = Transaction::where('status', 'paid')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->selectRaw('DATE(transaction_date) as date, SUM(total) as total')
            ->groupBy('date')->orderBy('date')->get();

        $repairTrend = ServiceRepair::whereIn('status', ['completed', 'picked_up'])
            ->whereBetween('completion_date', [$startDate, $endDate])
            ->selectRaw('DATE(completion_date) as date, SUM(total_cost) as total_revenue')
            ->groupBy('date')->orderBy('date')->get();

        $smaService = new \App\Services\SmaForecastService();
        $diffDays = (int) $startDate->diffInDays($endDate);
        if ($diffDays === 0) $diffDays = 1;
        $smaResults = $smaService->calculateAll($diffDays, $endDate, null);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('dashboard.pdf', compact(
            'totalRevenue', 'transactionCount', 'salesTrend', 'topProducts', 'paymentBreakdown',
            'serviceRevenue', 'activeRepairs', 'repairTrend', 'serviceTrend', 'topSpareparts',
            'clusterResults', 'smaResults',
            'procurements', 'totalProcurementValue', 'receivedProcurements', 'pendingProcurements',
            'productCount', 'startDate', 'endDate'
        ));

        return $pdf->download('dashboard-report-' . $startDate->format('Y-m-d') . '-to-' . $endDate->format('Y-m-d') . '.pdf');
    }
}
