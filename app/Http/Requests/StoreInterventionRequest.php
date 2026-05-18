<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Ce formulaire de requête valide le compte-rendu des interventions physiques des techniciens (UC09).
class StoreInterventionRequest extends FormRequest
{
    // Détermine si l'utilisateur est autorisé à faire cette demande.
    public function authorize(): bool
    {
        return true;
    }

    // Règles de validation pour le rapport final d'intervention sur table.
    public function rules(): array
    {
        return [
            'compte_rendu'       => ['required', 'string', 'max:5000'],
            'pieces'             => ['nullable', 'array'],
            'pieces.*.id'        => ['nullable', 'exists:pieces,id'],
            'pieces.*.quantite'  => ['nullable', 'integer', 'min:1'],
            'labors'             => ['nullable', 'array'],
            'labors.*.id'        => ['nullable', 'exists:tarif_mos,id'],
            'labors.*.montant'   => ['nullable', 'numeric', 'min:0'],
            'photo_intervention' => ['nullable', 'image', 'max:2048'],
            'statut_final'       => ['required', 'string', 'in:REPARE,IRREPARABLE,ATTENTE_PIECE'],
        ];
    }

    // Messages d'erreur personnalisés traduits en français.
    public function messages(): array
    {
        return [
            'compte_rendu.required' => "Le compte-rendu technique de l'intervention est obligatoire.",
            'pieces.*.quantite.min' => "La quantité d'une pièce consommée doit être au moins de 1.",
        ];
    }
}
