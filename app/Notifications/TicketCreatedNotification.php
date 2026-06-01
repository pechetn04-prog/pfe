<?php

namespace App\Notifications;

use App\Models\Dossier;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// Notification de confirmation de réception d'un appareil et transmission des accès de compte client.
class TicketCreatedNotification extends Notification
{
    use Queueable;

    protected $dossier;
    protected $defaultPassword;

    // Initialise une nouvelle instance de la notification.
    public function __construct(Dossier $dossier, ?string $defaultPassword = null)
    {
        $this->dossier = $dossier;
        $this->defaultPassword = $defaultPassword;
    }

    // Définit les canaux de notification (Mail pour le client, Database pour la cloche).
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    // Génère le message d'email avec identifiants pour le client.
    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject('Confirmation de réception de votre appareil - #' . $this->dossier->num_dossier)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('Nous vous confirmons la réception de votre appareil **' . $this->dossier->appareil->modele . '** dans notre centre SAV.')
            ->line('Votre numéro de dossier est : **' . $this->dossier->num_dossier . '**')
            ->line('Panne déclarée : ' . ($this->dossier->panne_declaree ?: 'Non spécifiée'))
            ->action('Suivre mon dossier', route('client.suivi.public', $this->dossier->id));

        // Afficher les identifiants uniquement pour les nouveaux comptes
        if ($this->defaultPassword) {
            $mail->line('---')
                 ->line('**Votre compte client a été créé automatiquement.**')
                 ->line('Email : **' . $notifiable->email . '**')
                 ->line('Mot de passe par défaut : **' . $this->defaultPassword . '**')
                 ->line('Nous vous recommandons de changer ce mot de passe après votre première connexion.');
        }

        return $mail->line('Merci de votre confiance !');
    }

    // Formate les données pour la cloche d'alertes locale de l'espace client.
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
