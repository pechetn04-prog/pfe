<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Ce formulaire de requête valide l'inscription initiale des comptes utilisateurs.
class RegisterRequest extends FormRequest
{
    // Détermine si l'utilisateur est autorisé à soumettre ce formulaire.
    public function authorize(): bool
    {
        return true;
    }

    // Règles de validation pour la création initiale d'un compte.
    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'unique:users,email'],
            'password'  => ['required', 'string', 'min:8', 'confirmed'],
            'role'      => ['required', 'in:Admin,Agent,Technicien,Client'],
            'telephone' => ['nullable', 'string', 'max:50'],
            'Adresse'   => ['nullable', 'string', 'max:255'],
            'actif'     => ['nullable', 'boolean'],
        ];
    }
}
