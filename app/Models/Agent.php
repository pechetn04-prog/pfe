<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * Modèle Agent — Hérite de User
 * Représente le personnel d'accueil SAV qui crée les dossiers.
 */
class Agent extends User
{
    /**
     * Utiliser la table unifiée 'users'
     */
    protected $table = 'users';

    /**
     * Scope global pour filtrer uniquement les agents.
     */
    protected static function booted()
    {
        static::addGlobalScope('role', function (Builder $builder) {
            $builder->where('role', 'Agent');
        });
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->role = 'Agent';
        });
    }

    /**
     * Un agent peut avoir créé plusieurs tickets.
     */
    public function ticketsCrees()
    {
        return $this->hasMany(Dossier::class, 'agent_id');
    }
}
