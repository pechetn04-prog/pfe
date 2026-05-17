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
     * Vérifie si l'appareil est encore sous garantie.
     */
    public function getEstSousGarantieAttribute()
    {
        if (!$this->date_vente || !$this->duree_garantie_mois) return false;
        
        $expiration = \Carbon\Carbon::parse($this->date_vente)->addMonths($this->duree_garantie_mois);
        return now()->lt($expiration);
    }

    /**
     * Calcule la date de fin de garantie.
     */
    public function getDateExpirationAttribute()
    {
        if (!$this->date_vente || !$this->duree_garantie_mois) return null;
        return \Carbon\Carbon::parse($this->date_vente)->addMonths($this->duree_garantie_mois);
    }

    /**
     * Relation vers l'appareil enregistré dans le SAV.
     * Une vente est liée à un appareil unique via son numéro IMEI.
     */
    public function appareil()
    {
        return $this->hasOne(Appareil::class, 'imei', 'imei');
    }
}