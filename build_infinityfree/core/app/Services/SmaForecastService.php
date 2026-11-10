<?php

namespace App\Services;

use App\Models\Product;
use App\Models\TransactionItem;
use Carbon\Carbon;

class SmaForecastService
{
    /**
     * Calculate Simple Moving Average (daily) for a product.
     * Uses the last $days days of daily sales data.
     *
     * @param string $productCode
     * @param int    $days  Number of days to consider (default 30)
     * @param Carbon|null $endDate
     * @return array
     */
    public function calculate(string $productCode, int $days = 30, ?Carbon $endDate = null): array
    {
        $endDate   = $endDate ?? Carbon::now();
        
        // Prevent querying future dates for historical average
        $queryEndDate = $endDate->isFuture() ? Carbon::now() : $endDate;
        $startDate = $queryEndDate->copy()->subDays($days - 1)->startOfDay();

        $product = Product::findOrFail($productCode);

        // Get daily sales data within the period
        $dailySales = TransactionItem::where('product_code', $productCode)
            ->whereHas('transaction', function ($q) use ($startDate, $queryEndDate) {
                $q->where('status', 'completed')
                  ->whereBetween('transaction_date', [$startDate, $queryEndDate]);
            })
            ->selectRaw('DATE(created_at) as sale_date, SUM(quantity) as total_qty')
            ->groupByRaw('DATE(created_at)')
            ->orderByRaw('DATE(created_at)')
            ->get();

        // Build a full date-keyed array (0 for days with no sales)
        $salesByDate = $dailySales->pluck('total_qty', 'sale_date')->toArray();

        // Fill all days in the range (including zeros)
        $allDays   = [];
        $cursor    = $startDate->copy();
        while ($cursor->lte($endDate)) {
            $dateKey   = $cursor->toDateString();
            $allDays[] = (float) ($salesByDate[$dateKey] ?? 0);
            $cursor->addDay();
        }

        $actualDays  = count($allDays) ?: 1;
        $totalSales  = array_sum($allDays);

        // SMA = total penjualan / jumlah hari periode
        $smaDaily    = $totalSales / $actualDays;

        // Predicted demand for next $days days
        $demandNextPeriod   = (int) ceil($smaDaily * $days);
        
        // Current stock
        $currentStock = $product->stock;

        // Days of stock remaining (how many days current stock will last)
        $daysOfStockRemaining = $smaDaily > 0
            ? (int) floor($currentStock / $smaDaily)
            : 999;

        // Restock needed = how much to order to cover $days days
        $restockRecommendation = (int) max(0, $demandNextPeriod - $currentStock);
        $needsRestock          = $currentStock < $demandNextPeriod || $currentStock <= $product->minimum_stock;

        // If it needs restock because of minimum stock but recommendation is 0, suggest at least enough to exceed min stock
        if ($needsRestock && $restockRecommendation == 0) {
            $restockRecommendation = max(1, ($product->minimum_stock - $currentStock) + 1);
        }

        // Human-readable reason
        if ($smaDaily <= 0) {
            $reason = 'Tidak ada penjualan dalam ' . $days . ' hari terakhir';
        } elseif ($needsRestock) {
            $reason = 'Stok saat ini hanya cukup untuk ' . $daysOfStockRemaining . ' hari (kebutuhan ' . $days . ' hari: ' . $demandNextPeriod . ' unit)';
        } else {
            $reason = 'Stok tersedia untuk sekitar ' . $daysOfStockRemaining . ' hari ke depan';
        }

        return [
            'product_code'             => $productCode,
            'product_name'             => $product->name,
            'current_stock'            => $currentStock,
            'minimum_stock'            => $product->minimum_stock,
            'period_days'              => $days,
            'daily_sales_data'         => $allDays,
            'total_sales'              => $totalSales,
            'sma_daily'                => round($smaDaily, 2),
            'sma_value'                => round($smaDaily, 2),   // alias for backward-compat
            'predicted_demand_14d'     => $demandNextPeriod,     // keep key for backwards compat but it's now X days
            'predicted_demand_period'  => $demandNextPeriod,
            'predicted_demand'         => $demandNextPeriod,     // alias
            'days_of_stock_remaining'  => $daysOfStockRemaining,
            'restock_recommendation'   => $restockRecommendation,
            'needs_restock'            => $needsRestock,
            'reason'                   => $reason,
        ];
    }

    /**
     * Calculate SMA for all active products.
     *
     * @param int  $days
     * @param Carbon|null $endDate
     * @return array
     */
    public function calculateAll(int $days = 30, ?Carbon $endDate = null, ?string $categoryCode = null): array
    {
        $query = Product::active()->physical();
        if ($categoryCode) {
            $query->where('category_code', $categoryCode);
        }
        $products = $query->get();
        $results  = [];

        foreach ($products as $product) {
            $results[] = $this->calculate($product->product_code, $days, $endDate);
        }

        // Sort by urgency: needs restock first, then by days remaining asc
        usort($results, function ($a, $b) {
            if ($a['needs_restock'] !== $b['needs_restock']) {
                return $b['needs_restock'] <=> $a['needs_restock'];
            }
            return $a['days_of_stock_remaining'] <=> $b['days_of_stock_remaining'];
        });

        return $results;
    }
}
