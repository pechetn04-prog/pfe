<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;

class StockInsuffisantNotification extends Notification
{
    protected $ticket;
    protected $pieces;
    protected $technicien;

    /**
     * @param $ticket      Le ticket concerné
     * @param $pieces      Les pièces en rupture de stock [{nom, reference, stock_actuel, quantite_demandee}]
     * @param $technicien  Le technicien qui a tenté l'intervention
     */
    public function __construct($ticket, $pieces, $technicien)
    {
        $this->ticket = $ticket;
        $this->pieces = $pieces;
        $this->technicien = $technicien;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $piecesList = collect($this->pieces)->pluck('nom')->implode(', ');

        return [
            'message' => '⚠️ Rupture de stock — Le technicien ' . $this->technicien->name
                . ' ne peut pas finaliser l\'intervention du ticket #' . $this->ticket->num_dossier
                . '. Pièce(s) manquante(s) : ' . $piecesList . '. Réapprovisionnement requis.',
            'url' => '/admin/stock',
            'dossier_id' => $this->ticket->id,
            'type' => 'stock_insuffisant',
            'pieces' => $this->pieces,
        ];
    }
}
