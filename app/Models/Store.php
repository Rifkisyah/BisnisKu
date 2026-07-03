<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Store extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'owner_id',
        'address',
        'phone',
        'email',
        'logo',
        'description',
        'is_active',
        'catalog_enabled',
    ];

    protected function casts(): array
    {
        return [
            'is_active'       => 'boolean',
            'catalog_enabled' => 'boolean',
        ];
    }

    // ─── Relationships ──────────────────────────────────────────────────

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function categories(): HasMany
    {
        return $this->hasMany(Category::class);
    }

    public function suppliers(): HasMany
    {
        return $this->hasMany(Supplier::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function serviceRepairs(): HasMany
    {
        return $this->hasMany(ServiceRepair::class);
    }

    public function debts(): HasMany
    {
        return $this->hasMany(Debt::class);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────

    /**
     * Generate a unique slug from the store name.
     */
    public static function generateSlug(string $name): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $original . '-' . $count++;
        }

        return $slug;
    }

    /**
     * Get the public catalog URL for this store.
     */
    public function catalogUrl(): string
    {
        return route('catalog.store', ['store' => $this->slug]);
    }

    /**
     * Get a setting value for this store.
     */
    public function getSetting(string $key, mixed $default = null): mixed
    {
        $setting = Setting::where('store_id', $this->id)
            ->where('key', $key)
            ->first();

        return $setting ? $setting->value : $default;
    }

    /**
     * Set a setting value for this store.
     */
    public function setSetting(string $key, mixed $value): void
    {
        Setting::updateOrCreate(
            ['store_id' => $this->id, 'key' => $key],
            ['value' => $value]
        );
    }
}
