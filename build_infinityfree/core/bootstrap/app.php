<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        apiPrefix: '',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);

        // Named middleware aliases
        $middleware->alias([
            'role'              => \App\Http\Middleware\RoleMiddleware::class,
            'set.tenant'        => \App\Http\Middleware\SetTenant::class,
            'set.public.tenant' => \App\Http\Middleware\SetPublicStoreTenant::class,
            'locale'            => \App\Http\Middleware\SetLocale::class,
        ]);

        // Exclude webhook routes dari CSRF verification
        $middleware->validateCsrfTokens(except: [
            '/api/webhooks/midtrans',
            '/api/payments/qris/callback',
            'webhooks/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

