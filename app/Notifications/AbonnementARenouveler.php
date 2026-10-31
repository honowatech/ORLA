<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

/**
 * Relance du propriétaire d'une entreprise : fin d'essai, abonnement bientôt
 * échu, ou accès passé en lecture seule.
 */
class AbonnementARenouveler extends Notification
{
    use Queueable;

    /** @param  'essai'|'abonnement'|'expire'  $motif */
    public function __construct(public string $motif, public Carbon $echeance, public int $jours)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $entreprise = $notifiable->entreprise?->name ?? config('app.name');
        $date = $this->echeance->format('d/m/Y');
        $quand = $this->jours <= 0 ? "aujourd'hui" : ($this->jours === 1 ? 'demain' : "dans {$this->jours} jours");

        [$sujet, $texte] = match ($this->motif) {
            'essai' => ["Votre essai gratuit se termine $quand", "L'essai gratuit de $entreprise se termine le $date. Choisissez une offre pour continuer à travailler sans interruption."],
            'abonnement' => ["Votre abonnement arrive à échéance $quand", "L'abonnement de $entreprise arrive à échéance le $date. Renouvelez-le pour continuer à enregistrer vos commandes."],
            default => ["Votre espace est passé en lecture seule", "L'accès de $entreprise a expiré le $date. Vos données sont conservées : renouvelez votre abonnement pour les modifier à nouveau."],
        };

        return (new MailMessage)
            ->subject($sujet)
            ->greeting('Bonjour '.$notifiable->noms.',')
            ->line($texte)
            ->action('Gérer mon abonnement', route('mon_abonnement'));
    }
}
