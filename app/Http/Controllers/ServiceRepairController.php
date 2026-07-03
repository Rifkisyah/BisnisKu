<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductPurchase;
use App\Models\ProductPurchaseItem;
use App\Models\ServiceRepair;
use App\Models\ServiceRepairItem;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ServiceRepairController extends Controller
{
    // ─── Index ──────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $startDate = $request->get('start_date', now()->toDateString());
        $endDate   = $request->get('end_date', now()->toDateString());

        $query = ServiceRepair::with(['technician', 'items'])
            ->whereBetween('start_date', [$startDate, \Carbon\Carbon::parse($endDate)->endOfDay()]);

        if (auth()->user()->isTeknisi()) {
            $query->where(function($q) {
                $q->where('technician_id', auth()->id())
                  ->orWhereNull('technician_id');
            });
        }

        $serviceRepairs = $query
            ->when($request->search, fn($q, $s) => $q->where('repair_code', 'like', "%{$s}%")->orWhere('customer_name', 'like', "%{$s}%"))
            ->when($request->status, fn($q, $st) => $q->where('status', $st))
            ->applySort($request->sort)
            ->paginate(15)
            ->withQueryString();

        if ($request->get('export') === 'pdf') {
            $allRepairs = $query->get();
            $pdf = Pdf::loadView('service-repairs.pdf', [
                'repairs' => $allRepairs, 'startDate' => $startDate, 'endDate' => $endDate,
            ]);
            return $pdf->download('laporan-perbaikan.pdf');
        }

        return view('service-repairs.index', compact('serviceRepairs', 'startDate', 'endDate'));
    }

    // ─── Create / Store ─────────────────────────────────────────────────────

    public function create()
    {
        // Hanya kasir dan owner yang boleh membuat data perbaikan baru
        if (auth()->user()->isTeknisi()) {
            abort(403, 'Teknisi tidak dapat membuat data perbaikan baru.');
        }

        $products = Product::available()->where('type', 'sparepart')->where('stock', '>', 0)->get();
        return view('service-repairs.create', compact('products'));
    }

    public function store(Request $request)
    {
        // Hanya kasir dan owner yang boleh menyimpan data perbaikan baru
        if (auth()->user()->isTeknisi()) {
            abort(403, 'Teknisi tidak dapat membuat data perbaikan baru.');
        }

        $validated = $request->validate([
            'customer_name'       => 'required|string|min:3|max:100',
            'customer_phone'      => 'nullable|string|min:7|max:20',
            'notes'               => 'nullable|string|max:500',
            'items'               => 'required|array|min:1',
            'items.*.name'        => 'required|string|min:3|max:100',
            'items.*.brand'       => 'nullable|string|max:60',
            'items.*.series'      => 'nullable|string|max:60',
            'items.*.complaint'   => 'required|string|max:1000',
            'items.*.service_fee' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $repairCode = ServiceRepair::generateCode();

            $serviceRepair = ServiceRepair::create([
                'repair_code'    => $repairCode,
                'technician_id'  => auth()->user()->isTeknisi() ? auth()->id() : null, // Assign to teknisi if they create it
                'customer_name'  => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'] ?? null,
                'service_fee'    => 0,
                'component_cost' => 0,
                'total_cost'     => 0,
                'payment_method' => 'cash',
                'down_payment'   => 0,
                'status'         => ServiceRepair::STATUS_DRAFT,
                'start_date'     => now(),
                'notes'          => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $idx => $item) {
                $imagePaths = [];
                if ($request->hasFile("items.{$idx}.images")) {
                    foreach ($request->file("items.{$idx}.images") as $file) {
                        $imagePaths[] = $file->store('service_repairs', 'public');
                    }
                }

                ServiceRepairItem::create([
                    'repair_code'      => $repairCode,
                    'parent_id'        => null, // device-level item
                    'component_code'   => null,
                    'name'             => $item['name'],
                    'brand'            => $item['brand'] ?? null,
                    'series'           => $item['series'] ?? null,
                    'complaint'        => $item['complaint'],
                    'diagnosis_result' => null,
                    'images'           => empty($imagePaths) ? null : $imagePaths,
                    'quantity'         => 1,
                    'service_fee'      => $item['service_fee'] ?? 0,
                    'subtotal'         => 0,
                ]);
            }

            $serviceRepair->calculateTotalCost();
        });

        return redirect()->route('service-repairs.index')
            ->with('success', __('messages.created', ['item' => __('messages.service_repair')]));
    }

    // ─── Show ───────────────────────────────────────────────────────────────

    public function show(ServiceRepair $serviceRepair)
    {
        $serviceRepair->load('technician', 'items.component', 'items.children.component', 'items.children.productPurchases');
        $products = Product::available()
            ->where('type', 'sparepart')
            ->where('stock', '>', 0)
            ->get();
        $categories = Category::active()->get();

        // Try to get shop name from settings table if available
        $shopName = config('app.name');
        try {
            $setting = \DB::table('business_settings')->first();
            if ($setting && !empty($setting->shop_name)) {
                $shopName = $setting->shop_name;
            }
        } catch (\Exception $e) {
            // Table may not exist, fall back to app name
        }

        return view('service-repairs.show', compact('serviceRepair', 'products', 'categories', 'shopName'));
    }

    // ─── Edit / Update ──────────────────────────────────────────────────────

    public function edit(ServiceRepair $serviceRepair)
    {
        if (auth()->user()->isKasir() && $serviceRepair->status !== 'draft') {
            abort(403, 'Kasir hanya dapat mengubah data perbaikan yang masih dalam status draft.');
        }
        $serviceRepair->load('items');
        return view('service-repairs.edit', compact('serviceRepair'));
    }

    public function update(Request $request, ServiceRepair $serviceRepair)
    {
        if (auth()->user()->isKasir() && $serviceRepair->status !== 'draft') {
            abort(403, 'Kasir hanya dapat mengubah data perbaikan yang masih dalam status draft.');
        }

        $validated = $request->validate([
            'customer_name'  => 'nullable|string|min:3|max:100',
            'customer_phone' => 'nullable|string|min:7|max:20',
            'notes'          => 'nullable|string|max:500',
            'down_payment'   => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:cash,qris',
        ]);

                DB::transaction(function () use ($request, $validated, $serviceRepair) {
            $updateData = array_filter([
                'customer_name'  => $validated['customer_name']  ?? null,
                'customer_phone' => $validated['customer_phone'] ?? null,
                'notes'          => $validated['notes']          ?? null,
            ], fn($v) => $v !== null);

            if (isset($validated['down_payment'])) {
                $updateData['down_payment'] = $validated['down_payment'];
            }
                        if (isset($validated['payment_method'])) {
                $updateData['payment_method'] = $validated['payment_method'];
            }

            // Assign technician if not assigned and user is a technician
            if (is_null($serviceRepair->technician_id) && auth()->check() && auth()->user()->isTeknisi()) {
                $updateData['technician_id'] = auth()->id();
            }

            $serviceRepair->update($updateData);

            // Update per-item fields
            if (!empty($validated['items'])) {
                foreach ($validated['items'] as $idx => $itemData) {
                    if (!isset($itemData['id'])) continue;
                    $item = ServiceRepairItem::find($itemData['id']);
                    if (!$item || $item->repair_code !== $serviceRepair->repair_code) continue;

                    $imagePaths = $item->images ?? [];
                    if ($request->hasFile("items.{$idx}.images")) {
                        foreach ($request->file("items.{$idx}.images") as $file) {
                            $imagePaths[] = $file->store('service_repairs', 'public');
                        }
                    }

                    $item->update([
                        'diagnosis_result' => $itemData['diagnosis_result'] ?? $item->diagnosis_result,
                        'service_fee'      => $itemData['service_fee'] ?? $item->service_fee,
                        'images'           => empty($imagePaths) ? null : $imagePaths,
                    ]);
                }
            }

            $serviceRepair->calculateTotalCost();
        });

        return redirect()->route('service-repairs.show', $serviceRepair)
            ->with('success', __('messages.updated', ['item' => __('messages.service_repair')]));
    }

    // ─── Status Transitions ─────────────────────────────────────────────────

    /**
     * Dedicated endpoint for status transitions with business rule enforcement.
     */
    public function updateStatus(Request $request, ServiceRepair $serviceRepair)
    {
                $validated = $request->validate([
            'status'               => 'required|string',
            'down_payment'         => 'nullable|numeric|min:0',
            'final_payment_method' => 'nullable|in:cash,qris',
            'items'                => 'nullable|array',
            'items.*.id'           => 'nullable|integer',
            'items.*.service_fee'  => 'nullable',
            'items.*.diagnosis_result' => 'nullable|string',
            'items.*.images.*'     => 'image|max:5120',
        ]);

        $newStatus = $validated['status'];
        $oldStatus = $serviceRepair->status;

        DB::transaction(function () use ($validated, $newStatus, $oldStatus, $serviceRepair, $request) {
            // ── Allowed transitions ──────────────────────────────────────
            $allowedTransitions = [
                ServiceRepair::STATUS_DRAFT         => [ServiceRepair::STATUS_DIAGNOSING, ServiceRepair::STATUS_CANCELLED],
                ServiceRepair::STATUS_WAITING_DP    => [ServiceRepair::STATUS_REPAIRING, ServiceRepair::STATUS_CANCELLED],
                ServiceRepair::STATUS_DIAGNOSING    => [ServiceRepair::STATUS_WAITING_DP, ServiceRepair::STATUS_REPAIRING, ServiceRepair::STATUS_CANCELLED],

                ServiceRepair::STATUS_WAITING_PARTS => [ServiceRepair::STATUS_REPAIRING, ServiceRepair::STATUS_CANCELLED],
                ServiceRepair::STATUS_REPAIRING     => [ServiceRepair::STATUS_READY, ServiceRepair::STATUS_CANCELLED],
                ServiceRepair::STATUS_READY         => [ServiceRepair::STATUS_DONE],
            ];

            if ($newStatus !== $oldStatus && !in_array($newStatus, $allowedTransitions[$oldStatus] ?? [])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'status' => "Transisi status dari '{$oldStatus}' ke '{$newStatus}' tidak diizinkan.",
                ]);
            }

            $updateData = ['status' => $newStatus];

            // Update DP if provided (when confirming DP)
            if (isset($validated['down_payment'])) {
                $updateData['down_payment'] = $validated['down_payment'];
                $serviceRepair->down_payment = $validated['down_payment'];
            }

            // Update per-item fields (diagnosis, images) if provided when changing status
            if (!empty($validated['items'])) {
                foreach ($validated['items'] as $idx => $itemData) {
                    if (!isset($itemData['id'])) continue;
                    $item = ServiceRepairItem::find($itemData['id']);
                    if (!$item || $item->repair_code !== $serviceRepair->repair_code) continue;

                    $updateItemData = [];
                    
                    if (array_key_exists('diagnosis_result', $itemData)) {
                        $updateItemData['diagnosis_result'] = $itemData['diagnosis_result'];
                    }
                    
                    if (isset($itemData['service_fee'])) {
                        $updateItemData['service_fee'] = (float)preg_replace('/[^0-9]/', '', $itemData['service_fee']);
                    }

                    $imagePaths = $item->images ?? [];
                    if ($request->hasFile("items.{$idx}.images")) {
                        foreach ($request->file("items.{$idx}.images") as $file) {
                            $imagePaths[] = $file->store('service_repairs', 'public');
                        }
                        $updateItemData['images'] = $imagePaths;
                    }

                    if (!empty($updateItemData)) {
                        $item->update($updateItemData);
                    }
                }
            }

            // ── Validate DP before moving to repairing ───────────────────
            if ($newStatus === ServiceRepair::STATUS_REPAIRING
                && in_array($oldStatus, [ServiceRepair::STATUS_WAITING_DP, ServiceRepair::STATUS_DIAGNOSING])) {
                if (!$serviceRepair->isDpSufficient()) {
                    $min   = number_format($serviceRepair->total_cost * 0.5, 0, ',', '.');
                    $total = number_format($serviceRepair->total_cost, 0, ',', '.');
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'status' => "DP belum mencukupi. Minimum 50% = Rp {$min} dari total Rp {$total}.",
                    ]);
                }
            }

            // ── Deduct stock when entering 'repairing' ───────────────────
            if ($newStatus === ServiceRepair::STATUS_REPAIRING && $oldStatus !== ServiceRepair::STATUS_REPAIRING) {
                $stockService = new StockService();
                $spareparts = ServiceRepairItem::where('repair_code', $serviceRepair->repair_code)
                    ->whereNotNull('parent_id')
                    ->where('sparepart_type', 'from_stock')
                    ->whereNotNull('component_code')
                    ->get();

                foreach ($spareparts as $part) {
                    $stockService->stockOut(
                        $part->component_code,
                        $part->quantity,
                        'service_repair',
                        $serviceRepair->repair_code,
                        "Sparepart untuk Perbaikan {$serviceRepair->repair_code}"
                    );
                    $part->update(['sparepart_status' => 'used']);
                }
            }

            // ── Set completion date ──────────────────────────────────────
            if (in_array($newStatus, [ServiceRepair::STATUS_READY, ServiceRepair::STATUS_DONE])
                && !$serviceRepair->completion_date) {
                $updateData['completion_date'] = now();
            }

            if (isset($validated['final_payment_method']) && $newStatus === ServiceRepair::STATUS_DONE) {
                // Final payment processing
                if (!empty($validated['items'])) {
                    foreach ($validated['items'] as $itemId => $itemData) {
                        if (isset($itemData['service_fee'])) {
                            // clean up formatting if user inputs with thousand separators (though alpine handles it)
                            $fee = preg_replace('/[^0-9]/', '', $itemData['service_fee']);
                            ServiceRepairItem::where('id', $itemId)
                                ->where('repair_code', $serviceRepair->repair_code)
                                ->update(['service_fee' => (float)$fee]);
                        }
                    }
                    $serviceRepair->recalculateCost();
                }
            }

            $serviceRepair->update($updateData);
        });

        return redirect()->route('service-repairs.show', $serviceRepair)->with('success', 'Status perbaikan berhasil diperbarui.');
    }

    // ─── Sparepart Management ────────────────────────────────────────────────

    /**
     * Add a sparepart to a device item (two modes: from_stock | requested).
     */
    public function addPart(Request $request, ServiceRepair $serviceRepair)
    {
        $validated = $request->validate([
            'parent_id'     => 'required|integer|exists:service_repair_items,id',
            'sparepart_type'=> 'required|in:from_stock,requested',
            'product_code'  => 'nullable|exists:products,product_code',
            'item_name'     => 'required|string|min:2|max:100',
            'quantity'      => 'required|integer|min:1',
            'unit_price'    => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $serviceRepair) {
            if ($validated['sparepart_type'] === 'from_stock') {
                $product = \App\Models\Product::where('product_code', $validated['product_code'])->firstOrFail();
                $validated['unit_price'] = $product->selling_price;
                $validated['item_name']  = $product->name;
            }

            $subtotal = $validated['unit_price'] * $validated['quantity'];
            
            // Assign technician if not assigned and user is a technician
            if (is_null($serviceRepair->technician_id) && auth()->check() && auth()->user()->isTeknisi()) {
                $serviceRepair->update(['technician_id' => auth()->id()]);
            }

            $part = ServiceRepairItem::create([
                'repair_code'         => $serviceRepair->repair_code,
                'parent_id'           => $validated['parent_id'],
                'component_code'      => $validated['product_code'] ?? null,
                'name'                => $validated['item_name'],
                'quantity'            => $validated['quantity'],
                'service_fee'         => 0,
                'subtotal'            => $subtotal,
                'sparepart_type'      => $validated['sparepart_type'],
                'sparepart_status'    => $validated['sparepart_type'] === 'from_stock' ? 'available' : null,
                'temp_purchase_price' => $validated['unit_price'],
            ]);

            // Calculate costs
            $serviceRepair->calculateTotalCost();
        });

        return back()->with('success', 'Pengajuan sparepart berhasil dibuat.');
    }

    /**
     * Delete a sparepart item from the repair ticket.
     */
    public function deletePart(ServiceRepair $serviceRepair, ServiceRepairItem $item)
    {
        if ($item->repair_code !== $serviceRepair->repair_code) {
            abort(403, 'Item tidak sesuai dengan tiket perbaikan.');
        }
        if ($item->sparepart_type === null) {
            abort(403, 'Tidak dapat menghapus item perangkat utama.');
        }

        // If from_stock and status is used, we technically should revert stock out, 
        // but typically deletion is done BEFORE status changes to repairing.
        if ($item->sparepart_status === 'used') {
            abort(403, 'Tidak dapat menghapus sparepart yang sudah terpakai.');
        }

        // Delete associated product purchases if any
        if ($item->productPurchases()->exists()) {
            $item->productPurchases()->delete();
        }

        $item->delete();
        $serviceRepair->calculateTotalCost();

        return back()->with('success', 'Sparepart berhasil dihapus.');
    }

    /**
     * Create a procurement request from a requested sparepart item.
     * Source = 'service', status = 'draft', linked to repair_item_id.
     */
    public function requestPart(Request $request, ServiceRepair $serviceRepair)
    {
        $validated = $request->validate([
            'sparepart_item_id' => 'required|integer|exists:service_repair_items,id',
            'supplier_code'     => 'nullable|exists:suppliers,supplier_code',
            'purchase_date'     => 'nullable|date',
        ]);

        $sparepartItem = ServiceRepairItem::findOrFail($validated['sparepart_item_id']);

        // Guard: must belong to this repair and must be a requested part
        if ($sparepartItem->repair_code !== $serviceRepair->repair_code) {
            return back()->with('error', 'Item tidak ditemukan di perbaikan ini.');
        }
        if ($sparepartItem->sparepart_type !== 'requested') {
            return back()->with('error', 'Item ini bukan jenis pengajuan sparepart.');
        }
        // Guard: don't double-request
        if ($sparepartItem->productPurchases()->whereNotIn('status', ['cancelled'])->exists()) {
            return back()->with('error', 'Pengajuan pengadaan untuk sparepart ini sudah ada.');
        }

        DB::transaction(function () use ($validated, $serviceRepair, $sparepartItem) {
            $code = ProductPurchase::generateCode();

            $purchase = ProductPurchase::create([
                'product_purchase_code'  => $code,
                'source'                 => 'service',
                'supplier_code'          => $validated['supplier_code'] ?? null,
                'purchase_date'          => $validated['purchase_date'] ?? now()->toDateString(),
                'status'                 => 'draft',
                'repair_item_id'         => $sparepartItem->id,
                'created_by'             => auth()->id(),
                'notes'                  => "Pengajuan dari Servis: {$serviceRepair->repair_code}",
            ]);

            // Create purchase item linked to master product if component_code exists, else temp name
            ProductPurchaseItem::create([
                'product_purchase_code' => $code,
                'product_code'          => $sparepartItem->component_code,
                'temp_product_name'     => $sparepartItem->component_code ? null : $sparepartItem->name,
                'is_resolved'           => !is_null($sparepartItem->component_code),
                'resolved_product_code' => $sparepartItem->component_code,
                'quantity'              => $sparepartItem->quantity,
                'quantity_received'     => 0,
                'quantity_rejected'     => 0,
                'purchase_price'        => $sparepartItem->temp_purchase_price ?? 0,
                'subtotal'              => ($sparepartItem->temp_purchase_price ?? 0) * $sparepartItem->quantity,
            ]);

            $purchase->update(['total' => $purchase->items()->sum('subtotal')]);

            // Update sparepart item status to 'pending'
            $sparepartItem->update(['sparepart_status' => 'pending']);

            // Auto-set repair ticket to waiting_parts if currently diagnosing
            if ($serviceRepair->status === ServiceRepair::STATUS_DIAGNOSING) {
                $serviceRepair->update(['status' => ServiceRepair::STATUS_WAITING_PARTS]);
            }
        });

        return redirect()->route('product-purchases.show',
            ProductPurchase::where('repair_item_id', $sparepartItem->id)->applySort($request->sort)->first())
            ->with('success', 'Pengajuan pengadaan sparepart berhasil dibuat. Lengkapi detailnya di halaman ini.');
    }

    // ─── Destroy ────────────────────────────────────────────────────────────

    public function destroy(ServiceRepair $serviceRepair)
    {
        // Teknisi tidak boleh menghapus data perbaikan
        if (auth()->user()->isTeknisi()) {
            abort(403, 'Teknisi tidak memiliki akses untuk menghapus data perbaikan.');
        }

        if ($serviceRepair->images) {
            foreach ($serviceRepair->images as $img) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($img);
            }
        }
        $serviceRepair->items()->delete();
        $serviceRepair->delete();

        return redirect()->route('service-repairs.index')
            ->with('success', __('messages.deleted', ['item' => __('messages.service_repair')]));
    }

    // ─── Receipt ────────────────────────────────────────────────────────────

    public function receipt(ServiceRepair $serviceRepair)
    {
        $serviceRepair->load('technician', 'items.component');
        return view('service-repairs.receipt', compact('serviceRepair'));
    }

    public function receiptPdf(ServiceRepair $serviceRepair)
    {
        $serviceRepair->load('technician', 'items.component');
        $pdf = Pdf::loadView('service-repairs.receipt-pdf', compact('serviceRepair'));
        return $pdf->download("service-{$serviceRepair->repair_code}.pdf");
    }
}

