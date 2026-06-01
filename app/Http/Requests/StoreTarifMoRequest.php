<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Ce formulaire de requête valide l'ajout d'un nouveau tarif de main d'œuvre dans le système.
class StoreTarifMoRequest extends FormRequest
{
    // Détermine si l'utilisateur est autorisé à effectuer cette requête.
    public function authorize(): bool
    {
        return true;
    }

    // Règles de validation pour la création d'un tarif de main d'œuvre.
    public function rules(): array
    {
        return [
            'type_intervention' => ['required', 'string', 'max:255'],
            'montant'           => ['required', 'numeric', 'min:0'],
        ];
    }

    // Messages d'erreur personnalisés traduits en français.
    public function messages(): array
    {
        return [
            'type_intervention.required' => 'Le type d’intervention est obligatoire.',
            'type_intervention.string'   => 'Le type d’intervention doit être une chaîne de caractères.',
            'type_intervention.max'      => 'Le type d’intervention ne peut pas dépasser 255 caractères.',
            'montant.required'           => 'Le montant est obligatoire.',
            'montant.numeric'            => 'Le montant doit être une valeur numérique.',
            'montant.min'                => 'Le montant ne peut pas être négatif.',
        ];
    }
}
