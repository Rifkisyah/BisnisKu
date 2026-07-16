<?php

namespace App\Services;

use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class KMeansService
{
    /**
     * Perform K-Means clustering on products based on total revenue and quantity sold.
     * 
     * @param int $k Number of clusters (default 3: High, Medium, Low)
     * @param int $maxIterations
     * @return array
     */
    public function clusterProducts($startDate, $endDate, $k = 3, $maxIterations = 100)
    {
        // 1. Fetch raw data
        $data = Product::active()->physical()
            ->leftJoin('transaction_items', 'products.product_code', '=', 'transaction_items.product_code')
            ->leftJoin('transactions', function($join) use ($startDate, $endDate) {
                $join->on('transaction_items.transaction_code', '=', 'transactions.transaction_code')
                     ->where('transactions.status', 'paid')
                     ->whereBetween('transactions.transaction_date', [$startDate, $endDate]);
            })
            ->selectRaw('
                products.product_code, 
                products.name as product_name, 
                COALESCE(SUM(transaction_items.subtotal), 0) as total_revenue, 
                COALESCE(SUM(transaction_items.quantity), 0) as quantity
            ')
            ->groupBy('products.product_code', 'products.name')
            ->get()
            ->toArray();

        if (empty($data)) {
            return [];
        }

        // Normalize data for better clustering (revenue can be very large compared to qty)
        $maxRevenue = max(array_column($data, 'total_revenue')) ?: 1;
        $maxQty = max(array_column($data, 'quantity')) ?: 1;

        // 2. Initialize centroids randomly from data points
        $centroids = [];
        $dataKeys = array_rand($data, min($k, count($data)));
        $dataKeys = is_array($dataKeys) ? $dataKeys : [$dataKeys];
        
        foreach ($dataKeys as $key) {
            $centroids[] = [
                'total_revenue' => (float)$data[$key]['total_revenue'] / $maxRevenue,
                'quantity' => (float)$data[$key]['quantity'] / $maxQty
            ];
        }

        // 3. K-Means Iteration
        $clusters = [];
        $hasChanged = true;
        $iterations = 0;

        while ($hasChanged && $iterations < $maxIterations) {
            $hasChanged = false;
            // Initialize empty clusters
            $clusters = array_fill(0, $k, []);

            // Assign points to nearest centroid
            foreach ($data as &$item) {
                $normRev = (float)$item['total_revenue'] / $maxRevenue;
                $normQty = (float)$item['quantity'] / $maxQty;

                $minDistance = PHP_FLOAT_MAX;
                $closestCentroidIndex = 0;

                foreach ($centroids as $index => $centroid) {
                    // Euclidean distance using normalized values
                    $dist = sqrt(
                        pow($normRev - $centroid['total_revenue'], 2) +
                        pow($normQty - $centroid['quantity'], 2)
                    );

                    if ($dist < $minDistance) {
                        $minDistance = $dist;
                        $closestCentroidIndex = $index;
                    }
                }
                
                // Track previous cluster for change detection
                $prevCluster = $item['cluster_index'] ?? -1;
                if ($prevCluster !== $closestCentroidIndex) {
                    $hasChanged = true;
                }
                
                $item['cluster_index'] = $closestCentroidIndex;
                $item['distance'] = $minDistance;
                $clusters[$closestCentroidIndex][] = $item;
            }
            unset($item);

            // Update centroids
            foreach ($clusters as $index => $clusterItems) {
                if (count($clusterItems) > 0) {
                    $sumRev = 0;
                    $sumQty = 0;
                    foreach ($clusterItems as $item) {
                        $sumRev += ((float)$item['total_revenue'] / $maxRevenue);
                        $sumQty += ((float)$item['quantity'] / $maxQty);
                    }
                    $centroids[$index]['total_revenue'] = $sumRev / count($clusterItems);
                    $centroids[$index]['quantity'] = $sumQty / count($clusterItems);
                }
            }

            $iterations++;
        }

        // 4. Sort clusters to determine High, Medium, Low
        $clusterScores = [];
        foreach ($centroids as $index => $centroid) {
            $clusterScores[$index] = sqrt(pow($centroid['total_revenue'], 2) + pow($centroid['quantity'], 2));
        }

        arsort($clusterScores);
        $sortedClusterIndices = array_keys($clusterScores);
        
        // Map sorted indices to labels: 0 -> High, 1 -> Medium, 2 -> Low (assuming k=3)
        $labels = ['High', 'Medium', 'Low'];
        $result = [];

        foreach ($sortedClusterIndices as $rank => $originalIndex) {
            $label = $labels[$rank] ?? 'Cluster ' . $rank;
            foreach ($clusters[$originalIndex] as $item) {
                $item['cluster_label'] = $label;
                $result[] = $item;
            }
        }

        return $result;
    }

    /**
     * Get top seller product codes based on K-Means clustering ("High" cluster).
     */
    public function getTopSellerCodes($startDate, $endDate, $limit = 4)
    {
        $clusteredData = $this->clusterProducts($startDate, $endDate);
        
        // Filter out "High" cluster items
        $highCluster = array_filter($clusteredData, function($item) {
            return $item['cluster_label'] === 'High' && $item['quantity'] > 0;
        });

        // Sort them by total revenue and quantity descending
        usort($highCluster, function($a, $b) {
            $scoreA = $a['total_revenue'] * 2 + $a['quantity'];
            $scoreB = $b['total_revenue'] * 2 + $b['quantity'];
            return $scoreB <=> $scoreA;
        });

        // Extract product codes
        $topCodes = array_map(function($item) {
            return $item['product_code'];
        }, array_slice($highCluster, 0, $limit));

        return $topCodes;
    }
}

