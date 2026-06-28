<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    protected $primaryKey = 'transaction_code';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'transaction_code', 'cashier_id', 'transaction_date', 'subtotal',
        'discount', 'total', 'payment_method', 'amount_paid',
        'change_amount', 'status', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'transaction_date' => 'datetime',
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'change_amount' => 'decimal:2',
        ];
    }

    /**
     * Alias for cashier (for compatibility with older controller code).
     */
    public function user(): BelongsTo
    {
        return $this->cashier();
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class, 'transaction_code', 'transaction_code');
    }

    public function debt(): HasOne
    {
        return $this->hasOne(Debt::class, 'transaction_code', 'transaction_code');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'transaction_code', 'transaction_code');
    }

    // ─── Scopes ────────────────────────────────────────────────────────────

    public function scopeCompleted($query)
    {
        // For backwards compatibility, completed means paid
        return $query->where($this->getTable() . '.status', 'paid');
    }

    public function scopeInPeriod($query, $start, $end)
    {
        return $query->whereBetween('transaction_date', [$start, $end]);
    }

    public static function generateCode(): string
    {
        $today = now()->format('Ymd');
        $prefix = 'TRX' . $today;
        $last = static::where('transaction_code', 'like', $prefix . '%')
            ->orderBy('transaction_code', 'desc')
            ->first();
        $number = $last ? (int) substr($last->transaction_code, -4) + 1 : 1;
        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
