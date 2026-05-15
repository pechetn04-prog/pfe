<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class TicketAssignedNotification extends Notification
{
    protected $ticket;
    protected $agent;

    public function __construct($ticket, $agent)
    {
        $this->ticket = $ticket;
        $this->agent = $agent;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Agent ' . $this->agent->name . ' vous a assigné le ticket #' . $this->ticket->id,
            'url' => '/tickets/' . $this->ticket->id,
            'dossier_id' => $this->ticket->id
        ];
    }
}