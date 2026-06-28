<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAnalytic extends Model
{
    protected $table = 'product_analytics';

    protected $fillable = [
        'product_code', 'total_qty_sold', 'transaction_frequency',
        'remaining_stock', 'cluster_label', 'sma_value',
        'predicted_demand', 'restock_recommendation', 'analysis_date',
    ];

    protected function casts(): array
    {
        return [
            'analysis_date' => 'date',
            'sma_value' => 'decimal:2',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_code', 'product_code');
    }
}
