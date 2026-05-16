<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Gère la validation lors de la création d'un utilisateur par l'administrateur ou l'agent.
 */
class StoreUserRequest extends FormRequest
{
    /**
     * Autorisation de la requête.
     */
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['Admin', 'Agent']);
    }

    /**
     * Règles de validation pour la création de compte.
     */
    public function rules(): array
    {
        $allowedRoles = auth()->user()->role === 'Agent' ? 'Client' : 'Admin,Agent,Technicien,Client';

        return [
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'    => ['required', 'string', 'min:8', 'confirmed'],
            'role'        => ['required', 'in:' . $allowedRoles],
            'telephone'   => ['nullable', 'string', 'max:20'],
            'specialites' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'      => 'Le nom est obligatoire.',
            'email.required'     => 'L\'adresse e-mail est obligatoire.',
            'email.unique'       => 'Cette adresse e-mail est déjà utilisée.',
            'role.required'      => 'Le rôle est obligatoire.',
            'password.required'  => 'Le mot de passe est obligatoire.',
            'password.min'       => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ];
    }
}
