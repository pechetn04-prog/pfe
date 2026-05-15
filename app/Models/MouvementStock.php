<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MouvementStock extends Model
{
    protected $fillable = [
        'piece_id',
        'user_id',
        'type',
        'quantite',
        'motif',
        'reference_id',
        'reference_type',
    ];

    /**
     * Relation Polymorphique 'MorphTo'.
     * Permet de lier le mouvement à sa source (ex: une Intervention ou une Entrée manuelle).
     */
    public function reference()
    {
        return $this->morphTo();
    }

    /**
     * Relation vers la pièce détachée.
     * Identifie la pièce concernée par ce mouvement.
     */
    public function piece()
    {
        return $this->belongsTo(Piece::class);
    }

    /**
     * Relation vers l'utilisateur (Admin / Technicien).
     * Identifie la personne qui a déclenché le mouvement de stock.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
