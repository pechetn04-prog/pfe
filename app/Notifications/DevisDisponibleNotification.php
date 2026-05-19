<?php

namespace App\Notifications;

use App\Models\Dossier;
use App\Models\Devis;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// Notification informant le client qu'un devis a été émis pour son dossier (UC04 / UC07).
class DevisDisponibleNotification extends Notification
{
    use Queueable;

    protected $dossier;
    protected $devis;

    // Initialise une nouvelle instance de la notification.
    public function __construct(Dossier $dossier, Devis $devis)
    {
        $this->dossier = $dossier;
        $this->devis = $devis;
    }

    // Définit les canaux de diffusion (Email pour le client, Database pour la cloche).
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    // Génère le message d'e-mail destiné au client.
    public function toMail($notifiable)
    {
        $company = \App\Models\ParametreSociete::first();
        $devise = $company ? $company->devise : 'DT';

        return (new MailMessage)
            ->subject('Votre devis est disponible - Dossier #' . $this->dossier->num_dossier)
            ->greeting('Bonjour ' . $this->dossier->client->name . ',')
            ->line('Le diagnostic de votre appareil (' . $this->dossier->appareil->modele . ') est terminé.')
            ->line('Un devis a été établi pour un montant total de ' . number_format($this->devis->montant_total, 2) . ' ' . $devise . '.')
            ->action('Consulter le devis', route('client.suivi.public', $this->dossier->id))
            ->line('Vous pouvez accepter ou refuser ce devis directement en ligne.')
            ->line('Merci de votre confiance !');
    }

    // Formate les données pour la cloche d'alertes locale de l'espace client.
    public function toArray($notifiable)
    {
        return [
            'dossier_id' => $this->dossier->id,
            'num_dossier' => $this->dossier->num_dossier,
            'devis_id' => $this->devis->id,
            'montant' => $this->devis->montant_total,
            'message' => 'Le devis pour le dossier #' . $this->dossier->num_dossier . ' est disponible.',
            'url' => route('client.ticket', $this->dossier->id),
        ];
    }
}
