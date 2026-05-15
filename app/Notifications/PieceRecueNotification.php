<?php

namespace App\Notifications;

use App\Models\Dossier;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PieceRecueNotification extends Notification
{
    use Queueable;

    protected $dossier;

    public function __construct(Dossier $dossier)
    {
        $this->dossier = $dossier;
    }

    public function via($notifiable)
    {
        return ['database']; // On privilégie les notifications internes pour le technicien
    }

    public function toArray($notifiable)
    {
        return [
            'dossier_id' => $this->dossier->id,
            'title' => 'Pièce reçue : Dossier #' . $this->dossier->num_dossier,
            'message' => 'La pièce pour le dossier #' . $this->dossier->num_dossier . ' (' . ($this->dossier->appareil->modele ?? '—') . ') est arrivée. Vous pouvez reprendre la réparation.',
            'url' => route('dossiers.show', $this->dossier->id),
            'type' => 'success'
        ];
    }
}
