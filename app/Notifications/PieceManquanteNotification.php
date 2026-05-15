<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PieceManquanteNotification extends Notification
{
    use Queueable;

    protected $dossier;

    public function __construct($dossier)
    {
        $this->dossier = $dossier;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => 'Dossier #'.$this->dossier->id.' en attente de pièce',
            'dossier_id' => $this->dossier->id
        ];
    }
}