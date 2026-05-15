<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class StockReapprovisionneNotification extends Notification
{
    protected $ticket;
    protected $piece;

    /**
     * @param $ticket  Le ticket en attente de pièce
     * @param $piece   La pièce qui a été réapprovisionnée
     */
    public function __construct($ticket, $piece)
    {
        $this->ticket = $ticket;
        $this->piece = $piece;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'message' => '✅ La pièce « ' . $this->piece->nom . ' » (Réf: ' . $this->piece->reference . ') a été réapprovisionnée. '
                . 'Vous pouvez reprendre l\'intervention du ticket #' . $this->ticket->num_dossier . '.',
            'url' => '/technicien/tickets/' . $this->ticket->id . '/intervention',
            'dossier_id' => $this->ticket->id,
            'type' => 'stock_reapprovisionne',
        ];
    }
}
