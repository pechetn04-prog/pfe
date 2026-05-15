<?php

namespace App\Notifications;

use App\Models\Facture;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FactureCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $facture;

    public function __construct(Facture $facture)
    {
        $this->facture = $facture;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Votre facture est disponible - ' . $this->facture->numero)
            ->greeting('Bonjour ' . $notifiable->name . ',')
            ->line('La réparation de votre appareil est terminée et votre facture a été générée.')
            ->line('Numéro de facture : **' . $this->facture->numero . '**')
            ->line('Montant Total : **' . number_format($this->facture->montant_total, 2) . ' DA**')
            ->action('Voir la facture', route('client.ticket', $this->facture->dossier_id))
            ->line('Vous pouvez récupérer votre appareil muni de cette facture.')
            ->line('Merci de votre confiance !');
    }

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
