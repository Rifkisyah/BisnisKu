<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Store;
use App\Models\TransactionItem;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    /**
     * Get the currently resolved store from the service container.
     * The store is set by SetPublicStoreTenant middleware.
     */
    private function currentStore(): Store
    {
        return app('current_store');
    }



    /**
     * Product listing for a specific store.
     */
    public function index(Request $request)
    {
        $store = $this->currentStore();

        $products = Product::active()
            ->where('stock', '>', 0)
            ->with('category')
            ->when($request->search, fn($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->when($request->category, fn($q, $c) => $q->where('category_code', $c))
            ->where('type', 'physical')
            ->paginate(12)->withQueryString();

        $categories = Category::orderBy('name')->get();

        $startDate = now()->subMonths(3);
        $endDate = now();
        
        $kmeansService = new \App\Services\KMeansService();
        $bestSellerCodes = $kmeansService->getTopSellerCodes($startDate, $endDate, 4);

        if (empty($bestSellerCodes)) {
            // Fallback if no transactions exist yet
            $topSellers = Product::active()->physical()->limit(4)->get();
        } else {
            // Fetch products ensuring the order from K-Means is preserved if possible, or just normal fetch
            $topSellers = Product::active()
                ->where('stock', '>', 0)
                ->with('category')
                ->whereIn('product_code', $bestSellerCodes)
                ->where('type', 'physical')
                ->limit(4)
                ->get();
        }

        return view('catalog.index', compact('store', 'products', 'categories', 'bestSellerCodes', 'topSellers'));
    }

    /**
     * Product detail page for a specific store.
     */
    public function show(string $store, Product $product)
    {
        if ($product->status !== 'active') {
            abort(404);
        }

        $currentStore = $this->currentStore();

        $soldQty = TransactionItem::where('product_code', $product->product_code)->sum('quantity');

        $relatedProducts = Product::active()
            ->where('category_code', $product->category_code)
            ->where('product_code', '!=', $product->product_code)
            ->where('type', 'physical')
            ->limit(4)
            ->get();

        return view('catalog.show', compact('currentStore', 'product', 'relatedProducts', 'soldQty'));
    }
}
