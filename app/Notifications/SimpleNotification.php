<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class SimpleNotification extends Notification
{
    private $message;
    private $ticket;

    public function __construct($message, $ticket)
    {
        $this->message = $message;
        $this->ticket = $ticket;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->message,
            'url' => route('dossiers.show', $this->ticket->id),
            'dossier_id' => $this->ticket->id
        ];
    }
}
