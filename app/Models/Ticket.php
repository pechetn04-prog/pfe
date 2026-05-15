<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'num_ticket',
        'appareil_id',
        'client_id',
        'agent_id',
        'technicien_id',
        'date_reception',
        'statut',
        'sous_garantie',
        'panne_declaree',
        'accessoires_remis',
        'etat_appareil',
        'date_diagnostic',
        'date_reparation',
        'date_livraison',
    ];

    public function appareil()
    {
        return $this->belongsTo(Appareil::class);
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function technicien()
    {
        return $this->belongsTo(User::class, 'technicien_id');
    }

    public function suivi()
    {
        return $this->hasMany(SuiviTicket::class);
    }

    public function diagnostic()
    {
        return $this->hasOne(Diagnostic::class);
    }

    public function intervention()
    {
        return $this->hasOne(Intervention::class);
    }

    public function devis()
    {
        return $this->hasOne(Devis::class);
    }

    public function facture()
    {
        return $this->hasOne(Facture::class);
    }

    public function isWarrantyValid()
    {
        return $this->sous_garantie == 1;
    }
}
