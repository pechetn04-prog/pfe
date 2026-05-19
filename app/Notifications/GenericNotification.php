<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

// Notification utilitaire générique pour pousser rapidement des alertes internes en base locale.
class GenericNotification extends Notification
{
    private $title;
    private $message;
    private $url;

    // Initialise une nouvelle instance de la notification.
    public function __construct($title, $message, $url = '#')
    {
        $this->title = $title;
        $this->message = $message;
        $this->url = $url;
    }

    // Définit le canal de notification à exploiter (Base de données locale uniquement).
    public function via($notifiable)
    {
        return ['database'];
    }

    // Formate les données pour la cloche d'alertes locale.
    public function toArray($notifiable)
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
        ];
    }
}
