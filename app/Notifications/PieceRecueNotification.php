<?php

namespace App\Notifications;

use App\Models\Dossier;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

// Notification alertant le technicien assigné qu'une pièce manquante en attente est arrivée en stock (UC06).
class PieceRecueNotification extends Notification
{
    use Queueable;

    protected $dossier;

    // Initialise une nouvelle instance de la notification.
    public function __construct(Dossier $dossier)
    {
        $this->dossier = $dossier;
    }

    // Spécifie le canal d'envoi (Notification interne à destination du technicien).
    public function via($notifiable)
    {
        return ['database'];
    }

    // Formate l'alerte pour affichage direct dans le tableau de bord du technicien.
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
