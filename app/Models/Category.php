<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $primaryKey = 'category_code';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = ['category_code', 'name', 'slug', 'description', 'type', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_code', 'category_code');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function generateCode(): string
    {
        $prefix = 'CAT';
        $last = static::where('category_code', 'like', $prefix . '%')
            ->orderBy('category_code', 'desc')
            ->first();
        $number = $last ? (int) substr($last->category_code, strlen($prefix)) + 1 : 1;
        return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
    }
}
