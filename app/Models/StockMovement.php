<?php

namespace App\Models;

use App\Models\Traits\BelongsToStore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use BelongsToStore;

    protected $fillable = [
        'product_code', 'store_id', 'created_by', 'type', 'total_stock',
        'previous_stock', 'current_stock', 'movement_date',
        'reference_type', 'reference_code', 'notes',
    ];

    protected function casts(): array
    {
        return ['movement_date' => 'datetime'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_code', 'product_code');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─── Scopes ────────────────────────────────────────────────────────────

    public function scopeInPeriod($query, $start, $end)
    {
        return $query->whereBetween('movement_date', [$start, $end]);
    }
}
