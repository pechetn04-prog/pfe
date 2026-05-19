<?php

namespace App\Notifications;

use App\Models\Dossier;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

// Notification envoyée à l'administrateur lorsqu'un technicien demande le retrait d'un dossier (UC05).
class DemandeRejetNotification extends Notification
{
    use Queueable;

    protected $dossier;
    protected $technicien;

    // Initialise la notification de demande de retrait.
    public function __construct(Dossier $dossier, User $technicien)
    {
        $this->dossier = $dossier;
        $this->technicien = $technicien;
    }

    // Définit le canal de diffusion de la notification (Base de données locale).
    public function via($notifiable)
    {
        return ['database'];
    }

    // Formate la notification pour l'affichage dans le panneau d'administration.
    public function toArray($notifiable)
    {
        return [
            'title' => 'Demande de retrait',
            'message' => "Le technicien {$this->technicien->name} demande le retrait du dossier #{$this->dossier->num_dossier}.",
            'dossier_id' => $this->dossier->id,
            'url' => route('admin.demandes_rejet.index'),
        ];
    }
}
