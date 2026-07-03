<?php

namespace App\Http\Middleware;

use App\Models\Store;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SetTenant Middleware — for POS internal routes (authenticated users).
 *
 * Resolves the current store from the authenticated user's store_id
 * and binds it into the service container as 'current_store'.
 * All models using BelongsToStore trait will automatically scope
 * their queries to this store.
 */
class SetTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->store_id) {
            $store = Store::find($user->store_id);

            if ($store && $store->is_active) {
                app()->instance('current_store', $store);
            }
        }

        return $next($request);
    }
}
