<?php

namespace App\Models;

use App\Models\Traits\BelongsToStore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use BelongsToStore;

    protected $primaryKey = 'product_code';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'product_code', 'store_id', 'name', 'category_code', 'supplier_code',
        'purchase_price', 'selling_price', 'stock', 'minimum_stock',
        'unit', 'description', 'image', 'status', 'type',
    ];

    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'selling_price'  => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_code', 'category_code');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_code', 'supplier_code');
    }

    public function transactionItems(): HasMany
    {
        return $this->hasMany(TransactionItem::class, 'product_code', 'product_code');
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(ProductPurchaseItem::class, 'product_code', 'product_code');
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class, 'product_code', 'product_code');
    }

    public function analytics(): HasMany
    {
        return $this->hasMany(ProductAnalytic::class, 'product_code', 'product_code');
    }

    public function isLowStock(): bool
    {
        return $this->stock <= $this->minimum_stock;
    }

    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
    }

    public function isTemporary(): bool
    {
        return $this->status === 'temporary';
    }

    // ─── Scopes ────────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /** Active + temporary — used when searching for products to add to transactions */
    public function scopeAvailable($query)
    {
        return $query->whereIn('status', ['active', 'temporary']);
    }

    public function scopeTemporary($query)
    {
        return $query->where('status', 'temporary');
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock', '<=', 'minimum_stock');
    }

    public function scopePhysical($query)
    {
        return $query->where('type', 'physical');
    }

    public function scopeDigital($query)
    {
        return $query->where('type', 'digital');
    }

    public function scopeService($query)
    {
        return $query->where('type', 'service');
    }

    public function scopeSparepart($query)
    {
        return $query->where('type', 'sparepart');
    }

    // ─── Code generator ────────────────────────────────────────────────────

    public static function generateCode(string $type = 'physical'): string
    {
        $prefix = match ($type) {
            'digital'   => 'DIG',
            'service'   => 'SVC',
            'sparepart' => 'SPR',
            default     => 'PRD',
        };
        $last = static::withoutGlobalScope('store')
            ->where('product_code', 'like', $prefix . '%')
            ->orderBy('product_code', 'desc')
            ->first();
        $number = $last ? (int) substr($last->product_code, strlen($prefix)) + 1 : 1;
        return $prefix . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}
