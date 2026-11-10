<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['store_id', 'key', 'value'];

    /**
     * Get the current store_id from the service container, or null if not in a tenant context.
     */
    private static function currentStoreId(): ?int
    {
        return app()->bound('current_store') ? app('current_store')->id : null;
    }

    /**
     * Get a setting value for the current store.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $setting = self::where('store_id', self::currentStoreId())
            ->where('key', $key)
            ->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value for the current store.
     */
    public static function set(string $key, mixed $value): self
    {
        return self::updateOrCreate(
            ['store_id' => self::currentStoreId(), 'key' => $key],
            ['value' => $value]
        );
    }
}

