<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vente extends Model
{
    protected $table = 'ventes';

    protected $fillable = [
        'type',
        'imei',
        'modele',
        'client_nom',
        'date_vente',
        'duree_garantie_mois',
        'reference_produit',
        'numero_facture_vente',
    ];

    protected $casts = [
        'date_vente' => 'datetime',
    ];



    /**
     * Relation vers l'appareil enregistré dans le SAV.
     * Une vente est liée à un appareil unique via son numéro IMEI.
     */
    public function appareil()
    {
        return $this->hasOne(Appareil::class, 'imei', 'imei');
    }
}