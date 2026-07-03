<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductAnalytic;
use App\Models\TransactionItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductClusteringService
{
    private int $k            = 3;   // K-Means clusters (fast/medium/slow)
    private int $maxIterations = 100;
    private int $deadStockDays = 90; // Days without sale → dead stock

    // Recommendations per cluster label
    private array $recommendations = [
        'fast_moving'   => 'Pertahankan stok, prioritaskan reorder segera',
        'medium_moving' => 'Monitor tren, restock sesuai estimasi SMA',
        'slow_moving'   => 'Kurangi frekuensi pembelian, pertimbangkan promosi',
        'dead_stock'    => 'Tidak terjual 90+ hari — pertimbangkan diskon clearance atau retur ke supplier',
        'new_product'   => 'Produk baru, belum ada riwayat penjualan. Lakukan promosi awal.',
    ];

    /**
     * Run K-Means clustering on products.
     * Features: total_qty_sold, transaction_frequency, days_without_sale
     * Data normalized (min-max) before clustering.
     * Dead stock products are identified BEFORE clustering and excluded from K-Means.
     */
    public function cluster(?Carbon $startDate = null, ?Carbon $endDate = null, ?string $categoryCode = null): array
    {
        $endDate   = $endDate ?? Carbon::now();
        $startDate = $startDate ?? $endDate->copy()->subMonths(3);

        $query = Product::active()->physical();
        if ($categoryCode) {
            $query->where('category_code', $categoryCode);
        }
        $products = $query->get();
        $data     = [];

        foreach ($products as $product) {
            $salesQuery = TransactionItem::where('product_code', $product->product_code)
                ->whereHas('transaction', function ($q) use ($startDate, $endDate) {
                    $q->where('status', 'completed')
                      ->whereBetween('transaction_date', [$startDate, $endDate]);
                });

            $totalQtySold = (clone $salesQuery)->sum('quantity');

            $transactionFrequency = (clone $salesQuery)
                ->distinct('transaction_code')
                ->count('transaction_code');

            // Last sale date to compute days_without_sale
            $lastSale = TransactionItem::where('product_code', $product->product_code)
                ->whereHas('transaction', fn ($q) => $q->where('status', 'completed'))
                ->join('transactions', 'transaction_items.transaction_code', '=', 'transactions.transaction_code')
                ->max('transactions.transaction_date');

            $daysWithoutSale = $lastSale
                ? (int) Carbon::parse($lastSale)->diffInDays($endDate)
                : (int) $product->created_at->diffInDays($endDate);

            $data[] = [
                'product_code'          => $product->product_code,
                'product_name'          => $product->name,
                'total_qty_sold'        => (int) $totalQtySold,
                'transaction_frequency' => (int) $transactionFrequency,
                'remaining_stock'       => $product->stock,
                'days_without_sale'     => $daysWithoutSale,
                'last_sale_date'        => $lastSale ? Carbon::parse($lastSale)->toDateString() : null,
            ];
        }

        if (empty($data)) {
            return [];
        }

        // ─── Separate dead stock (not sold for >= deadStockDays) ──────────────
        $deadStock = array_filter($data, fn ($item) =>
            $item['days_without_sale'] >= $this->deadStockDays && $item['total_qty_sold'] === 0
        );
        $newProduct = array_filter($data, fn ($item) =>
            $item['days_without_sale'] < $this->deadStockDays && $item['total_qty_sold'] === 0
        );
        $activeData = array_values(array_filter($data, fn ($item) =>
            $item['total_qty_sold'] > 0
        ));

        // Assign dead_stock label
        $deadStockLabeled = array_map(fn ($item) => array_merge($item, [
            'cluster_label'   => 'dead_stock',
            'cluster_id'      => 3,
            'recommendation'  => $this->recommendations['dead_stock'],
        ]), $deadStock);
        
        // Assign new_product label
        $newProductLabeled = array_map(fn ($item) => array_merge($item, [
            'cluster_label'   => 'new_product',
            'cluster_id'      => 4,
            'recommendation'  => $this->recommendations['new_product'],
        ]), $newProduct);

        // ─── K-Means on remaining active products ────────────────────────────
        if (count($activeData) < $this->k) {
            $labeled = $this->assignDefaultCluster($activeData);
        } else {
            // Features: [qty_sold, frequency, days_without_sale]
            $features   = $this->extractFeatures($activeData);
            $normalized = $this->normalize($features);
            $clusters   = $this->kMeans($normalized);
            $labels     = $this->assignClusterLabels($clusters['centroids']);

            $labeled = [];
            foreach ($activeData as $i => $item) {
                $clusterId = $clusters['assignments'][$i];
                $label     = $labels[$clusterId];
                $labeled[] = array_merge($item, [
                    'cluster_label'  => $label,
                    'cluster_id'     => $clusterId,
                    'recommendation' => $this->recommendations[$label],
                ]);
            }
        }

        // Merge results: active first, dead stock appended, then new products
        $results = array_merge($labeled, array_values($deadStockLabeled), array_values($newProductLabeled));

        // Save to database
        $this->saveResults($results, $endDate);

        return $results;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Private helpers
    // ─────────────────────────────────────────────────────────────────────────

    private function extractFeatures(array $data): array
    {
        // Features: qty_sold, frequency, days_without_sale (inverted: lower = better)
        return array_map(fn ($item) => [
            $item['total_qty_sold'],
            $item['transaction_frequency'],
            $item['days_without_sale'],
        ], $data);
    }

    private function normalize(array $features): array
    {
        if (empty($features)) return [];

        $numFeatures = count($features[0]);
        $mins        = array_fill(0, $numFeatures, PHP_FLOAT_MAX);
        $maxs        = array_fill(0, $numFeatures, -PHP_FLOAT_MAX);

        foreach ($features as $point) {
            for ($j = 0; $j < $numFeatures; $j++) {
                $mins[$j] = min($mins[$j], $point[$j]);
                $maxs[$j] = max($maxs[$j], $point[$j]);
            }
        }

        $normalized = [];
        foreach ($features as $point) {
            $norm = [];
            for ($j = 0; $j < $numFeatures; $j++) {
                $range  = $maxs[$j] - $mins[$j];
                $norm[] = $range > 0 ? ($point[$j] - $mins[$j]) / $range : 0;
            }
            $normalized[] = $norm;
        }

        return $normalized;
    }

    private function kMeans(array $data): array
    {
        $n           = count($data);
        $numFeatures = count($data[0]);
        $centroids   = $this->initializeCentroids($data);
        $assignments = array_fill(0, $n, 0);

        for ($iter = 0; $iter < $this->maxIterations; $iter++) {
            $newAssignments = [];
            foreach ($data as $i => $point) {
                $minDist = PHP_FLOAT_MAX;
                $closest = 0;
                foreach ($centroids as $c => $centroid) {
                    $dist = $this->euclideanDistance($point, $centroid);
                    if ($dist < $minDist) { $minDist = $dist; $closest = $c; }
                }
                $newAssignments[$i] = $closest;
            }

            if ($newAssignments === $assignments) break;
            $assignments = $newAssignments;

            for ($c = 0; $c < $this->k; $c++) {
                $clusterPoints = [];
                foreach ($assignments as $i => $cluster) {
                    if ($cluster === $c) $clusterPoints[] = $data[$i];
                }
                if (!empty($clusterPoints)) {
                    $centroids[$c] = $this->calculateCentroid($clusterPoints, $numFeatures);
                }
            }
        }

        return ['centroids' => $centroids, 'assignments' => $assignments];
    }

    private function initializeCentroids(array $data): array
    {
        $centroids   = [$data[array_rand($data)]];

        for ($c = 1; $c < $this->k; $c++) {
            $distances = [];
            foreach ($data as $point) {
                $minDist = PHP_FLOAT_MAX;
                foreach ($centroids as $centroid) {
                    $minDist = min($minDist, $this->euclideanDistance($point, $centroid));
                }
                $distances[] = $minDist * $minDist;
            }

            $totalDist = array_sum($distances);
            if ($totalDist <= 0) { $centroids[] = $data[array_rand($data)]; continue; }

            $rand   = mt_rand() / mt_getrandmax() * $totalDist;
            $cumSum = 0;
            foreach ($distances as $i => $d) {
                $cumSum += $d;
                if ($cumSum >= $rand) { $centroids[] = $data[$i]; break; }
            }
        }

        return $centroids;
    }

    private function euclideanDistance(array $a, array $b): float
    {
        $sum = 0;
        for ($i = 0; $i < count($a); $i++) $sum += ($a[$i] - $b[$i]) ** 2;
        return sqrt($sum);
    }

    private function calculateCentroid(array $points, int $numFeatures): array
    {
        $centroid = array_fill(0, $numFeatures, 0);
        foreach ($points as $point) {
            for ($j = 0; $j < $numFeatures; $j++) $centroid[$j] += $point[$j];
        }
        return array_map(fn ($v) => $v / count($points), $centroid);
    }

    /**
     * Assign cluster labels based on centroids.
     * Features[0] = qty_sold (↑ better), Features[1] = frequency (↑ better),
     * Features[2] = days_without_sale (↑ worse, so we invert).
     */
    private function assignClusterLabels(array $centroids): array
    {
        $scores = [];
        foreach ($centroids as $i => $c) {
            // Higher qty + frequency, lower days_without_sale → fast moving
            $scores[$i] = $c[0] + $c[1] - $c[2];
        }

        arsort($scores);
        $keys = array_keys($scores);

        return [
            $keys[0] => 'fast_moving',
            $keys[1] => 'medium_moving',
            $keys[2] => 'slow_moving',
        ];
    }

    private function assignDefaultCluster(array $data): array
    {
        return array_map(fn ($item) => array_merge($item, [
            'cluster_label'  => 'medium_moving',
            'cluster_id'     => 1,
            'recommendation' => $this->recommendations['medium_moving'],
        ]), $data);
    }

    private function saveResults(array $results, Carbon $analysisDate): void
    {
        DB::transaction(function () use ($results, $analysisDate) {
            foreach ($results as $r) {
                ProductAnalytic::updateOrCreate(
                    [
                        'product_code'  => $r['product_code'],
                        'analysis_date' => $analysisDate->toDateString(),
                    ],
                    [
                        'total_qty_sold'        => $r['total_qty_sold'],
                        'transaction_frequency' => $r['transaction_frequency'],
                        'remaining_stock'       => $r['remaining_stock'],
                        'cluster_label'         => $r['cluster_label'],
                    ]
                );
            }
        });
    }
}
