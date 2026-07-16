<?php

namespace App\Models;

use App\Models\Traits\BelongsToStore;
use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    use BelongsToStore;

    protected $fillable = [
        'store_id',
        'qris_mode',
        'manual_qris_image',
        'qris_provider',
        'merchant_id',
        'client_key',
        'server_key',
        'callback_url',
        'is_qris_active',
        'bank_name',
        'bank_account_number',
        'bank_account_name',
    ];

    protected $casts = [
        'is_qris_active' => 'boolean',
    ];

    /**
     * Get or create the PaymentSetting for the currently resolved store.
     */
    public static function getSettings(): self
    {
        $storeId = app()->bound('current_store') ? app('current_store')->id : null;

        return self::firstOrCreate(
            ['store_id' => $storeId],
            ['qris_mode' => 'manual', 'is_qris_active' => true]
        );
    }
}
