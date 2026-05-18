<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Ce formulaire de requête valide la création d'une facture finale pour le client (UC08).
class StoreFactureRequest extends FormRequest
{
    // Détermine si l'utilisateur est autorisé à générer cette facture.
    public function authorize(): bool
    {
        return true;
    }

    // Règles de validation des éléments financiers de la facture (remise et main d'œuvre).
    public function rules(): array
    {
        return [
            'labors'           => ['nullable', 'array'],
            'labors.*.id'      => ['required_with:labors', 'exists:tarif_mos,id'],
            'labors.*.montant' => ['required_with:labors', 'numeric', 'min:0'],
            'remise'           => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }

    // Messages d'erreur personnalisés traduits en français.
    public function messages(): array
    {
        return [
            'labors.*.montant.required_with' => "Le montant de prestation est obligatoire pour les lignes ajoutées.",
            'labors.*.montant.min'           => "Le montant de prestation ne peut pas être négatif.",
            'labors.*.montant.numeric'       => "Le montant de prestation doit être une valeur numérique valide.",
            'remise.min'                     => "La remise ne peut pas être négative.",
            'remise.max'                     => "La remise ne peut pas dépasser 100%.",
            'remise.numeric'                 => "La remise doit être une valeur numérique.",
        ];
    }
}
