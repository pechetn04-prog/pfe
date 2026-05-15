<?php

namespace App\Notifications;

use App\Models\Dossier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $dossier;

    public function __construct(Dossier $dossier)
    {
        $this->dossier = $dossier;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Confirmation de réception de votre appareil - #' . $this->dossier->num_dossier)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Nous vous confirmons la réception de votre appareil ' . $this->dossier->appareil->modele . ' dans notre centre SAV.')
            ->line('Votre numéro de dossier est : **' . $this->dossier->num_dossier . '**')
            ->line('Panne déclarée : ' . ($this->dossier->panne_declaree ?: 'Non spécifiée'))
            ->action('Suivre mon dossier', route('client.suivi.public', $this->dossier->id))
            ->line('Pour accéder à votre espace client complet, utilisez votre email avec le mot de passe par défaut : **sav12345**')
            ->line('Merci de votre confiance !');
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Nouveau Ticket',
            'message' => 'Nouveau ticket créé : #' . $this->dossier->num_dossier,
            'dossier_id' => $this->dossier->id,
            'num_dossier' => $this->dossier->num_dossier,
            'url' => route('dossiers.show', $this->dossier->id),
        ];
    }
}
