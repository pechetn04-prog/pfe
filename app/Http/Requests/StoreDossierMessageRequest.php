<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Ce formulaire de requête valide l'ajout d'un message ou d'une note dans le fil de discussion d'un dossier SAV.
class StoreDossierMessageRequest extends FormRequest
{
    // Détermine si l'utilisateur est autorisé à effectuer cette requête.
    public function authorize(): bool
    {
        return true;
    }

    // Règles de validation pour la création d'un message.
    public function rules(): array
    {
        return [
            'message' => ['required', 'string', 'max:1000'],
            'type'    => ['nullable', 'string', 'in:public,internal'],
        ];
    }

    // Messages d'erreur personnalisés traduits en français.
    public function messages(): array
    {
        return [
            'message.required' => 'Le contenu du message est obligatoire.',
            'message.string'   => 'Le message doit être une chaîne de caractères.',
            'message.max'      => 'Le message ne peut pas dépasser 1000 caractères.',
            'type.in'          => 'Le type de message spécifié est invalide.',
        ];
    }
}
