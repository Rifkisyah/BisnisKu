<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'payment_code',
        'transaction_code',
        'payment_method',
        'qris_mode',
        'provider',
        'amount',
        'status',
        'external_order_id',
        'reference_number',
        'proof_image',
        'callback_payload',
        'paid_at',
        'confirmed_by'
    ];

    protected $casts = [
        'callback_payload' => 'array',
        'paid_at' => 'datetime',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_code', 'transaction_code');
    }

    public function confirmer()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }

    public static function generateCode()
    {
        $prefix = 'PAY-' . date('Ymd');
        $lastPayment = self::where('payment_code', 'like', $prefix . '%')->orderBy('payment_code', 'desc')->first();
        if ($lastPayment) {
            $number = intval(substr($lastPayment->payment_code, -4)) + 1;
        } else {
            $number = 1;
        }
        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
