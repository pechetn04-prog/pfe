<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

   
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'actif',
        'telephone',
        'specialite',


    ];

     /**
     * Liste des spécialités techniques de l'atelier SAV.
     */
    public const SPECIALITES = [
        'Écran & Affichage',
        'Batterie & Alimentation',
        'Connectique & Ports',
        'Caméra',
        'Audio',
        'Connectivité',
        'Logiciel & Système',
        'Dommages Physiques',
        'Sécurité & Accès'
    ];


    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    /**
     * Un utilisateur peut envoyer plusieurs messages dans le chat des dossiers.
     */
    public function dossierMessages()
    {
        return $this->hasMany(DossierMessage::class, 'dossier_id');
    }

    /**
     * Si l'utilisateur est un TECHNICIEN, il possède plusieurs dossiers qui lui sont assignés.
     */
    public function dossiers()
    {
        return $this->hasMany(Dossier::class, 'technicien_id');
    }

    /**
     * Si l'utilisateur est un CLIENT, il possède plusieurs dossiers SAV.
     */
    public function clientDossiers()
    {
        return $this->hasMany(Dossier::class, 'client_id');
    }

    /**
     * Un utilisateur peut avoir plusieurs appareils enregistrés à son nom.
     */
    public function appareils()
    {
        return $this->hasMany(Appareil::class, 'client_id');
    }

    /**
     * Accessseur pour obtenir les spécialités sous forme de collection.
     * Maintient la compatibilité avec l'ancien schéma relationnel.
     */
    public function getSpecialitesAttribute()
    {
        $specs = explode(',', $this->specialite ?? '');
        return collect($specs)->filter()->map(function($spec) {
            return (object) ['specialite' => trim($spec)];
        });
    }

}
