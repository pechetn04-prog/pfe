<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Ce formulaire de requête valide le rejet d'une demande de retrait par l'administrateur (UC12).
class RejectDemandeRejetRequest extends FormRequest
{
    // Détermine si l'utilisateur (Admin) est autorisé à rejeter cette demande.
    public function authorize(): bool
    {
        return true;
    }

    // Règles de validation pour rejeter la demande de retrait (le commentaire justificatif est requis).
    public function rules(): array
    {
        return [
            'commentaire_admin' => ['required', 'string', 'min:5'],
        ];
    }

    // Messages d'erreur personnalisés traduits en français.
    public function messages(): array
    {
        return [
            'commentaire_admin.required' => 'Le motif du refus est obligatoire.',
            'commentaire_admin.min'      => 'Le motif du refus doit faire au moins 5 caractères.',
        ];
    }
}
