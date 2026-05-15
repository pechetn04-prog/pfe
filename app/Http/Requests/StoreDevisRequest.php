<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Gère la validation des données lors de la création d'un devis.
 */
class StoreDevisRequest extends FormRequest
{
    /**
     * Autorisation de la requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation pour le devis (main d'œuvre, pièces).
     */
    public function rules(): array
    {
        return [
            'pieces' => ['nullable', 'array'],
            'pieces.*.id' => ['nullable', 'exists:pieces,id'],
            'pieces.*.quantite' => ['nullable', 'integer', 'min:1'],
            
            'labors' => ['required', 'array', 'min:1'], // Au moins une prestation est requise
            'labors.*.id' => ['required', 'exists:tarif_mos,id'],
            'labors.*.montant' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'labors.required' => "Au moins une prestation technique est obligatoire.",
            'labors.min' => "Veuillez sélectionner au moins une prestation technique.",
            'labors.*.id.required' => "Veuillez sélectionner un type d'intervention.",
            'labors.*.montant.required' => "Le montant de la prestation est obligatoire.",
            'pieces.*.quantite.min' => "La quantité d'une pièce doit être au moins de 1.",
        ];
    }
}
