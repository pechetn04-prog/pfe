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

    public function toArray($notifiable)
    {
        return [
            'title' => 'Pièce Manquante',
            'message' => 'Le dossier #' . $this->dossier->num_dossier . ' est en attente de pièce.',
            'dossier_id' => $this->dossier->id,
            'url' => route('dossiers.show', $this->dossier->id),
        ];
    }
}