<?php

namespace App\Models\Traits;

use App\Models\Store;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * BelongsToStore Trait
 *
 * Applies a global scope that automatically filters all queries
 * to the currently resolved tenant store. Also auto-fills store_id
 * when creating new records.
 *
 * Usage: add `use BelongsToStore;` to any Model that has a store_id column.
 *
 * The current store is resolved from the service container key 'current_store',
 * which is set by SetTenant middleware (POS) or SetPublicStoreTenant (catalog).
 * When no store is in context (e.g. during seeding), no scope is applied.
 */
trait BelongsToStore
{
    public static function bootBelongsToStore(): void
    {
        // Auto-fill store_id on create
        static::creating(function (Model $model) {
            if (! $model->store_id) {
                $store = app()->bound('current_store') ? app('current_store') : null;
                if ($store) {
                    $model->store_id = $store->id;
                }
            }
        });

        // Apply global scope to all queries
        static::addGlobalScope('store', function (Builder $builder) {
            if (app()->bound('current_store') && app('current_store') !== null) {
                $builder->where($builder->getModel()->getTable() . '.store_id', app('current_store')->id);
            }
        });
    }

    /**
     * Relationship to the owning store.
     */
    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Scope to disable the store filter (useful for admin/global queries).
     */
    public function scopeWithoutStoreScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope('store');
    }
}
