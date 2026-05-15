<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * Modèle Client — Hérite de User
 * Représente les propriétaires des appareils déposés au SAV.
 */
class Client extends User
{
    /**
     * Utiliser la table unifiée 'users'
     */
    protected $table = 'users';

    /**
     * Scope global pour filtrer uniquement les clients.
     */
    protected static function booted()
    {
        static::addGlobalScope('role', function (Builder $builder) {
            $builder->where('role', 'Client');
        });
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->role = 'Client';
        });
    }

    /**
     * Un client possède plusieurs appareils.
     */
    public function appareils()
    {
        return $this->hasMany(Appareil::class, 'client_id');
    }

    /**
     * Un client possède plusieurs tickets SAV ouverts à son nom.
     */
    public function tickets()
    {
        return $this->hasMany(Dossier::class, 'client_id');
    }
}
