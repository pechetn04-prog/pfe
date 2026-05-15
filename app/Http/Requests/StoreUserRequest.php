<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Gère la validation lors de la création d'un utilisateur par l'administrateur.
 */
class StoreUserRequest extends FormRequest
{
    /**
     * Autorisation de la requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation pour la création de compte (nom, email, mdp, rôle).
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:Admin,Agent,Technicien,Client'],
            'telephone' => ['nullable', 'string', 'max:20'],

            'specialites' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.unique' => "Cette adresse email est déjà utilisée.",
            'password.min' => "Le mot de passe doit faire au moins 8 caractères.",
            'password.confirmed' => "Les deux mots de passe ne correspondent pas.",
        ];
    }
}
