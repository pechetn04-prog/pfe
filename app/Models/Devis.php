<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Devis extends Model
{
    protected $table = 'devis';

    protected $fillable = [
        'numero',
        'dossier_id',
        'montant_total',
        'frais_mod',
        'remise',
        'statut',
        'date_creation',
        'date_decision',
    ];

    protected $casts = [
        'montant_total' => 'decimal:3',
        'frais_mod' => 'decimal:3',
        'remise' => 'decimal:3',
        'date_creation' => 'date',
        'date_decision' => 'date',
    ];

    /**
     * Relation vers le dossier associé.
     */
    public function dossier()
    {
        return $this->belongsTo(Dossier::class, 'dossier_id');
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

    /**
     * Relation Polymorphique vers la Main d'œuvre (TarifMo).
     */
    public function tarifsMo()
    {
        return $this->morphToMany(TarifMo::class, 'source', 'ligne_mod')
            ->withPivot('montant')
            ->withTimestamps();
    }
}