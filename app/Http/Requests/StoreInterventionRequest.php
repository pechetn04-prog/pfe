<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Gère la validation des données lors de l'enregistrement d'une intervention.
 */
class StoreInterventionRequest extends FormRequest
{
    /**
     * Autorisation de la requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation pour l'intervention (compte-rendu, pièces, stock).
     */
    public function rules(): array
    {
        return [
            'compte_rendu' => ['required', 'string', 'max:5000'],

            'pieces' => ['nullable', 'array'],
            'pieces.*.id' => ['nullable', 'exists:pieces,id'],
            'pieces.*.quantite' => ['nullable', 'integer', 'min:1'],
            'labors' => ['nullable', 'array'],
            'labors.*.id' => ['nullable', 'exists:tarif_mos,id'],
            'labors.*.montant' => ['nullable', 'numeric', 'min:0'],
            'photo_intervention' => ['nullable', 'image', 'max:2048'],
            'statut_final' => ['nullable', 'string', 'in:REPARE,IRREPARABLE,ATTENTE_PIECE'],
        ];
    }

    public function messages(): array
    {
        return [
            'compte_rendu.required' => "Le compte-rendu de l'intervention est obligatoire.",

            'pieces.*.quantite.min' => "La quantité d'une pièce doit être au moins de 1.",
        ];
    }
}
