<?php

use App\Http\Middleware\Check_Sa_Client_Error;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // La pile globale par défaut de Laravel 12 (TrustProxies, HandleCors,
        // PreventRequestsDuringMaintenance, ValidatePostSize, TrimStrings,
        // ConvertEmptyStringsToNull) couvre déjà les besoins de l'application.
        $middleware->alias([
            'Check_Sa_Client_Error' => Check_Sa_Client_Error::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->dontFlash([
            'current_password',
            'password',
            'password_confirmation',
        ]);
    })
    ->create();
