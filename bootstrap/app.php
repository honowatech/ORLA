<?php

use App\Http\Middleware\BypassTenancy;
use App\Http\Middleware\EnsureEntrepriseActive;
use App\Http\Middleware\EnsureUserIsActive;
use App\Http\Middleware\SetCurrentEntreprise;
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
            'entreprise' => SetCurrentEntreprise::class,
            'actif' => EnsureUserIsActive::class,
            'entreprise.active' => EnsureEntrepriseActive::class,
            'tenancy.bypass' => BypassTenancy::class,
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
