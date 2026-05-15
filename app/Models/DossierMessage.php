<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DossierMessage extends Model
{
    protected $table = 'dossier_messages';

    protected $fillable = [
        'dossier_id',
        'user_id',
        'message'
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