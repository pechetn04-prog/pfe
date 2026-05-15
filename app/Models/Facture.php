<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    protected $fillable = [
        'dossier_id',
        'montant_total',
        'remise',
        'date_facture',
        'numero',
    ];

    /**
     * Relation vers le dossier associé.
     */
    public function dossier()
    {
        return $this->belongsTo(Dossier::class, 'dossier_id');
    }

    /**
     * Relation Polymorphique vers la Main d'œuvre (TarifMo).
     */
    public function tarifsMo()
    {
        return $this->morphToMany(TarifMo::class, 'source', 'ligne_mod')
            ->withPivot('montant')
            ->withTimestamps();
    }

    /**
     * Relation Polymorphique vers les Pièces.
     */
    public function pieces()
    {
        return $this->morphToMany(Piece::class, 'source', 'ligne_pieces')
            ->withPivot('quantite', 'prix_unitaire')
            ->withTimestamps();
    }
}