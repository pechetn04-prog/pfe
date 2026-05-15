<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class TicketMessageNotification extends Notification
{
    protected $ticket;
    protected $user;

    public function __construct($ticket, $user)
    {
        $this->ticket = $ticket;
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => $this->user->name . ' a envoyé un message sur le ticket #' . $this->ticket->id,
            'url' => '/tickets/' . $this->ticket->id,
            'dossier_id' => $this->ticket->id
        ];
    }
}