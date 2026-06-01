<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

// Notification simple et directe pour notifier les utilisateurs internes ou le client
class SimpleNotification extends Notification
{
    private $message;
    private $ticket;

    // Initialise une nouvelle instance de la notification.
    public function __construct($message, $ticket)
    {
        $this->message = $message;
        $this->ticket = $ticket;
    }

    // Définit le canal de notification (Base de données locale).
    public function via($notifiable)
    {
        return ['database'];
    }

    // Formate les données pour la cloche d'alertes locale.
    public function toArray($notifiable)
    {
        return [
            'message' => $this->message,
            'url' => route('dossiers.show', $this->ticket->id),
            'dossier_id' => $this->ticket->id
        ];
    }
}
