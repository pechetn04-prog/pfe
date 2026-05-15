<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketNotification extends Notification
{
    private $ticket;
    private $user;

    public function __construct($ticket, $user)
    {
        $this->ticket = $ticket;
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['database']; // notification dashboard
    }

    public function toArray($notifiable)
    {
        return [
            'message' => $this->user->name . ' a créé le ticket #' . $this->ticket->id,
            'url' => '/tickets/' . $this->ticket->id,
            'dossier_id' => $this->ticket->id
        ];
    }
}