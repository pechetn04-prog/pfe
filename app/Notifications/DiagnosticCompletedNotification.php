<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class DiagnosticCompletedNotification extends Notification
{
    protected $ticket;
    protected $technicien;

    public function __construct($ticket, $technicien)
    {
        $this->ticket = $ticket;
        $this->technicien = $technicien;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $sousGarantie = $this->ticket->sous_garantie ? 'sous garantie' : 'hors garantie';

        return [
            'message' => 'Le technicien ' . $this->technicien->name . ' a terminé le diagnostic du ticket #' . $this->ticket->id . ' (' . $sousGarantie . ')',
            'url' => '/tickets/' . $this->ticket->id,
            'dossier_id' => $this->ticket->id
        ];
    }
}
