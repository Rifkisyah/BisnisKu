<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        $tab = $request->get('tab', '');
        $showSparepart = $request->boolean('show_sparepart');

        $products = Product::with('category', 'supplier')
            ->when($tab, function ($q) use ($tab, $showSparepart) {
                if ($showSparepart && $tab !== 'sparepart') {
                    $q->whereIn('type', [$tab, 'sparepart']);
                } else {
                    $q->where('type', $tab);
                }
            })
            ->when(!$tab && !$showSparepart, function ($q) {
                $q->where('type', '!=', 'sparepart');
            })
            ->when($request->search, fn($q, $s) => $q->where(fn($sq) => $sq->where('name', 'like', "%{$s}%")->orWhere('product_code', 'like', "%{$s}%")))
            ->when($request->category, fn($q, $c) => $q->where('category_code', $c))
            ->when($request->low_stock, fn($q) => $q->lowStock())
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->applySort($request->sort)
            ->paginate(15)
            ->withQueryString();

        $categories = Category::active()->get();

        return view('products.index', compact('products', 'categories', 'tab'));
    }

    public function create(Request $request)
    {
        if (auth()->user()->isKasir()) abort(403, 'Akses Ditolak: Kasir tidak dapat memodifikasi data produk.');
        $categories = Category::active()->get();
        $suppliers  = Supplier::active()->get();
        $type       = $request->get('type', 'physical');

        return view('products.create', compact('categories', 'suppliers', 'type'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->isKasir()) abort(403, 'Akses Ditolak: Kasir tidak dapat memodifikasi data produk.');
        $validated = $request->validate([
            'name'           => 'required|string|min:3|max:100',
            'category_code'  => 'required|exists:categories,category_code',
            'supplier_code'  => 'nullable|exists:suppliers,supplier_code',
            'unit'           => 'nullable|string|min:1|max:20',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price'  => 'required|numeric|min:0|gte:purchase_price',
            'minimum_stock'  => 'required|integer|min:0',
            'description'    => 'nullable|string|max:500',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'type'           => 'required|in:physical,digital,sparepart',
            'status'         => 'nullable|in:active,inactive,temporary',
        ]);

        // Stok selalu 0 saat pertama dibuat — tidak boleh di-input
        $validated['stock']        = 0;
        $validated['unit']         = $validated['unit'] ?? 'pcs';
        $validated['status']       = $validated['status'] ?? 'active';
        $validated['product_code'] = Product::generateCode($validated['type']);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        Product::create($validated);

        return redirect()->route('products.index', ['tab' => $validated['type']])
            ->with('success', __('messages.created', ['item' => __('messages.product')]));
    }

    /**
     * Quick Store — creates a product with status "temporary" via a modal popup.
     * Used from Pengadaan and Service Repair forms when the product doesn't exist yet.
     */
    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string|min:3|max:100',
            'category_code' => 'required|exists:categories,category_code',
            'unit'          => 'nullable|string|min:1|max:20',
            'type'          => 'nullable|in:physical,sparepart',
        ]);

        $type = $validated['type'] ?? 'physical';

        $product = Product::create([
            'product_code'  => Product::generateCode($type),
            'name'          => $validated['name'],
            'category_code' => $validated['category_code'],
            'unit'          => $validated['unit'] ?? 'pcs',
            'type'          => $type,
            'status'        => 'temporary', // Harus dilengkapi pemilik nanti
            'purchase_price' => 0,
            'selling_price'  => 0,
            'stock'          => 0,
            'minimum_stock'  => 0,
        ]);

        return response()->json([
            'success'      => true,
            'product_code' => $product->product_code,
            'name'         => $product->name,
            'unit'         => $product->unit,
            'status'       => $product->status,
            'message'      => "Produk \"{$product->name}\" ditambahkan sebagai data sementara. Lengkapi detailnya di menu Produk.",
        ]);
    }

    public function show(Product $product)
    {
        $product->load('category', 'supplier', 'stockMovements.createdBy');
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        if (auth()->user()->isKasir()) abort(403, 'Akses Ditolak: Kasir tidak dapat memodifikasi data produk.');
        $categories = Category::active()->get();
        $suppliers  = Supplier::active()->get();
        return view('products.edit', compact('product', 'categories', 'suppliers'));
    }

    public function update(Request $request, Product $product)
    {
        if (auth()->user()->isKasir()) abort(403, 'Akses Ditolak: Kasir tidak dapat memodifikasi data produk.');
        $validated = $request->validate([
            'name'           => 'required|string|min:3|max:100',
            'category_code'  => 'required|exists:categories,category_code',
            'supplier_code'  => 'nullable|exists:suppliers,supplier_code',
            'unit'           => 'nullable|string|min:1|max:20',
            'purchase_price' => 'required|numeric|min:0',
            'selling_price'  => 'required|numeric|min:0|gte:purchase_price',
            'minimum_stock'  => 'required|integer|min:0',
            'description'    => 'nullable|string|max:500',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'status'         => 'required|in:active,inactive,temporary',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('products.index', ['tab' => $product->type])
            ->with('success', __('messages.updated', ['item' => __('messages.product')]));
    }

    public function destroy(Product $product)
    {
        if (auth()->user()->isKasir()) abort(403, 'Akses Ditolak: Kasir tidak dapat memodifikasi data produk.');
        if ($product->transactionItems()->exists() || $product->purchaseItems()->exists()) {
            return back()->with('error', __('messages.cannot_delete_has_relation', ['item' => __('messages.product')]));
        }

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')
            ->with('success', __('messages.deleted', ['item' => __('messages.product')]));
    }
}
