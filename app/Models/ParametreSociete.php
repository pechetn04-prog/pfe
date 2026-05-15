<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParametreSociete extends Model
{
    protected $table = 'parametres_societe';

    protected $fillable = [
        'nom_societe',
        'telephone',
        'email',
        'site_web',
        'ville',
        'pays',
        'numero_fiscal',
        'devise',
        'adresse',
        'logo',
    ];
}