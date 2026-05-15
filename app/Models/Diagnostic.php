<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Diagnostic
 * 
 * Stocke l'expertise technique réalisée par un technicien pour un appareil donné.
 * Ce modèle utilise des relations polymorphiques (morphToMany) pour lier les lignes de pièces 
 * détachées et les prestations de main d'œuvre aux différents documents (Diagnostic, Devis, Intervention).
 */
class Diagnostic extends Model
{
    protected $fillable = [
        'dossier_id',
        'technicien_id',
        'constat',
        'recommandation',
        'photo_panne',
        'exclusion_commentaire',
        'motif_exclusion',
    ];

    /**
     * Relation vers le dossier associé.
     * Le diagnostic est l'expertise technique d'un dossier spécifique.
     */
    public function dossier()
    {
        return $this->belongsTo(Dossier::class, 'dossier_id');
    }

    /**
     * Relation vers le Technicien (Modèle spécialisé).
     * Identifie l'expert qui a réalisé le diagnostic.
     */
    public function technicien()
    {
        return $this->belongsTo(Technicien::class, 'technicien_id');
    }

    /**
     * Relation Polymorphique vers les Pièces.
     * Permet de lister les pièces nécessaires identifiées lors du diagnostic.
     * La table 'ligne_pieces' sert de pivot pour plusieurs types de documents.
     */
    public function pieces()
    {
        return $this->morphToMany(Piece::class, 'source', 'ligne_pieces')
            ->withPivot('quantite', 'prix_unitaire')
            ->withTimestamps();
    }

    /**
     * Relation Polymorphique vers la Main d'œuvre (TarifMo).
     * Permet de lister les prestations techniques prévues.
     * La table 'ligne_mod' est utilisée de façon flexible par les diagnostics, devis et interventions.
     */
    public function tarifsMo()
    {
        return $this->morphToMany(TarifMo::class, 'source', 'ligne_mod')
            ->withPivot('montant')
            ->withTimestamps();
    }
}