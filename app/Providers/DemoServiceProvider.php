<?php

namespace App\Providers;

use App\Demo\Demo;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class DemoServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Retient la base principale avant toute bascule vers un bac.
        Demo::mainConnection();

        // Entrée en démo : quelques bacs par adresse et par fenêtre.
        RateLimiter::for('demo', fn (Request $request) => Limit::perMinutes(
            config('demo.throttle.minutes'),
            config('demo.throttle.attempts'),
        )->by($request->ip()));

        // Aucun message sortant : tout e-mail envoyé depuis un bac, ou vers
        // une adresse réservée (.invalid), est annulé.
        Event::listen(MessageSending::class, function (MessageSending $event) {
            $recipients = array_map(fn ($a) => $a->getAddress(), array_merge(
                $event->message->getTo(), $event->message->getCc(), $event->message->getBcc(),
            ));
            $reserved = array_filter($recipients, fn ($a) => Demo::isReservedAddress($a));

            if (Demo::active() || $reserved !== []) {
                Log::info('demo.mail_withheld', ['in_sandbox' => Demo::active(), 'recipients' => count($recipients)]);

                return false;
            }

            return null;
        });
    }
}
