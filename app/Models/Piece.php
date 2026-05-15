<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Piece extends Model
{
    protected $fillable = [
        'nom',
        'reference',
        'categorie',
        'quantite',
        'prix_unitaire',
        'seuil_alerte',
        'actif',
    ];

    /**
     * Liste des catégories de pièces prédéfinies.
     */
    const CATEGORIES = [
        'Écran',
        'Batterie',
        'Connecteur de charge',
        'Caméra',
        'Haut-parleur',
        'Boutons',
        'Châssis / Coque',
        'Carte mère',
        'Divers',
    ];

    /**
     * Relation inverse Polymorphique vers les Devis.
     * Récupère tous les devis dans lesquels cette pièce a été estimée.
     */
    public function devis()
    {
        return $this->morphedByMany(Devis::class, 'source', 'ligne_pieces')
            ->withPivot('quantite', 'prix_unitaire')
            ->withTimestamps();
    }

    /**
     * Relation inverse Polymorphique vers les Interventions.
     * Récupère toutes les interventions ayant consommé cette pièce.
     */
    public function interventions()
    {
        return $this->morphedByMany(Intervention::class, 'source', 'ligne_pieces')
            ->withPivot('quantite', 'prix_unitaire')
            ->withTimestamps();
    }

    /**
     * Relation inverse Polymorphique vers les Diagnostics.
     * Récupère tous les diagnostics ayant identifié le besoin de cette pièce.
     */
    public function diagnostics()
    {
        return $this->morphedByMany(Diagnostic::class, 'source', 'ligne_pieces')
            ->withPivot('quantite', 'prix_unitaire')
            ->withTimestamps();
    }

    /**
     * Relation vers les mouvements de stock.
     * Historique complet des entrées, sorties et ajustements pour cette pièce.
     */
    public function mouvements()
    {
        return $this->hasMany(MouvementStock::class);
    }
}