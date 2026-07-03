<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ServiceRepair;
use App\Models\ServiceRepairItem;
use App\Models\StockMovement;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Services\ProductClusteringService;
use App\Services\SmaForecastService;
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
        // Section 3 — K-Means Clustering
        // ─────────────────────────────────────────────────────────────────────

        $clusteringService = new ProductClusteringService();
        $clusterResults    = $clusteringService->cluster($startDate, $endDate, $categoryFilter);

        $clusterSummary = ['fast_moving' => 0, 'medium_moving' => 0, 'slow_moving' => 0, 'dead_stock' => 0];
        foreach ($clusterResults as $r) {
            $clusterSummary[$r['cluster_label']] = ($clusterSummary[$r['cluster_label']] ?? 0) + 1;
        }

        // ─────────────────────────────────────────────────────────────────────
        // Section 4 — SMA Restock Recommendations
        // ─────────────────────────────────────────────────────────────────────

        $diffDays = (int) $startDate->diffInDays($endDate);
        if ($diffDays === 0) $diffDays = 1;

        $smaService    = new SmaForecastService();
        $smaResults    = $smaService->calculateAll($diffDays, $endDate, $categoryFilter);
        $restockNeeded = collect($smaResults)->where('needs_restock', true)->count();
        $safeStock     = collect($smaResults)->where('needs_restock', false)->count();

        $limit = $request->input('limit', 10);

        return view('reports.business-performance.index', compact(
            'startDate', 'endDate', 'categories', 'categoryFilter',
            'stockIn', 'stockOut', 'stockAdj',
            'inventoryTurnover', 'stockMovementTrend', 'deadStockProducts',
            'clusterResults', 'clusterSummary',
            'smaResults', 'restockNeeded', 'safeStock', 'diffDays', 'limit'
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
        $pdf  = Pdf::loadView('reports.pdf.business-performance', $data)->setPaper('a4', 'landscape');
        return $pdf->download('bi-report-' . now()->format('Y-m-d') . '.pdf');
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────────────

    public function clusters(Request $request)
    {
        $startDate = $request->get('start_date') ? Carbon::parse($request->get('start_date'))->startOfDay() : now()->startOfDay();
        $endDate   = $request->get('end_date')   ? Carbon::parse($request->get('end_date'))->endOfDay()   : now()->endOfDay();
        $categoryFilter = $request->get('category');

        $clusteringService = new ProductClusteringService();
        $clusterResults    = $clusteringService->cluster($startDate, $endDate, $categoryFilter);

        $clusterFilter = $request->get('cluster_filter', 'all');
        if ($clusterFilter !== 'all') {
            $clusterResults = array_filter($clusterResults, fn($item) => $item['cluster_label'] === $clusterFilter);
        }

        $currentPage = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage();
        $perPage = 50;
        $currentItems = array_slice($clusterResults, ($currentPage - 1) * $perPage, $perPage);
        $paginatedClusters = new \Illuminate\Pagination\LengthAwarePaginator($currentItems, count($clusterResults), $perPage, $currentPage, [
            'path' => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(),
            'query' => $request->query(),
        ]);

        return view('reports.business-performance.details.clusters', compact('paginatedClusters', 'startDate', 'endDate'));
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

        return view('reports.business-performance.details.sma', compact('paginatedSma', 'startDate', 'endDate', 'diffDays'));
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

        $clusteringService = new ProductClusteringService();
        $clusterResults    = $clusteringService->cluster($startDate, $endDate, $categoryFilter);

        return compact('productRevenue', 'serviceRevenue', 'topProducts', 'clusterResults', 'startDate', 'endDate');
    }
}
