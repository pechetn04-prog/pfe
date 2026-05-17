<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Ce formulaire de requête valide la tentative d'authentification d'un utilisateur.
class LoginRequest extends FormRequest
{
    // Détermine si l'utilisateur est autorisé à soumettre ce formulaire.
    public function authorize(): bool
    {
        return true;
    }

    // Règles de validation des identifiants de connexion.
    public function rules(): array
    {
        return [
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }
}