<?php

namespace App\Models;

use App\Models\Traits\BelongsToStore;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceRepair extends Model
{
    use BelongsToStore;

    protected $primaryKey = 'repair_code';
    protected $keyType = 'string';
    public $incrementing = false;

    // Status constants
    const STATUS_DRAFT          = 'draft';
    const STATUS_WAITING_DP     = 'waiting_dp';
    const STATUS_DIAGNOSING     = 'diagnosing';
    const STATUS_WAITING_PARTS  = 'waiting_parts';
    const STATUS_REPAIRING      = 'repairing';
    const STATUS_READY          = 'ready';
    const STATUS_DONE           = 'done';
    const STATUS_CANCELLED      = 'cancelled';

    protected $fillable = [
        'repair_code', 'store_id', 'technician_id', 'customer_name', 'customer_phone',
        'service_fee', 'component_cost', 'total_cost', 'payment_method',
        'down_payment', 'status', 'start_date', 'completion_date', 'notes', 'images',
    ];

    protected function casts(): array
    {
        return [
            'start_date'      => 'datetime',
            'completion_date' => 'datetime',
            'service_fee'     => 'decimal:2',
            'component_cost'  => 'decimal:2',
            'total_cost'      => 'decimal:2',
            'down_payment'    => 'decimal:2',
            'images'          => 'array',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────────

    public function technician(): BelongsTo
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ServiceRepairItem::class, 'repair_code', 'repair_code');
    }

    /** Only top-level device items (no parent) */
    public function deviceItems(): HasMany
    {
        return $this->hasMany(ServiceRepairItem::class, 'repair_code', 'repair_code')
            ->whereNull('parent_id');
    }

    // ─── Status helpers ─────────────────────────────────────────────────────

    public function isDraft(): bool         { return $this->status === self::STATUS_DRAFT; }
    public function isWaitingDp(): bool     { return $this->status === self::STATUS_WAITING_DP; }
    public function isDiagnosing(): bool    { return $this->status === self::STATUS_DIAGNOSING; }
    public function isWaitingParts(): bool  { return $this->status === self::STATUS_WAITING_PARTS; }
    public function isRepairing(): bool     { return $this->status === self::STATUS_REPAIRING; }
    public function isReady(): bool         { return $this->status === self::STATUS_READY; }
    public function isDone(): bool          { return $this->status === self::STATUS_DONE; }
    public function isCancelled(): bool     { return $this->status === self::STATUS_CANCELLED; }

    public function isFinal(): bool         { return $this->isDone() || $this->isCancelled(); }

    public function isTechEditable(): bool
    {
        return in_array($this->status, [
            self::STATUS_DIAGNOSING,
            self::STATUS_WAITING_PARTS,
        ]);
    }

    // ─── Business logic ─────────────────────────────────────────────────────

    /**
     * Check if DP >= 50% of total cost.
     */
    public function isDpSufficient(): bool
    {
        if ($this->total_cost <= 0) return true;
        return $this->down_payment >= ($this->total_cost * 0.5);
    }

    /**
     * Remaining amount after DP.
     */
    public function getRemainingAmountAttribute(): float
    {
        return max(0, $this->total_cost - $this->down_payment);
    }

    /**
     * Check if all requested spareparts in this ticket are now available.
     */
    public function allPartsAvailable(): bool
    {
        $pendingCount = ServiceRepairItem::where('repair_code', $this->repair_code)
            ->whereNotNull('parent_id')
            ->where('sparepart_type', 'requested')
            ->where('sparepart_status', 'pending')
            ->count();

        return $pendingCount === 0;
    }

    /**
     * Check if all device items have diagnosis filled.
     */
    public function isDiagnosisFilled(): bool
    {
        return $this->deviceItems()->where(function($q) {
            $q->whereNull('diagnosis_result')->orWhere('diagnosis_result', '');
        })->count() === 0;
    }

    /**
     * Recalculate total cost from all items.
     * component_cost = sum of item subtotals (spare parts child rows)
     * service_fee    = sum of item service_fees (device parent rows)
     * total_cost     = component_cost + service_fee
     */
    public function calculateTotalCost(): void
    {
        $this->component_cost = ServiceRepairItem::where('repair_code', $this->repair_code)
            ->whereNotNull('parent_id')
            ->sum('subtotal');

        $this->service_fee = ServiceRepairItem::where('repair_code', $this->repair_code)
            ->whereNull('parent_id')
            ->sum('service_fee');

        $this->total_cost = $this->service_fee + $this->component_cost;
        $this->save();
    }

    /**
     * Build a WhatsApp URL with the "ready for pickup" message template.
     */
    public function buildWhatsAppUrl(string $shopName = 'Toko Kami'): string
    {
        $deviceNames = $this->deviceItems()->pluck('name')->implode(', ');
        $remaining = number_format($this->remaining_amount, 0, ',', '.');
        $total = number_format($this->total_cost, 0, ',', '.');
        $dp = number_format($this->down_payment, 0, ',', '.');

        $message = "Halo *{$this->customer_name}*, servis *{$deviceNames}* Anda di {$shopName} sudah selesai dan siap diambil. 🎉\n\n";
        $message .= "Total biaya: Rp {$total}\n";
        $message .= "DP sudah: Rp {$dp}\n";
        $message .= "Sisa: Rp {$remaining}\n\n";
        $message .= "Silakan hubungi kami untuk konfirmasi pengambilan. Terima kasih! 🙏";

        $phone = preg_replace('/\D/', '', $this->customer_phone ?? '');
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        return 'https://wa.me/' . $phone . '?text=' . urlencode($message);
    }

    // ─── Scopes ────────────────────────────────────────────────────────────

    public function scopeCompletedInPeriod($query, $start, $end)
    {
        return $query
            ->whereIn('status', [self::STATUS_DONE, 'picked_up'])
            ->whereBetween('start_date', [$start, $end]);
    }

    // ─── Code generator ─────────────────────────────────────────────────────

    public static function generateCode(): string
    {
        $today = now()->format('Ymd');
        $prefix = 'SRV' . $today;
        $last = static::withoutGlobalScope('store')->where('repair_code', 'like', $prefix . '%')
            ->orderBy('repair_code', 'desc')
            ->first();
        $number = $last ? (int) substr($last->repair_code, -4) + 1 : 1;
        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
