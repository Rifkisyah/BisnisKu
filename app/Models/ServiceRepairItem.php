<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceRepairItem extends Model
{
    protected $fillable = [
        'parent_id', 'repair_code', 'component_code', 'name', 'brand', 'series',
        'complaint', 'diagnosis_result', 'images', 'quantity',
        'service_fee', 'subtotal',
        // Sparepart fields
        'sparepart_type',   // null = device item, 'from_stock', 'requested'
        'sparepart_status', // 'pending', 'available', 'used'
        'temp_purchase_price',
    ];

    protected function casts(): array
    {
        return [
            'images'               => 'array',
            'service_fee'          => 'decimal:2',
            'subtotal'             => 'decimal:2',
            'temp_purchase_price'  => 'decimal:2',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────────

    public function serviceRepair(): BelongsTo
    {
        return $this->belongsTo(ServiceRepair::class, 'repair_code', 'repair_code');
    }

    public function component(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'component_code', 'product_code');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ServiceRepairItem::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ServiceRepairItem::class, 'parent_id');
    }

    /** Pengadaan yang dibuat dari item sparepart ini */
    public function productPurchases(): HasMany
    {
        return $this->hasMany(ProductPurchase::class, 'repair_item_id');
    }

    // ─── Status helpers ─────────────────────────────────────────────────────

    /** True if this row represents a device (not a sparepart child) */
    public function isDevice(): bool       { return is_null($this->parent_id); }

    /** True if this is a sparepart taken from existing stock */
    public function isFromStock(): bool    { return $this->sparepart_type === 'from_stock'; }

    /** True if this is a requested (to-be-procured) sparepart */
    public function isRequested(): bool    { return $this->sparepart_type === 'requested'; }

    public function isPending(): bool      { return $this->sparepart_status === 'pending'; }
    public function isAvailable(): bool    { return $this->sparepart_status === 'available'; }
    public function isUsed(): bool         { return $this->sparepart_status === 'used'; }
}
