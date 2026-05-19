<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

// Notification interne envoyée lorsque l'intervention est bloquée par l'absence d'une pièce en stock (UC06).
class PieceManquanteNotification extends Notification
{
    use Queueable;

    protected $dossier;

    // Initialise une nouvelle instance de la notification.
    public function __construct($dossier)
    {
        $this->dossier = $dossier;
    }

    // Définit le canal de notification (Base de données locale pour la cloche d'administration).
    public function via($notifiable)
    {
        return ['database'];
    }

    // Formate les données pour l'enregistrement et l'affichage dans la cloche locale.
    public function toArray($notifiable)
    {
        return [
            'title' => 'Pièce Manquante',
            'message' => 'Le dossier #' . $this->dossier->num_dossier . ' est bloqué : attente d\'approvisionnement de pièce.',
            'dossier_id' => $this->dossier->id,
            'url' => route('dossiers.show', $this->dossier->id),
        ];
    }
}