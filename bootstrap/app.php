<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Named middleware aliases
        $middleware->alias([
            'role'              => \App\Http\Middleware\RoleMiddleware::class,
            'set.tenant'        => \App\Http\Middleware\SetTenant::class,
            'set.public.tenant' => \App\Http\Middleware\SetPublicStoreTenant::class,
            'locale'            => \App\Http\Middleware\SetLocale::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

