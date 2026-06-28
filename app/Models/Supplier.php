<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $primaryKey = 'supplier_code';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['supplier_code', 'name', 'phone_prefix', 'phone_number', 'email', 'address', 'notes', 'is_active', 'image'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function getWhatsappNumberAttribute(): ?string
    {
        if ($this->phone_prefix && $this->phone_number) {
            return $this->phone_prefix . ltrim($this->phone_number, '0');
        }
        return null;
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'supplier_code', 'supplier_code');
    }

    public function productPurchases(): HasMany
    {
        return $this->hasMany(ProductPurchase::class, 'supplier_code', 'supplier_code');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function generateCode(): string
    {
        $prefix = 'SUP';
        $last = static::where('supplier_code', 'like', $prefix . '%')
            ->orderBy('supplier_code', 'desc')
            ->first();
        $number = $last ? (int) substr($last->supplier_code, strlen($prefix)) + 1 : 1;
        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
