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
            $query->where('technician_id', auth()->id());
        }

        $serviceRepairs = $query
            ->when($request->search, fn($q, $s) => $q->where('repair_code', 'like', "%{$s}%")->orWhere('customer_name', 'like', "%{$s}%"))
            ->when($request->status, fn($q, $st) => $q->where('status', $st))
            ->latest()
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
        return view('service-repairs.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'       => 'required|string|min:3|max:100',
            'customer_phone'      => 'nullable|string|min:7|max:20',
            'notes'               => 'nullable|string|max:500',
            'items'               => 'required|array|min:1',
            'items.*.name'        => 'required|string|min:3|max:100',
            'items.*.brand'       => 'nullable|string|max:60',
            'items.*.series'      => 'nullable|string|max:60',
            'items.*.complaint'   => 'required|string|min:10|max:1000',
            'items.*.service_fee' => 'nullable|numeric|min:0',
            'items.*.images.*'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $repairCode = ServiceRepair::generateCode();

            $serviceRepair = ServiceRepair::create([
                'repair_code'    => $repairCode,
                'technician_id'  => null, // Assign nanti oleh teknisi
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
            ->where(fn($q) => $q->where('type', 'physical')->orWhere('type', 'sparepart'))
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
        $serviceRepair->load('items');
        return view('service-repairs.edit', compact('serviceRepair'));
    }

    public function update(Request $request, ServiceRepair $serviceRepair)
    {
        $validated = $request->validate([
            'customer_name'  => 'sometimes|required|string|min:3|max:100',
            'customer_phone' => 'nullable|string|min:7|max:20',
            'technician_id'  => 'nullable|exists:users,id',
            'notes'          => 'nullable|string|max:500',
            'down_payment'   => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:cash,qris',
            // Per-item diagnosis update
            'items'                    => 'nullable|array',
            'items.*.id'               => 'nullable|integer|exists:service_repair_items,id',
            'items.*.diagnosis_result' => 'nullable|string|max:1000',
            'items.*.service_fee'      => 'nullable|numeric|min:0',
            'items.*.images.*'         => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        DB::transaction(function () use ($request, $validated, $serviceRepair) {
            $updateData = array_filter([
                'customer_name'  => $validated['customer_name']  ?? null,
                'customer_phone' => $validated['customer_phone'] ?? null,
                'technician_id'  => $validated['technician_id']  ?? null,
                'notes'          => $validated['notes']          ?? null,
                'down_payment'   => $validated['down_payment']   ?? null,
                'payment_method' => $validated['payment_method'] ?? null,
            ], fn($v) => !is_null($v));

            // Validate DP <= total
            $dp = $updateData['down_payment'] ?? $serviceRepair->down_payment;
            if ($dp > $serviceRepair->total_cost && $serviceRepair->total_cost > 0) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'down_payment' => 'DP tidak boleh lebih besar dari total tagihan.',
                ]);
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
            'status'       => 'required|string',
            'down_payment' => 'nullable|numeric|min:0',
        ]);

        $newStatus = $validated['status'];
        $oldStatus = $serviceRepair->status;

        DB::transaction(function () use ($validated, $newStatus, $oldStatus, $serviceRepair) {
            // ── Allowed transitions ──────────────────────────────────────
            $allowedTransitions = [
                ServiceRepair::STATUS_DRAFT         => [ServiceRepair::STATUS_WAITING_DP, ServiceRepair::STATUS_CANCELLED],
                ServiceRepair::STATUS_WAITING_DP    => [ServiceRepair::STATUS_DIAGNOSING, ServiceRepair::STATUS_CANCELLED],
                ServiceRepair::STATUS_DIAGNOSING    => [ServiceRepair::STATUS_WAITING_PARTS, ServiceRepair::STATUS_REPAIRING, ServiceRepair::STATUS_CANCELLED],
                ServiceRepair::STATUS_WAITING_PARTS => [ServiceRepair::STATUS_REPAIRING, ServiceRepair::STATUS_CANCELLED],
                ServiceRepair::STATUS_REPAIRING     => [ServiceRepair::STATUS_READY],
                ServiceRepair::STATUS_READY         => [ServiceRepair::STATUS_DONE],
            ];

            if (!in_array($newStatus, $allowedTransitions[$oldStatus] ?? [])) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'status' => "Transisi status dari '{$oldStatus}' ke '{$newStatus}' tidak diizinkan.",
                ]);
            }

            $updateData = ['status' => $newStatus];

            // Update DP if provided
            if (isset($validated['down_payment'])) {
                $updateData['down_payment'] = $validated['down_payment'];
                $serviceRepair->down_payment = $validated['down_payment'];
            }

            // ── Validate DP before moving to diagnosing ──────────────────
            if ($newStatus === ServiceRepair::STATUS_DIAGNOSING) {
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

            $serviceRepair->update($updateData);
        });

        return back()->with('success', 'Status tiket berhasil diperbarui.');
    }

    // ─── Sparepart Management ────────────────────────────────────────────────

    /**
     * Add a sparepart to a device item (two modes: from_stock | requested).
     */
    public function addPart(Request $request, ServiceRepair $serviceRepair)
    {
        $validated = $request->validate([
            'parent_id'      => 'required|integer|exists:service_repair_items,id',
            'sparepart_type' => 'required|in:from_stock,requested',
            // From stock
            'product_code'   => 'nullable|exists:products,product_code',
            // Requested
            'item_name'      => 'required|string|min:3|max:100',
            'quantity'       => 'required|integer|min:1',
            'unit_price'     => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($validated, $serviceRepair) {
            $subtotal = $validated['unit_price'] * $validated['quantity'];

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

            $serviceRepair->calculateTotalCost();
        });

        return back()->with('success', __('messages.sparepart_added'));
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
            return back()->with('error', 'Item tidak ditemukan di tiket ini.');
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
            ProductPurchase::where('repair_item_id', $sparepartItem->id)->latest()->first())
            ->with('success', 'Pengajuan pengadaan sparepart berhasil dibuat. Lengkapi detailnya di halaman ini.');
    }

    // ─── Destroy ────────────────────────────────────────────────────────────

    public function destroy(ServiceRepair $serviceRepair)
    {
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

