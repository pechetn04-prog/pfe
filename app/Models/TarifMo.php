<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TarifMo extends Model
{
    use HasFactory;

    protected $fillable = ['type_intervention', 'montant', 'actif'];

    public function devis()
    {
        return $this->morphedByMany(Devis::class, 'source', 'ligne_mod')
            ->withPivot('montant');
    }

    public function interventions()
    {
        return $this->morphedByMany(Intervention::class, 'source', 'ligne_mod')
            ->withPivot('montant');
    }

    public function diagnostics()
    {
        return $this->morphedByMany(Diagnostic::class, 'source', 'ligne_mod')
            ->withPivot('montant');
    }

    public function factures()
    {
        return $this->morphedByMany(Facture::class, 'source', 'ligne_mod')
            ->withPivot('montant');
    }
}
