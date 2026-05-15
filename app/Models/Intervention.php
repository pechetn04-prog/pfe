<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Intervention extends Model
{
    protected $fillable = [
        'dossier_id',
        'technicien_id',
        'date_fin',
        'compte_rendu',
        'photo_intervention',
    ];

    /**
     * Relation vers le dossier associé.
     */
    public function dossier()
    {
        return $this->belongsTo(Dossier::class, 'dossier_id');
    }

    /**
     * Relation vers le Technicien (Modèle spécialisé).
     * Identifie la personne qui a effectué les travaux de réparation.
     */
    public function technicien()
    {
        return $this->belongsTo(User::class, 'technicien_id');
    }

    /**
     * Relation Polymorphique vers les Pièces.
     * Liste les pièces réellement consommées durant la réparation.
     */
    public function pieces()
    {
        return $this->morphToMany(Piece::class, 'source', 'ligne_pieces')
            ->withPivot('quantite', 'prix_unitaire')
            ->withTimestamps();
    }

    /**
     * Relation Polymorphique vers la Main d'œuvre (TarifMo).
     * Liste les prestations facturées lors de l'intervention.
     */
    public function tarifsMo()
    {
        return $this->morphToMany(TarifMo::class, 'source', 'ligne_mod')
            ->withPivot('montant')
            ->withTimestamps();
    }
}