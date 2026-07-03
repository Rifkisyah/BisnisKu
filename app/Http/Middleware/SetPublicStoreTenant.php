<?php

namespace App\Http\Middleware;

use App\Models\Store;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SetPublicStoreTenant Middleware — for public catalog routes.
 *
 * Resolves the current store from the {store} route parameter (slug).
 * Sets the store in the service container so models can be read.
 * Aborts with 404 if the store does not exist, is inactive, or catalog is disabled.
 *
 * Note: This middleware does NOT apply BelongsToStore write scope —
 * catalog routes are read-only and controllers explicitly pass store context.
 */
class SetPublicStoreTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $slug = $request->route('store');

        if (! $slug) {
            abort(404);
        }

        $store = Store::where('slug', $slug)->first();

        if (! $store || ! $store->is_active || ! $store->catalog_enabled) {
            abort(404, 'Toko tidak ditemukan atau katalog tidak tersedia.');
        }

        // Bind the resolved store so BelongsToStore scopes work for read queries
        app()->instance('current_store', $store);

        // Also share with views
        view()->share('currentStore', $store);

        return $next($request);
    }
}
