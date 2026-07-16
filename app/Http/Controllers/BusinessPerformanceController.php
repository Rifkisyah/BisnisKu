<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPurchase;
use App\Models\ServiceRepair;
use App\Models\ServiceRepairItem;
use App\Models\StockMovement;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BusinessPerformanceController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────────
    // Main dashboard
    // ─────────────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $startDate = $request->get('start_date')
            ? Carbon::parse($request->get('start_date'))->startOfDay()
            : now()->startOfDay();

        $endDate = $request->get('end_date')
            ? Carbon::parse($request->get('end_date'))->endOfDay()
            : now()->endOfDay();

        // ── Categories for filter ─────────────────────────────────────────────
        $categories = Category::orderBy('name')->get();
        $categoryFilter = $request->get('category');

        // ─────────────────────────────────────────────────────────────────────
        // Section 1 — Top Products & Category Breakdown
        // ─────────────────────────────────────────────────────────────────────

        $topProducts = TransactionItem::whereHas('transaction',
                fn ($q) => $q->where('status', 'paid')->whereBetween('transaction_date', [$startDate, $endDate]))
            ->join('products', 'transaction_items.product_code', '=', 'products.product_code')
            ->when($categoryFilter, fn($q) => $q->where('products.category_code', $categoryFilter))
            ->selectRaw('products.name, products.product_code,
                SUM(transaction_items.quantity) as total_qty,
                SUM(transaction_items.subtotal) as total_revenue')
            ->groupBy('products.name', 'products.product_code')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        $totalProductRev = $topProducts->sum('total_revenue') ?: 1;
        $topProducts->transform(function ($p) use ($totalProductRev) {
            $p->revenue_pct = round(($p->total_revenue / $totalProductRev) * 100, 1);
            return $p;
        });

        // ── Category Breakdown ────────────────────────────────────────────────
        $categoryBreakdown = TransactionItem::whereHas('transaction',
                fn ($q) => $q->where('status', 'paid')->whereBetween('transaction_date', [$startDate, $endDate]))
            ->join('products', 'transaction_items.product_code', '=', 'products.product_code')
            ->join('categories', 'products.category_code', '=', 'categories.category_code')
            ->when($categoryFilter, fn($q) => $q->where('products.category_code', $categoryFilter))
            ->selectRaw('categories.category_code as id, categories.name,
                SUM(transaction_items.subtotal) as total_revenue,
                SUM(transaction_items.quantity) as total_qty')
            ->groupBy('categories.category_code', 'categories.name')
            ->orderByDesc('total_revenue')
            ->get();

        $totalCatRev = $categoryBreakdown->sum('total_revenue') ?: 1;
        $categoryBreakdown->transform(function ($c) use ($totalCatRev) {
            $c->revenue_pct = round(($c->total_revenue / $totalCatRev) * 100, 1);
            return $c;
        });

        // ─────────────────────────────────────────────────────────────────────
        // Section 2 — Inventory Analysis (Dead Stock)
        // ─────────────────────────────────────────────────────────────────────

        $stockIn  = StockMovement::inPeriod($startDate, $endDate)
            ->where('type', 'in')
            ->when($categoryFilter, fn($q) => $q->whereHas('product', fn($pq) => $pq->where('category_code', $categoryFilter)))
            ->sum('total_stock');
        $stockOut = StockMovement::inPeriod($startDate, $endDate)
            ->where('type', 'out')
            ->when($categoryFilter, fn($q) => $q->whereHas('product', fn($pq) => $pq->where('category_code', $categoryFilter)))
            ->sum('total_stock');
        $stockAdj = StockMovement::inPeriod($startDate, $endDate)
            ->where('type', 'adjustment')
            ->when($categoryFilter, fn($q) => $q->whereHas('product', fn($pq) => $pq->where('category_code', $categoryFilter)))
            ->sum('total_stock');

        $avgStock          = Product::active()->physical()->when($categoryFilter, fn($q) => $q->where('category_code', $categoryFilter))->avg('stock') ?: 1;
        $inventoryTurnover = round($stockOut / $avgStock, 2);

        $stockMovementTrend = StockMovement::inPeriod($startDate, $endDate)
            ->when($categoryFilter, fn($q) => $q->whereHas('product', fn($pq) => $pq->where('category_code', $categoryFilter)))
            ->selectRaw('DATE(movement_date) as date, type, SUM(total_stock) as total')
            ->groupByRaw('DATE(movement_date), type')
            ->orderBy('date')
            ->get();

        $deadStockProducts = Product::active()->physical()
            ->when($categoryFilter, fn($q) => $q->where('category_code', $categoryFilter))
            ->whereDoesntHave('transactionItems', function ($q) {
                $q->whereHas('transaction', fn ($tq) =>
                    $tq->where('status', 'paid')
                       ->where('transaction_date', '>=', now()->subDays(90)));
            })
            ->with('category')
            ->get()
            ->map(function ($p) {
                $lastSale = TransactionItem::where('product_code', $p->product_code)
                    ->whereHas('transaction', fn ($q) => $q->where('status', 'paid'))
                    ->join('transactions', 'transaction_items.transaction_code', '=', 'transactions.transaction_code')
                    ->max('transactions.transaction_date');

                $p->last_sale_date  = $lastSale ? Carbon::parse($lastSale)->toDateString() : null;
                $p->days_since_sale = $lastSale ? (int) Carbon::parse($lastSale)->diffInDays(now()) : 999;
                return $p;
            })
            ->sortByDesc('days_since_sale')
            ->values();

        // ─────────────────────────────────────────────────────────────────────
        // Section 3 — Popular Products (frequency + volume based ranking)
        // ─────────────────────────────────────────────────────────────────────

        $clusterResults = Product::active()->physical()
            ->when($categoryFilter, fn($q) => $q->where('products.category_code', $categoryFilter))
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

        // Sort by total_revenue + total_qty_sold to get most popular first
        usort($clusterResults, function ($a, $b) {
            $scoreA = ($a['total_revenue'] ?? 0) * 2 + ($a['total_qty_sold'] ?? 0);
            $scoreB = ($b['total_revenue'] ?? 0) * 2 + ($b['total_qty_sold'] ?? 0);
            return $scoreB <=> $scoreA;
        });

        $totalProductsSold = count(array_filter($clusterResults, fn($r) => ($r['total_qty_sold'] ?? 0) > 0));

        // ─────────────────────────────────────────────────────────────────────
        // Section 4 — Stock Procurement Data
        // ─────────────────────────────────────────────────────────────────────

        $procurements = \App\Models\ProductPurchase::whereBetween('purchase_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->with(['items', 'creator'])
            ->orderByDesc('purchase_date')
            ->get();

        $totalProcurementValue = $procurements->sum('total');
        $receivedProcurements  = $procurements->where('status', 'received')->count();
        $pendingProcurements   = $procurements->whereIn('status', ['draft', 'ordered', 'partial_received'])->count();
        $cancelledProcurements = $procurements->where('status', 'cancelled')->count();

        // Procurement per-date trend for chart
        $procurementTrend = \App\Models\ProductPurchase::whereBetween('purchase_date', [$startDate->toDateString(), $endDate->toDateString()])
            ->selectRaw('DATE(purchase_date) as date, COUNT(*) as count, SUM(total) as total')
            ->groupByRaw('DATE(purchase_date)')
            ->orderBy('date')
            ->get();

        $limit    = $request->input('limit', 10);
        $diffDays = (int) $startDate->diffInDays($endDate);
        if ($diffDays === 0) $diffDays = 1;

        return view('reports.product-stock-turnover.index', compact(
            'startDate', 'endDate', 'categories', 'categoryFilter', 'topProducts', 'categoryBreakdown',
            'stockIn', 'stockOut', 'stockAdj',
            'inventoryTurnover', 'stockMovementTrend', 'deadStockProducts',
            'clusterResults', 'totalProductsSold',
            'procurements', 'totalProcurementValue', 'receivedProcurements',
            'pendingProcurements', 'cancelledProcurements', 'procurementTrend',
            'diffDays', 'limit'
        ));
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Export PDF
    // ─────────────────────────────────────────────────────────────────────────

    public function exportPdf(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date')) : now()->startOfDay();
        $endDate   = $request->get('end_date')   ? Carbon::parse($request->get('end_date'))   : now();
        $data = $this->gatherDataForPdf($startDate, $endDate, $request);
        $pdf  = Pdf::loadView('reports.pdf.product-stock-turnover', $data)->setPaper('a4', 'landscape');
        return $pdf->download('laporan-perputaran-stok-' . now()->format('Y-m-d') . '.pdf');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    public function clusters(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : now()->startOfDay();
        $endDate   = $request->get('end_date')   ? Carbon::parse($request->get('end_date'))->endOfDay()   : now()->endOfDay();
        $categoryFilter = $request->get('category');

        $clusterResults = Product::active()->physical()
            ->when($categoryFilter, fn($q) => $q->where('products.category_code', $categoryFilter))
            ->leftJoin('transaction_items', 'products.product_code', '=', 'transaction_items.product_code')
            ->leftJoin('transactions', function($join) use ($startDate, $endDate) {
                $join->on('transaction_items.transaction_code', '=', 'transactions.transaction_code')
                     ->where('transactions.status', 'paid')
                     ->whereBetween('transactions.transaction_date', [$startDate, $endDate]);
            })
            ->selectRaw('products.product_code, products.name as product_name, 
                         COUNT(DISTINCT transactions.transaction_code) as transaction_frequency, 
                         COALESCE(SUM(transaction_items.quantity), 0) as total_qty_sold, 
                         COALESCE(SUM(transaction_items.subtotal), 0) as total_revenue')
            ->groupBy('products.product_code', 'products.name')
            ->get()
            ->map(function ($item) {
                return [
                    'product_code'          => $item->product_code,
                    'product_name'          => $item->product_name,
                    'transaction_frequency' => (int) $item->transaction_frequency,
                    'total_qty_sold'        => (int) $item->total_qty_sold,
                    'total_revenue'         => (float) $item->total_revenue,
                ];
            })
            ->toArray();

        // Sort by transaction_frequency + total_qty_sold to get most popular first
        usort($clusterResults, function ($a, $b) {
            $scoreA = ($a['transaction_frequency'] ?? 0) * 2 + ($a['total_qty_sold'] ?? 0);
            $scoreB = ($b['transaction_frequency'] ?? 0) * 2 + ($b['total_qty_sold'] ?? 0);
            return $scoreB <=> $scoreA;
        });

        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $perPage = 50;
        $currentItems = array_slice($clusterResults, ($currentPage - 1) * $perPage, $perPage);
        $paginatedClusters = new \Illuminate\Pagination\LengthAwarePaginator($currentItems, count($clusterResults), $perPage, $currentPage, [
            'path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(),
            'query' => $request->query(),
        ]);

        return view('reports.product-stock-turnover.details.clusters', compact('paginatedClusters', 'startDate', 'endDate'));
    }

    public function sma(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : now()->startOfDay();
        $endDate   = $request->get('end_date')   ? Carbon::parse($request->get('end_date'))->endOfDay()   : now()->endOfDay();
        $categoryFilter = $request->get('category');

        $diffDays = (int) $startDate->diffInDays($endDate);
        if ($diffDays === 0) $diffDays = 1;

        $smaService = new SmaForecastService();
        $smaResults = $smaService->calculateAll($diffDays, $endDate, $categoryFilter);

        $statusFilter = $request->get('status_filter', 'all');
        if ($statusFilter !== 'all') {
            $isRestock = $statusFilter === 'restock';
            $smaResults = array_filter($smaResults, fn($item) => $item['needs_restock'] === $isRestock);
        }

        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $perPage = 50;
        $currentItems = array_slice($smaResults, ($currentPage - 1) * $perPage, $perPage);
        $paginatedSma = new \Illuminate\Pagination\LengthAwarePaginator($currentItems, count($smaResults), $perPage, $currentPage, [
            'path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(),
            'query' => $request->query(),
        ]);

        return view('reports.product-stock-turnover.details.sma', compact('paginatedSma', 'startDate', 'endDate', 'diffDays'));
    }



    private function gatherDataForPdf(Carbon $startDate, Carbon $endDate, Request $request): array
    {
        $categoryFilter = $request->get('category');
        
        $productRevenue = Transaction::completed()->inPeriod($startDate, $endDate)->sum('total');
        $serviceRevenue = ServiceRepair::completedInPeriod($startDate, $endDate)->sum('total_cost');
        
        $topProducts    = TransactionItem::whereHas('transaction',
                fn ($q) => $q->where('status', 'paid')->whereBetween('transaction_date', [$startDate, $endDate]))
            ->join('products', 'transaction_items.product_code', '=', 'products.product_code')
            ->when($categoryFilter, fn($q) => $q->where('products.category_code', $categoryFilter))
            ->selectRaw('products.name, SUM(transaction_items.quantity) as total_qty, SUM(transaction_items.subtotal) as total_revenue')
            ->groupBy('products.name')
            ->orderByDesc('total_qty')
            ->limit(10)
            ->get();

        $clusterResults = Product::active()->physical()
            ->when($categoryFilter, fn($q) => $q->where('products.category_code', $categoryFilter))
            ->leftJoin('transaction_items', 'products.product_code', '=', 'transaction_items.product_code')
            ->leftJoin('transactions', function($join) use ($startDate, $endDate) {
                $join->on('transaction_items.transaction_code', '=', 'transactions.transaction_code')
                     ->where('transactions.status', 'paid')
                     ->whereBetween('transactions.transaction_date', [$startDate, $endDate]);
            })
            ->selectRaw('products.product_code, products.name as product_name, 
                         COUNT(DISTINCT transactions.transaction_code) as transaction_frequency, 
                         COALESCE(SUM(transaction_items.quantity), 0) as total_qty_sold, 
                         COALESCE(SUM(transaction_items.subtotal), 0) as total_revenue')
            ->groupBy('products.product_code', 'products.name')
            ->get()
            ->map(function ($item) {
                return [
                    'product_code'          => $item->product_code,
                    'product_name'          => $item->product_name,
                    'transaction_frequency' => (int) $item->transaction_frequency,
                    'total_qty_sold'        => (int) $item->total_qty_sold,
                    'total_revenue'         => (float) $item->total_revenue,
                ];
            })
            ->toArray();

        usort($clusterResults, function ($a, $b) {
            $scoreA = ($a['transaction_frequency'] ?? 0) * 2 + ($a['total_qty_sold'] ?? 0);
            $scoreB = ($b['transaction_frequency'] ?? 0) * 2 + ($b['total_qty_sold'] ?? 0);
            return $scoreB <=> $scoreA;
        });

        return compact('productRevenue', 'serviceRevenue', 'topProducts', 'clusterResults', 'startDate', 'endDate');
    }
}
