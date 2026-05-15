<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeRejet extends Model
{
    use HasFactory;

    protected $table = 'demandes_rejet';

    protected $fillable = [
        'dossier_id',
        'user_id',
        'raison',
        'statut',
        'commentaire_admin'
    ];

    /**
     * Relation vers le ticket.
     * Identifie le dossier concerné par la demande de rejet ou d'annulation.
     */
    public function dossier()
    {
        return $this->belongsTo(Dossier::class, 'dossier_id');
    }

    /**
     * Relation vers l'utilisateur.
     * Identifie le technicien ou l'agent qui a soumis la demande.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
