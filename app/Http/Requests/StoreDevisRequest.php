<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Ce formulaire de requête valide l'établissement d'un devis commercial (UC05 - Établir un devis).
class StoreDevisRequest extends FormRequest
{
    // Détermine si l'utilisateur est autorisé à faire cette demande.
    public function authorize(): bool
    {
        return true;
    }

    // Règles de validation des données financières et de main d'œuvre associées au devis.
    public function rules(): array
    {
        return [
            'total_ttc'              => ['required', 'numeric', 'min:0'],
            'pieces'                 => ['nullable', 'array'],
            'pieces.*.id'            => ['nullable', 'exists:pieces,id'],
            'pieces.*.prix_unitaire' => ['nullable', 'numeric', 'min:0'],
            'pieces.*.quantite'      => ['nullable', 'integer', 'min:1'],
            
            'labors'                 => ['nullable', 'array'],
            'labors.*.id'            => ['nullable', 'exists:tarif_mos,id'],
            'labors.*.montant'       => ['nullable', 'numeric', 'min:0'],
        ];
    }

    // Messages d'erreur personnalisés traduits en français.
    public function messages(): array
    {
        return [
            'labors.required'           => "Au moins une prestation technique est obligatoire.",
            'labors.min'                => "Veuillez sélectionner au moins une prestation technique.",
            'labors.*.id.required'      => "Veuillez sélectionner un type d'intervention.",
            'labors.*.montant.required' => "Le montant de la prestation est obligatoire.",
            'pieces.*.quantite.min'     => "La quantité d'une pièce de rechange doit être au moins de 1.",
        ];
    }
}
