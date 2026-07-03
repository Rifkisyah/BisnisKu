<?php

namespace App\Models;

use App\Models\Traits\BelongsToStore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductPurchase extends Model
{
    use BelongsToStore;

    protected $table = 'product_purchases';

    protected $primaryKey = 'product_purchase_code';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'product_purchase_code', 'store_id', 'purchase_date', 'estimated_arrival_date',
        'total', 'status', 'notes', 'partial_notes', 'created_by',
        // Service — linked to a ServiceRepairItem that requested this purchase
        'repair_item_id',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date'          => 'date',
            'estimated_arrival_date' => 'date',
            'total'                  => 'decimal:2',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProductPurchaseItem::class, 'product_purchase_code', 'product_purchase_code');
    }

    public function repairItem(): BelongsTo
    {
        return $this->belongsTo(ServiceRepairItem::class, 'repair_item_id');
    }

    // ─── Status helpers ─────────────────────────────────────────────────────

    public function isDraft(): bool      { return $this->status === 'draft'; }
    public function isOrdered(): bool    { return $this->status === 'ordered'; }
    public function isPartial(): bool    { return $this->status === 'partial_received'; }
    public function isReceived(): bool   { return $this->status === 'received'; }
    public function isCancelled(): bool  { return $this->status === 'cancelled'; }

    /** Can still be edited (add/remove items) */
    public function isEditable(): bool   { return $this->isDraft(); }

    /** Is a final (immutable) state */
    public function isFinal(): bool      { return $this->isReceived() || $this->isCancelled(); }

    // ─── Business logic ─────────────────────────────────────────────────────

    public function calculateTotal(): void
    {
        $this->total = $this->items()->sum('subtotal');
        $this->save();
    }

    /**
     * Get a list of WhatsApp suppliers involved in this purchase, with their generated messages.
     * @return array
     */
    public function getWhatsappSuppliers(): array
    {
        $suppliers = [];
        $whatsappItems = $this->items()->with(['supplier', 'product'])->where('source', 'whatsapp')->whereNotNull('supplier_code')->get();

        $grouped = $whatsappItems->groupBy('supplier_code');
        foreach ($grouped as $supplierCode => $items) {
            $supplier = $items->first()->supplier;
            if (!$supplier) continue;
            
            $supplierName = $supplier->name;
            $date = $this->purchase_date->format('d/m/Y');
            $lines = ["Halo *{$supplierName}*,"];
            $lines[] = "";
            $lines[] = "Kami ingin memesan barang berikut (Tanggal: {$date}):";
            $lines[] = "";
            foreach ($items as $i => $item) {
                $name = $item->product ? $item->product->name : $item->temp_product_name;
                $lines[] = ($i + 1) . ". *{$name}* — Jumlah: {$item->quantity}";
            }
            $lines[] = "";
            $lines[] = "Mohon konfirmasi ketersediaan dan harga. Terima kasih!";
            
            $suppliers[] = [
                'supplier' => $supplier,
                'message'  => implode("\n", $lines)
            ];
        }

        return $suppliers;
    }

    public function getSummarySources(): string
    {
        $sourceLabels = [
            'whatsapp'    => 'Supplier',
            'marketplace' => 'Marketplace',
            'offline'     => 'Toko Offline',
            'service'     => 'Dari Servis',
            'other'       => 'Lainnya',
        ];

        $sources = $this->items()->select('source')->distinct()->pluck('source')->toArray();
        if (empty($sources)) return '-';
        if (count($sources) > 1) return 'Multi-Sumber';

        return $sourceLabels[$sources[0]] ?? $sources[0];
    }

    public function getSummarySuppliers(): string
    {
        $suppliers = $this->items()->with('supplier')
            ->whereNotNull('supplier_code')
            ->get()
            ->pluck('supplier.name')
            ->filter()
            ->unique()
            ->toArray();
            
        if (empty($suppliers)) return '-';
        if (count($suppliers) > 1) return 'Multi-Supplier';
        
        return array_values($suppliers)[0];
    }

    public static function generateCode(): string
    {
        $today = now()->format('Ymd');
        $prefix = 'PPR' . $today;
        $last = static::withoutGlobalScope('store')->where('product_purchase_code', 'like', $prefix . '%')
            ->orderBy('product_purchase_code', 'desc')
            ->first();
        $number = $last ? (int) substr($last->product_purchase_code, -4) + 1 : 1;
        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
