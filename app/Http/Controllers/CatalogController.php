<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\TransactionItem;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::active()
            ->with('category')
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($request->category, fn($q, $c) => $q->where('category_code', $c))
            ->where('type', 'physical')
            ->paginate(12)->withQueryString();

        $categories = Category::orderBy('name')->get();

        $clusteringService = new \App\Services\ProductClusteringService();
        $startDate = now()->startOfDay();
        $endDate = now()->endOfDay();
        $clusterResults = $clusteringService->cluster($startDate, $endDate);
        
        $bestSellerCodes = collect($clusterResults)
            ->where('cluster_label', 'fast_moving')
            ->pluck('product_code')
            ->toArray();

        return view('catalog.index', compact('products', 'categories', 'bestSellerCodes'));
    }
}
