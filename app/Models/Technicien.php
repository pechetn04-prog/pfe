<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * Modèle Technicien — Hérite de User
 * Représente les experts techniques chargés des diagnostics et réparations.
 */
class Technicien extends User
{
    /**
     * Utiliser la table unifiée 'users'
     */
    protected $table = 'users';

    /**
     * Scope global pour filtrer uniquement les techniciens.
     */
    protected static function booted()
    {
        static::addGlobalScope('role', function (Builder $builder) {
            $builder->where('role', 'Technicien');
        });
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->role = 'Technicien';
        });
    }

    /**
     * Un technicien possède plusieurs tickets qui lui sont assignés.
     */
    public function ticketsAssignes()
    {
        return $this->hasMany(Dossier::class, 'technicien_id');
    }
}
