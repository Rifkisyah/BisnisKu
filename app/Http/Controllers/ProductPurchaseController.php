<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPurchase;
use App\Models\ProductPurchaseItem;
use App\Models\ServiceRepairItem;
use App\Models\Supplier;
use App\Services\StockService;
use App\Services\WhatsAppService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductPurchaseController extends Controller
{
    // ─── Index ──────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $startDate = $request->get('start_date', now()->toDateString());
        $endDate   = $request->get('end_date', now()->toDateString());

        $query = ProductPurchase::with('creator')
            ->whereBetween('purchase_date', [$startDate, \Carbon\Carbon::parse($endDate)->endOfDay()])
            ->when($request->search,   fn($q, $s)   => $q->where('product_purchase_code', 'like', "%{$s}%")->orWhere('notes', 'like', "%{$s}%"))
            ->when($request->status,   fn($q, $st)  => $q->where('status', $st))
            ->when($request->source,   fn($q, $src) => $q->whereHas('items', fn($iq) => $iq->where('source', $src)))
            ->when($request->supplier, fn($q, $sup) => $q->whereHas('items', fn($iq) => $iq->where('supplier_code', $sup)));

        if ($request->get('export') === 'pdf') {
            $allPurchases = $query->applySort($request->sort)->get();
            $pdf = Pdf::loadView('product-purchases.pdf', [
                'purchases' => $allPurchases, 'startDate' => $startDate, 'endDate' => $endDate,
            ]);
            return $pdf->download('laporan-pengadaan.pdf');
        }

        $productPurchases = $query->applySort($request->sort)->paginate(15)->withQueryString();
        $suppliers        = Supplier::active()->get();

        return view('product-purchases.index', compact('productPurchases', 'startDate', 'endDate', 'suppliers'));
    }

    // ─── Create / Store ─────────────────────────────────────────────────────

    public function create()
    {
        $suppliers = Supplier::active()->get();
        $products  = Product::available()->get(); // active + temporary
        $categories = Category::active()->get();
        return view('product-purchases.create', compact('suppliers', 'products', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'purchase_date'          => 'required|date',
            'estimated_arrival_date' => 'nullable|date|after_or_equal:purchase_date',
            'notes'                  => 'nullable|string|max:500',
            'repair_item_id'         => 'nullable|exists:service_repair_items,id',
            'items'                  => 'required|array|min:1',
            'items.*.product_code'      => 'nullable|exists:products,product_code',
            'items.*.temp_product_name' => 'nullable|string|min:3|max:100',
            'items.*.is_sparepart'      => 'nullable|boolean',
            'items.*.notes'             => 'nullable|string|max:500',
            'items.*.quantity'          => 'required|integer|min:1',
            'items.*.purchase_price'    => 'required|numeric|min:0',
            'items.*.source'                 => 'required|in:whatsapp,marketplace,offline,other,service',
            // Source-specific per item
            'items.*.supplier_code'        => 'nullable|exists:suppliers,supplier_code',
            'items.*.marketplace_name'     => 'nullable|string|min:2|max:100',
            'items.*.marketplace_seller'   => 'nullable|string|min:2|max:100',
            'items.*.marketplace_order_id' => 'nullable|string|max:100',
            'items.*.marketplace_notes'    => 'nullable|string|max:500',
            'items.*.store_name'           => 'nullable|string|min:2|max:100',
            'items.*.receipt_number'       => 'nullable|string|max:50',
            'items.*.offline_notes'        => 'nullable|string|max:500',
            'items.*.other_source'         => 'nullable|string|min:2|max:100',
            'items.*.other_notes'          => 'nullable|string|max:500',
        ]);

        // Setiap item wajib ada product_code atau temp_product_name
        foreach ($validated['items'] as $i => $item) {
            if (empty($item['product_code']) && empty($item['temp_product_name'])) {
                return back()->withErrors(["items.{$i}.product_code" => 'Pilih produk atau isi nama produk sementara.'])->withInput();
            }
        }

        DB::transaction(function () use ($validated) {
            $code = ProductPurchase::generateCode();

            $purchaseData = [
                'product_purchase_code'  => $code,
                'purchase_date'          => $validated['purchase_date'],
                'estimated_arrival_date' => $validated['estimated_arrival_date'] ?? null,
                'status'                 => 'draft',
                'notes'                  => $validated['notes'] ?? null,
                'repair_item_id'         => $validated['repair_item_id'] ?? null,
                'created_by'             => auth()->id(),
            ];

            $productPurchase = ProductPurchase::create($purchaseData);

            $total = 0;
            foreach ($validated['items'] as $item) {
                $subtotal    = $item['purchase_price'] * $item['quantity'];
                $productCode = $item['product_code'] ?? null;

                // Jika tidak ada product_code dan ada temp name, buat produk sementara
                if (empty($productCode) && !empty($item['temp_product_name'])) {
                    $isSparepart = !empty($item['is_sparepart']);
                    $type = $isSparepart ? 'sparepart' : 'physical';
                    
                    $catSlug = $isSparepart ? 'sparepart' : 'umum';
                    $catName = $isSparepart ? 'Sparepart' : 'Umum';
                    
                    $category = \App\Models\Category::where('slug', $catSlug)->first()
                        ?? \App\Models\Category::where('slug', 'umum')->first()
                        ?? \App\Models\Category::first()
                        ?? \App\Models\Category::create([
                            'category_code' => 'CAT' . time() . rand(10,99),
                            'name' => $catName,
                            'slug' => $catSlug
                        ]);
                        
                    $supplierCode = ($item['source'] === 'whatsapp' && !empty($item['supplier_code'])) ? $item['supplier_code'] : null;

                    $newProduct = Product::create([
                        'product_code'   => Product::generateCode($type),
                        'name'           => $item['temp_product_name'],
                        'category_code'  => $category?->category_code,
                        'purchase_price' => $item['purchase_price'],
                        'selling_price'  => $item['purchase_price'],
                        'stock'          => 0,
                        'minimum_stock'  => 0,
                        'unit'           => 'pcs',
                        'status'         => 'temporary',
                        'type'           => $type,
                        'supplier_code'  => $supplierCode,
                    ]);
                    $productCode = $newProduct->product_code;
                }

                $itemData = [
                    'product_purchase_code' => $code,
                    'product_code'          => $productCode,
                    'temp_product_name'     => null,
                    'is_resolved'           => !empty($productCode),
                    'resolved_product_code' => $productCode,
                    'quantity'              => $item['quantity'],
                    'quantity_received'     => 0,
                    'quantity_rejected'     => 0,
                    'purchase_price'        => $item['purchase_price'],
                    'subtotal'              => $subtotal,
                    'notes'                 => $item['notes'] ?? null,
                    'source'                => $item['source'],
                    'supplier_code'         => $item['supplier_code'] ?? null,
                ];

                match ($item['source']) {
                    'marketplace' => $itemData = array_merge($itemData, [
                        'marketplace_name'     => $item['marketplace_name'] ?? null,
                        'marketplace_seller'   => $item['marketplace_seller'] ?? null,
                        'marketplace_order_id' => $item['marketplace_order_id'] ?? null,
                        'marketplace_notes'    => $item['marketplace_notes'] ?? null,
                    ]),
                    'offline' => $itemData = array_merge($itemData, [
                        'store_name'     => $item['store_name'] ?? null,
                        'receipt_number' => $item['receipt_number'] ?? null,
                        'offline_notes'  => $item['offline_notes'] ?? null,
                    ]),
                    'other' => $itemData = array_merge($itemData, [
                        'other_source' => $item['other_source'] ?? null,
                        'other_notes'  => $item['other_notes'] ?? null,
                    ]),
                    default => null,
                };

                ProductPurchaseItem::create($itemData);
                $total += $subtotal;
            }
            $productPurchase->update(['total' => $total]);

            // Link to service: update sparepart item status to 'pending'
            if (!empty($validated['repair_item_id'])) {
                ServiceRepairItem::where('id', $validated['repair_item_id'])
                    ->update(['sparepart_status' => 'pending']);
            }
        });

        return redirect()->route('product-purchases.index')
            ->with('success', __('messages.created', ['item' => __('messages.procurement')]));
    }

    // ─── Edit / Update ──────────────────────────────────────────────────────

    public function edit(ProductPurchase $productPurchase)
    {
        if ($productPurchase->status !== 'draft') {
            return redirect()->route('product-purchases.index')->with('error', 'Hanya pengadaan berstatus draft yang dapat diubah.');
        }

        $suppliers = Supplier::active()->get();
        $products  = Product::available()->get();
        $categories = Category::active()->get();
        
        return view('product-purchases.edit', compact('productPurchase', 'suppliers', 'products', 'categories'));
    }

    public function update(Request $request, ProductPurchase $productPurchase)
    {
        if ($productPurchase->status !== 'draft') {
            return redirect()->route('product-purchases.index')->with('error', 'Hanya pengadaan berstatus draft yang dapat diubah.');
        }

        $validated = $request->validate([
            'purchase_date'          => 'required|date',
            'estimated_arrival_date' => 'nullable|date|after_or_equal:purchase_date',
            'notes'                  => 'nullable|string|max:500',
            'items'                  => 'required|array|min:1',
            'items.*.product_code'      => 'nullable|exists:products,product_code',
            'items.*.temp_product_name' => 'nullable|string|min:3|max:100',
            'items.*.is_sparepart'      => 'nullable|boolean',
            'items.*.notes'             => 'nullable|string|max:500',
            'items.*.quantity'          => 'required|integer|min:1',
            'items.*.purchase_price'    => 'required|numeric|min:0',
            'items.*.source'                 => 'required|in:whatsapp,marketplace,offline,other,service',
            'items.*.supplier_code'        => 'nullable|exists:suppliers,supplier_code',
            'items.*.marketplace_name'     => 'nullable|string|min:2|max:100',
            'items.*.marketplace_seller'   => 'nullable|string|min:2|max:100',
            'items.*.marketplace_order_id' => 'nullable|string|max:100',
            'items.*.marketplace_notes'    => 'nullable|string|max:500',
            'items.*.store_name'           => 'nullable|string|min:2|max:100',
            'items.*.receipt_number'       => 'nullable|string|max:50',
            'items.*.offline_notes'        => 'nullable|string|max:500',
            'items.*.other_source'         => 'nullable|string|min:2|max:100',
            'items.*.other_notes'          => 'nullable|string|max:500',
        ]);

        foreach ($validated['items'] as $i => $item) {
            if (empty($item['product_code']) && empty($item['temp_product_name'])) {
                return back()->withErrors(["items.{$i}.product_code" => 'Pilih produk atau isi nama produk sementara.'])->withInput();
            }
        }

        DB::transaction(function () use ($validated, $productPurchase) {
            $productPurchase->update([
                'purchase_date'          => $validated['purchase_date'],
                'estimated_arrival_date' => $validated['estimated_arrival_date'] ?? null,
                'notes'                  => $validated['notes'] ?? null,
            ]);

            // Hapus item lama, karena draft kita bisa re-create semua itemnya
            $productPurchase->items()->delete();

            $total = 0;
            foreach ($validated['items'] as $item) {
                $subtotal    = $item['purchase_price'] * $item['quantity'];
                $productCode = $item['product_code'] ?? null;

                if (empty($productCode) && !empty($item['temp_product_name'])) {
                    $isSparepart = !empty($item['is_sparepart']);
                    $type = $isSparepart ? 'sparepart' : 'physical';
                    $catSlug = $isSparepart ? 'sparepart' : 'umum';
                    $catName = $isSparepart ? 'Sparepart' : 'Umum';
                    
                    $category = \App\Models\Category::where('slug', $catSlug)->first()
                        ?? \App\Models\Category::where('slug', 'umum')->first()
                        ?? \App\Models\Category::first()
                        ?? \App\Models\Category::create([
                            'category_code' => 'CAT' . time() . rand(10,99),
                            'name' => $catName,
                            'slug' => $catSlug
                        ]);
                        
                    $supplierCode = ($item['source'] === 'whatsapp' && !empty($item['supplier_code'])) ? $item['supplier_code'] : null;

                    $newProduct = Product::create([
                        'product_code'   => Product::generateCode($type),
                        'name'           => $item['temp_product_name'],
                        'category_code'  => $category?->category_code,
                        'purchase_price' => $item['purchase_price'],
                        'selling_price'  => $item['purchase_price'],
                        'stock'          => 0,
                        'minimum_stock'  => 0,
                        'unit'           => 'pcs',
                        'status'         => 'temporary',
                        'type'           => $type,
                        'supplier_code'  => $supplierCode,
                    ]);
                    $productCode = $newProduct->product_code;
                }

                $itemData = [
                    'product_purchase_code' => $productPurchase->product_purchase_code,
                    'product_code'          => $productCode,
                    'temp_product_name'     => null,
                    'is_resolved'           => !empty($productCode),
                    'resolved_product_code' => $productCode,
                    'quantity'              => $item['quantity'],
                    'quantity_received'     => 0,
                    'quantity_rejected'     => 0,
                    'purchase_price'        => $item['purchase_price'],
                    'subtotal'              => $subtotal,
                    'notes'                 => $item['notes'] ?? null,
                    'source'                => $item['source'],
                    'supplier_code'         => $item['supplier_code'] ?? null,
                ];

                match ($item['source']) {
                    'marketplace' => $itemData = array_merge($itemData, [
                        'marketplace_name'     => $item['marketplace_name'] ?? null,
                        'marketplace_seller'   => $item['marketplace_seller'] ?? null,
                        'marketplace_order_id' => $item['marketplace_order_id'] ?? null,
                        'marketplace_notes'    => $item['marketplace_notes'] ?? null,
                    ]),
                    'offline' => $itemData = array_merge($itemData, [
                        'store_name'     => $item['store_name'] ?? null,
                        'receipt_number' => $item['receipt_number'] ?? null,
                        'offline_notes'  => $item['offline_notes'] ?? null,
                    ]),
                    'other' => $itemData = array_merge($itemData, [
                        'other_source' => $item['other_source'] ?? null,
                        'other_notes'  => $item['other_notes'] ?? null,
                    ]),
                    default => null,
                };

                ProductPurchaseItem::create($itemData);
                $total += $subtotal;
            }
            $productPurchase->update(['total' => $total]);
        });

        return redirect()->route('product-purchases.show', $productPurchase)
            ->with('success', 'Pengadaan berhasil diperbarui.');
    }

    // ─── Show ───────────────────────────────────────────────────────────────

    public function show(ProductPurchase $productPurchase)
    {
        $productPurchase->load('creator', 'items.product', 'items.resolvedProduct', 'repairItem.serviceRepair', 'items.supplier');
        $products = Product::available()->get();
        return view('product-purchases.show', compact('productPurchase', 'products'));
    }

    // ─── Update Status ──────────────────────────────────────────────────────

    public function updateStatus(Request $request, ProductPurchase $productPurchase)
    {
        $validated = $request->validate([
            'status'       => 'required|in:draft,ordered,partial_received,received,cancelled',
            'partial_notes'=> 'nullable|string',
            // Per-item qty received / rejected (used when status = partial_received or received)
            'items'        => 'nullable|array',
            'items.*.id'                  => 'required|integer|exists:product_purchase_items,id',
            'items.*.quantity_received'   => 'required|integer|min:0',
            'items.*.quantity_rejected'   => 'required|integer|min:0',
            'items.*.rejection_notes'     => 'nullable|string',
            'items.*.purchase_price'      => 'nullable|numeric|min:0',
        ]);

        $stockService = new StockService();

        DB::transaction(function () use ($validated, $productPurchase, $stockService) {
            $oldStatus = $productPurchase->status;
            $newStatus = $validated['status'];

            // Guard: cannot re-receive
            if ($oldStatus === 'received') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'status' => 'Pengadaan sudah selesai diterima dan tidak bisa diubah.',
                ]);
            }

            // Guard: cannot un-cancel
            if ($oldStatus === 'cancelled') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'status' => 'Pengadaan yang dibatalkan tidak dapat diubah kembali.',
                ]);
            }

            // Update item qty received/rejected jika ada
            if (!empty($validated['items'])) {
                foreach ($validated['items'] as $itemData) {
                    $item = ProductPurchaseItem::find($itemData['id']);
                    if ($item && $item->product_purchase_code === $productPurchase->product_purchase_code) {
                        $item->update([
                            'quantity_received' => $itemData['quantity_received'],
                            'quantity_rejected'  => $itemData['quantity_rejected'],
                            'rejection_notes'    => $itemData['rejection_notes'] ?? null,
                            'purchase_price'     => $itemData['purchase_price'] ?? $item->purchase_price,
                            'subtotal'           => ($itemData['purchase_price'] ?? $item->purchase_price) * $item->quantity,
                        ]);
                    }
                }
                // Recalculate total
                $productPurchase->update([
                    'total' => $productPurchase->items()->sum('subtotal'),
                ]);
            }

            $productPurchase->update([
                'status'        => $newStatus,
                'partial_notes' => $validated['partial_notes'] ?? $productPurchase->partial_notes,
            ]);

            // Process stock only on received / partial_received
            if (in_array($newStatus, ['received', 'partial_received']) && $oldStatus !== 'received') {
                $productPurchase->load('items.product', 'items.resolvedProduct');

                foreach ($productPurchase->items as $item) {
                    $effectiveCode = $item->effective_product_code;
                    $qtyToAdd      = $newStatus === 'partial_received'
                        ? $item->quantity_received
                        : ($item->quantity_received > 0 ? $item->quantity_received : $item->quantity);

                    if ($effectiveCode && $qtyToAdd > 0) {
                        $stockService->stockIn(
                            $effectiveCode,
                            $qtyToAdd,
                            'product_purchase',
                            $productPurchase->product_purchase_code,
                            "Pengadaan: {$productPurchase->product_purchase_code}"
                        );
                        // Update purchase_price to latest
                        Product::where('product_code', $effectiveCode)
                            ->update(['purchase_price' => $item->purchase_price]);
                    }
                }

                // If linked to a repair item, update its sparepart status & check if repair can proceed
                if ($productPurchase->repair_item_id) {
                    $repairItem = ServiceRepairItem::find($productPurchase->repair_item_id);
                    if ($repairItem) {
                        // Ambil harga asli dari pengadaan yang disetujui (purchase_price)
                        $purchaseItem = $productPurchase->items->first();
                        $finalPrice = $purchaseItem ? $purchaseItem->purchase_price : ($repairItem->temp_purchase_price ?? 0);

                        $repairItem->update([
                            'sparepart_status'    => 'available',
                            'temp_purchase_price' => $finalPrice,
                            'subtotal'            => $finalPrice * $repairItem->quantity,
                        ]);

                        $repair = $repairItem->serviceRepair;
                        if ($repair) {
                            $repair->calculateTotalCost();

                            if ($repair->allPartsAvailable() && $repair->status === 'waiting_parts') {
                                // Auto-advance ticket to 'repairing' when all parts are ready
                                $repair->update(['status' => 'repairing']);
                            }
                        }
                    }
                }
            }
        });

        return back()->with('success', __('messages.status_updated'));
    }

    // ─── WhatsApp ───────────────────────────────────────────────────────────

    public function sendWhatsApp(ProductPurchase $productPurchase)
    {
        if ($productPurchase->source !== 'whatsapp') {
            return back()->with('error', 'Pengadaan ini bukan via WhatsApp.');
        }
        if (!$productPurchase->supplier || !$productPurchase->supplier->whatsapp_number) {
            return back()->with('error', 'Supplier atau nomor kontak supplier tidak ditemukan.');
        }

        $waService = new WhatsAppService();
        $phone     = WhatsAppService::formatPhone($productPurchase->supplier->whatsapp_number);
        $result    = $waService->send($phone, $productPurchase->wa_message_content);

        $productPurchase->update([
            'wa_message_status' => $result['success'] ? 'sent' : 'failed',
        ]);

        $msg = $result['success']
            ? 'Pesan WhatsApp berhasil dikirim ke supplier.'
            : 'Gagal mengirim pesan: ' . $result['message'];

        return back()->with($result['success'] ? 'success' : 'error', $msg);
    }

    // ─── Destroy ────────────────────────────────────────────────────────────

    public function destroy(ProductPurchase $productPurchase)
    {
        if ($productPurchase->isCancelled()) {
            return back()->with('error', 'Pengadaan yang dibatalkan tidak bisa dihapus.');
        }

        // Revert stock jika ada barang yang sempat diterima (status partial_received atau received)
        if ($productPurchase->isPartial() || $productPurchase->isReceived()) {
            $stockService = new \App\Services\StockService();
            foreach ($productPurchase->items as $item) {
                if ($item->effective_product_code && $item->quantity_received > 0) {
                    $stockService->stockOut(
                        $item->effective_product_code,
                        $item->quantity_received,
                        'product_purchase',
                        $productPurchase->product_purchase_code,
                        'Revert pengadaan dihapus'
                    );
                }
            }
        }

        $productPurchase->delete();
        return redirect()->route('product-purchases.index')
            ->with('success', __('messages.deleted', ['item' => __('messages.procurement')]));
    }
}
