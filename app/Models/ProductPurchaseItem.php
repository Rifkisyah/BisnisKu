<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPurchaseItem extends Model
{
    protected $table = 'product_purchase_items';

    protected $fillable = [
        'product_purchase_code', 'product_code', 'temp_product_name',
        'is_resolved', 'resolved_product_code',
        'quantity', 'quantity_received', 'quantity_rejected', 'rejection_notes',
        'purchase_price', 'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'purchase_price'    => 'decimal:2',
            'subtotal'          => 'decimal:2',
            'is_resolved'       => 'boolean',
            'quantity_received' => 'integer',
            'quantity_rejected' => 'integer',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────────

    public function productPurchase(): BelongsTo
    {
        return $this->belongsTo(ProductPurchase::class, 'product_purchase_code', 'product_purchase_code');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_code', 'product_code');
    }

    public function resolvedProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'resolved_product_code', 'product_code');
    }

    // ─── Computed attributes ────────────────────────────────────────────────

    /**
     * Get display name: resolved product > product > temp_product_name
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->resolvedProduct) return $this->resolvedProduct->name;
        if ($this->product) return $this->product->name;
        return $this->temp_product_name ?? '(Tidak diketahui)';
    }

    /**
     * Get the effective product code for stock operations.
     */
    public function getEffectiveProductCodeAttribute(): ?string
    {
        return $this->resolved_product_code ?? $this->product_code;
    }

    /**
     * Qty that should affect stock. Use quantity_received when set (partial), else quantity.
     */
    public function getStockableQtyAttribute(): int
    {
        return $this->quantity_received > 0 ? $this->quantity_received : $this->quantity;
    }
}
