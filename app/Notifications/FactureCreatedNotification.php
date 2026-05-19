<?php

namespace App\Notifications;

use App\Models\Facture;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

// Notification informant le client qu'une facture de règlement a été émise pour son dossier (UC08 / UC10).
class FactureCreatedNotification extends Notification
{
    use Queueable;

    protected $facture;

    // Initialise une nouvelle instance de la notification.
    public function __construct(Facture $facture)
    {
        $this->facture = $facture;
    }

    // Définit les canaux de transmission de l'alerte (Mail + Cloche de notifications).
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    // Structure le courriel récapitulatif de facturation expédié au client.
    public function toMail($notifiable)
    {
        $company = \App\Models\ParametreSociete::first();
        $devise = $company ? $company->devise : 'DT';

        return (new MailMessage)
            ->subject('Votre facture est disponible - ' . $this->facture->numero)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('La réparation de votre appareil est terminée et votre facture a été générée.')
            ->line('Numéro de facture : **' . $this->facture->numero . '**')
            ->line('Montant Total : **' . number_format($this->facture->montant_total, 2) . ' ' . $devise . '**')
            ->action('Voir la facture', route('client.ticket', $this->facture->dossier_id))
            ->line('Vous pouvez récupérer votre appareil muni de cette facture.')
            ->line('Merci de votre confiance !');
    }

    // Stocke les métadonnées de facturation dans la base locale.
    public function toArray($notifiable)
    {
        return [
            'title' => 'Nouvelle Facture',
            'message' => 'Nouvelle facture générée : ' . $this->facture->numero,
            'facture_id' => $this->facture->id,
            'dossier_id' => $this->facture->dossier_id,
            'numero' => $this->facture->numero,
            'url' => route('factures.show', $this->facture->id),
        ];
    }
}
