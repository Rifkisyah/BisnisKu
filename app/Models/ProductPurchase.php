<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductPurchase extends Model
{
    protected $table = 'product_purchases';

    protected $primaryKey = 'product_purchase_code';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'product_purchase_code', 'source', 'supplier_code', 'purchase_date', 'estimated_arrival_date',
        'total', 'status', 'notes', 'partial_notes', 'created_by',
        // WA
        'wa_message_content', 'wa_message_status',
        // Marketplace
        'marketplace_name', 'marketplace_seller', 'marketplace_order_id', 'marketplace_notes',
        // Offline
        'store_name', 'receipt_number', 'offline_notes',
        // Other
        'other_source', 'other_notes',
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

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_code', 'supplier_code');
    }

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
     * Generate an automatic WhatsApp message content based on purchase items.
     */
    public function generateWaMessageContent(): string
    {
        $supplierName = $this->supplier ? $this->supplier->name : 'Supplier';
        $date = $this->purchase_date->format('d/m/Y');
        $lines = ["Halo *{$supplierName}*,"];
        $lines[] = "";
        $lines[] = "Kami ingin memesan barang berikut (Tanggal: {$date}):";
        $lines[] = "";
        foreach ($this->items as $i => $item) {
            $name = $item->product ? $item->product->name : $item->temp_product_name;
            $lines[] = ($i + 1) . ". *{$name}* — Qty: {$item->quantity}";
        }
        $lines[] = "";
        $lines[] = "Mohon konfirmasi ketersediaan dan harga. Terima kasih!";
        return implode("\n", $lines);
    }

    public static function generateCode(): string
    {
        $today = now()->format('Ymd');
        $prefix = 'PPR' . $today;
        $last = static::where('product_purchase_code', 'like', $prefix . '%')
            ->orderBy('product_purchase_code', 'desc')
            ->first();
        $number = $last ? (int) substr($last->product_purchase_code, -4) + 1 : 1;
        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
