<?php

namespace App\Notifications;

use App\Models\Dossier;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class DemandeRejetNotification extends Notification
{
    use Queueable;

    protected $dossier;
    protected $technicien;

    public function __construct(Dossier $dossier, User $technicien)
    {
        $this->dossier = $dossier;
        $this->technicien = $technicien;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

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
