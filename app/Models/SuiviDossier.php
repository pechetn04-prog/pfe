<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SuiviDossier extends Model
{
    protected $table = 'suivi_dossiers';

    protected $fillable = [
        'dossier_id',
        'ancien_statut',
        'nouveau_statut',
        'user_id',
        'commentaire'
    ];

    public function dossier()
    {
        return $this->belongsTo(Dossier::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}