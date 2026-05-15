<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * Modèle Admin — Hérite de User
 * Représente les utilisateurs ayant un accès total au système.
 */
class Admin extends User
{
    /**
     * Utiliser la table unifiée 'users'
     */
    protected $table = 'users';

    /**
     * Scope global pour filtrer uniquement les administrateurs.
     */
    protected static function booted()
    {
        static::addGlobalScope('role', function (Builder $builder) {
            $builder->where('role', 'Admin');
        });
    }

    /**
     * Initialise automatiquement le rôle lors de la création d'un Admin.
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->role = 'Admin';
        });
    }
}
