<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Ce formulaire de requête valide l'affectation ou la réaffectation d'un technicien à un dossier (UC15).
class AssignDossierRequest extends FormRequest
{
    // Détermine si l'utilisateur est autorisé à affecter ce dossier.
    public function authorize(): bool
    {
        return true;
    }

    // Règles de validation pour l'affectation du dossier.
    public function rules(): array
    {
        return [
            'technicien_id' => ['required', 'exists:users,id'],
            'commentaire'   => ['nullable', 'string', 'max:500'],
        ];
    }

    // Messages d'erreur personnalisés traduits en français.
    public function messages(): array
    {
        return [
            'technicien_id.required' => "Le choix d'un technicien est obligatoire pour l'affectation.",
            'technicien_id.exists'   => "Le technicien sélectionné est invalide.",
            'commentaire.max'        => "Le commentaire de transfert ne peut pas dépasser 500 caractères.",
        ];
    }
}
