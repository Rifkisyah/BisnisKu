<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    protected $fillable = [
        'qris_mode',
        'manual_qris_image',
        'qris_provider',
        'merchant_id',
        'client_key',
        'server_key',
        'callback_url',
        'is_qris_active'
    ];

    public static function getSettings()
    {
        return self::firstOrCreate([], [
            'qris_mode' => 'manual',
            'is_qris_active' => true
        ]);
    }
}
