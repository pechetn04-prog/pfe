<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dossier extends Model
{
    use HasFactory;

    protected $table = 'dossiers';

    protected $fillable = [
        'num_dossier',
        'appareil_id',
        'client_id',
        'agent_id',
        'technicien_id',
        'date_reception',
        'date_vente',
        'fin_garantie',
        'date_diagnostic',
        'date_reparation',
        'date_livraison',
        'date_cloture',
        'statut',
        'sous_garantie',
        'garantie_annulee',
        'panne_declaree',
        'accessoires_remis',
        'etat_appareil',
        'imei_remplacement',
        'modele_remplacement',
    ];

    protected $casts = [
        'date_reception' => 'datetime',
        'date_vente' => 'datetime',
        'fin_garantie' => 'datetime',
        'date_diagnostic' => 'datetime',
        'date_reparation' => 'datetime',
        'date_livraison' => 'datetime',
        'date_cloture' => 'datetime',
        'sous_garantie' => 'boolean',
    ];

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function appareil()
    {
        return $this->belongsTo(Appareil::class, 'appareil_id');
    }

    public function technicien()
    {
        return $this->belongsTo(User::class, 'technicien_id');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function suivi()
    {
        return $this->hasMany(SuiviDossier::class, 'dossier_id');
    }

    public function diagnostic()
    {
        return $this->hasOne(Diagnostic::class, 'dossier_id');
    }

    public function intervention()
    {
        return $this->hasOne(Intervention::class, 'dossier_id');
    }

    public function devis()
    {
        return $this->hasOne(Devis::class, 'dossier_id');
    }

    public function facture()
    {
        return $this->hasOne(Facture::class, 'dossier_id');
    }

    public function messages()
    {
        return $this->hasMany(DossierMessage::class, 'dossier_id');
    }



    // Helper pour récupérer l'IMEI via l'appareil
    public function getImeiAttribute()
    {
        return $this->appareil ? $this->appareil->imei : null;
    }
}
