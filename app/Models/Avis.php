<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Avis extends Model
{
    use HasFactory;

    protected $table = 'avis';

    protected $fillable = [
        'dossier_id',
        'note',
        'commentaire',
        'date_avis'
    ];

    /**
     * Relation vers le ticket.
     * Chaque avis est lié à un ticket SAV spécifique pour évaluer la prestation.
     */
    public function ticket()
    {
        return $this->belongsTo(Dossier::class);
    }
}
