<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appareil extends Model
{
    protected $fillable = [
        'imei',
        'modele',
        'reference_produit',
        'client_id'
    ];

    /**
     * Relation vers les dossiers.
     * Un appareil peut être enregistré dans plusieurs dossiers SAV au cours de sa vie.
     */
    public function dossiers()
    {
        return $this->hasMany(Dossier::class);
    }

    /**
     * Relation vers le Client (Modèle spécialisé).
     * Un appareil appartient à un client spécifique.
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * Relation vers la vente originale.
     * Permet de lier l'appareil à sa fiche de vente via l'IMEI pour vérifier la garantie.
     */
    public function vente()
    {
        return $this->belongsTo(Vente::class, 'imei', 'imei');
    }
}
