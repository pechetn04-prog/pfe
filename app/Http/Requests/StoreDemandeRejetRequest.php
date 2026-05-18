<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Ce formulaire de requête valide la formulation d'une demande de désaffectation/rejet par un technicien (UC12).
class StoreDemandeRejetRequest extends FormRequest
{
    // Détermine si l'utilisateur est autorisé à formuler cette demande.
    public function authorize(): bool
    {
        return true;
    }

    // Règles de validation pour la soumission d'une demande de retrait.
    public function rules(): array
    {
        return [
            'raison' => ['required', 'string', 'min:10', 'max:1000'],
        ];
    }

    // Messages d'erreur personnalisés traduits en français.
    public function messages(): array
    {
        return [
            'raison.required' => "Le motif de la demande de désaffectation est obligatoire.",
            'raison.min'      => "Le motif doit faire au moins 10 caractères pour justifier le retrait.",
            'raison.max'      => "Le motif ne peut pas dépasser 1000 caractères.",
        ];
    }
}
