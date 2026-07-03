<?php

namespace App\Models;

use App\Models\Traits\BelongsToStore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Debt extends Model
{
    use BelongsToStore;

    protected $primaryKey = 'debt_code';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'debt_code', 'store_id', 'debtor_name', 'debtor_contact',
        'debtor_address', 'total_amount', 'paid_amount', 'remaining_amount',
        'debt_date', 'due_date', 'status', 'notes', 'transaction_code',
    ];

    protected function casts(): array
    {
        return [
            'debt_date' => 'date',
            'due_date' => 'date',
            'total_amount' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
        ];
    }

    public function payments(): HasMany
    {
        return $this->hasMany(DebtPayment::class, 'debt_code', 'debt_code');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'transaction_code', 'transaction_code');
    }

    public function updateStatus(): void
    {
        $this->paid_amount = $this->payments()->sum('amount');
        $this->remaining_amount = $this->total_amount - $this->paid_amount;

        if ($this->remaining_amount <= 0) {
            $this->remaining_amount = 0;
            $this->status = 'paid';
        } elseif ($this->paid_amount > 0) {
            $this->status = 'partial';
        } else {
            $this->status = 'unpaid';
        }
        $this->save();
    }

    public function isOverdue(): bool
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'paid';
    }

    public static function generateCode(): string
    {
        $prefix = 'DBT';
        $last = static::withoutGlobalScope('store')->where('debt_code', 'like', $prefix . '%')
            ->orderBy('debt_code', 'desc')
            ->first();
        $number = $last ? (int) substr($last->debt_code, 3) + 1 : 1;
        return $prefix . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}
