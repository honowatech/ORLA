<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Invitation à rejoindre l'espace d'une entreprise : le lien permet de
 * choisir son mot de passe (et vaut vérification de l'email).
 */
class InvitationUtilisateur extends Notification
{
    use Queueable;

    public function __construct(public string $token)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $entreprise = $notifiable->entreprise?->name ?? config('app.name');
        $url = url(route('password.reset', ['token' => $this->token, 'email' => $notifiable->getEmailForPasswordReset()], false));

        return (new MailMessage)
            ->subject("Invitation à rejoindre $entreprise")
            ->greeting('Bonjour '.$notifiable->noms.',')
            ->line("Un compte a été créé pour vous dans l'espace de $entreprise sur ".config('app.name').'.')
            ->action('Choisir mon mot de passe', $url)
            ->line('Ce lien expire dans '.config('auth.passwords.'.config('auth.defaults.passwords').'.expire').' minutes. Passé ce délai, utilisez « Mot de passe oublié » sur la page de connexion.');
    }
}
