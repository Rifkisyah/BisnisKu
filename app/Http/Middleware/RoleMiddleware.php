<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Admin has super access — bypasses all role checks
        if (auth()->user()->isAdmin()) {
            return $next($request);
        }

        if (!auth()->user()->hasRole(...$roles)) {
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
