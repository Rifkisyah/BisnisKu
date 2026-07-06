<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ExportAnalyticsData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'analytics:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export real transaction and product stock data to CSV for K-Means and SMA analytics';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting data export...');

        $exportDir = base_path('analytics/datasets');
        if (!File::exists($exportDir)) {
            File::makeDirectory($exportDir, 0755, true);
        }

        try {
            $this->exportTransactionData($exportDir);
            $this->exportProductStockData($exportDir);
            $this->exportKmeansSummaryData($exportDir);
            
            $this->info('Data export completed successfully.');
        } catch (\Exception $e) {
            $this->error('Failed to export data. Please ensure your database is running.');
            $this->error('Error details: ' . $e->getMessage());
        }
    }

    private function exportTransactionData($exportDir)
    {
        $this->info('Exporting transaction data...');
        
        $transactions = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_code', '=', 'transactions.transaction_code')
            ->join('products', 'transaction_items.product_code', '=', 'products.product_code')
            ->select(
                DB::raw('DATE(transactions.transaction_date) as date'),
                'products.product_code',
                'products.name as product_name',
                'products.category_code as category',
                'products.selling_price as price',
                DB::raw('SUM(transaction_items.quantity) as quantity_sold')
            )
            ->where('transactions.status', 'completed')
            ->groupBy('date', 'products.product_code', 'products.name', 'products.category_code', 'products.selling_price')
            ->orderBy('date', 'asc')
            ->get();

        $path = $exportDir . '/transaction_data.csv';
        $file = fopen($path, 'w');
        
        // Add Header
        fputcsv($file, ['date', 'product_code', 'product_name', 'category', 'price', 'quantity_sold']);
        
        foreach ($transactions as $row) {
            fputcsv($file, [
                $row->date,
                $row->product_code,
                $row->product_name,
                $row->category,
                $row->price,
                $row->quantity_sold
            ]);
        }
        fclose($file);
        
        $this->info("Transaction data exported to: {$path} (" . count($transactions) . " rows)");
    }

    private function exportProductStockData($exportDir)
    {
        $this->info('Exporting product stock data...');
        
        $products = DB::table('products')
            ->leftJoin('categories', 'products.category_code', '=', 'categories.category_code')
            ->select(
                'products.product_code',
                'products.name as product_name',
                'categories.name as category_name',
                'products.stock',
                'products.minimum_stock',
                'products.selling_price'
            )
            ->where('products.status', 'active')
            ->get();

        $path = $exportDir . '/product_stock_data.csv';
        $file = fopen($path, 'w');
        
        // Add Header
        fputcsv($file, ['product_code', 'product_name', 'category', 'current_stock', 'minimum_stock', 'selling_price']);
        
        foreach ($products as $row) {
            fputcsv($file, [
                $row->product_code,
                $row->product_name,
                $row->category_name,
                $row->stock,
                $row->minimum_stock,
                $row->selling_price
            ]);
        }
        fclose($file);
        
        $this->info("Product stock data exported to: {$path} (" . count($products) . " rows)");
    }

    private function exportKmeansSummaryData($exportDir)
    {
        $this->info('Exporting K-Means summary data...');
        
        // Calculate total sales and days with sales per product
        $summary = DB::table('transaction_items')
            ->join('transactions', 'transaction_items.transaction_code', '=', 'transactions.transaction_code')
            ->join('products', 'transaction_items.product_code', '=', 'products.product_code')
            ->select(
                'products.product_code',
                'products.name as product_name',
                'products.category_code as category',
                DB::raw('SUM(transaction_items.quantity) as total_quantity_sold'),
                DB::raw('COUNT(DISTINCT DATE(transactions.transaction_date)) as days_with_sales')
            )
            ->where('transactions.status', 'completed')
            ->groupBy('products.product_code', 'products.name', 'products.category_code')
            ->get();

        // Optional: Calculate overall date range to compute accurate daily averages
        $dateRange = DB::table('transactions')
            ->where('status', 'completed')
            ->select(
                DB::raw('MIN(DATE(transaction_date)) as min_date'),
                DB::raw('MAX(DATE(transaction_date)) as max_date')
            )
            ->first();
            
        $totalDays = 1;
        if ($dateRange && $dateRange->min_date && $dateRange->max_date) {
            $minDate = new \DateTime($dateRange->min_date);
            $maxDate = new \DateTime($dateRange->max_date);
            $totalDays = max(1, $minDate->diff($maxDate)->days + 1);
        }

        $path = $exportDir . '/kmeans_product_summary.csv';
        $file = fopen($path, 'w');
        
        // Add Header
        fputcsv($file, ['product_code', 'product_name', 'category', 'total_quantity_sold', 'average_daily_sales', 'days_with_sales']);
        
        foreach ($summary as $row) {
            $averageDailySales = number_format($row->total_quantity_sold / $totalDays, 2, '.', '');
            fputcsv($file, [
                $row->product_code,
                $row->product_name,
                $row->category,
                $row->total_quantity_sold,
                $averageDailySales,
                $row->days_with_sales
            ]);
        }
        fclose($file);
        
        $this->info("K-Means summary data exported to: {$path} (" . count($summary) . " rows)");
    }
}
