<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Ce formulaire de requête valide la création d'un utilisateur par un Administrateur ou un Agent SAV (UC11).
class StoreUserRequest extends FormRequest
{
    // L'autorisation d'ajouter des collaborateurs/clients est restreinte aux Admins et Agents.
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['Admin', 'Agent']);
    }

    // Règles de validation dynamiques selon les privilèges de l'acteur connecté.
    public function rules(): array
    {
        // Un Agent SAV peut uniquement inscrire des profils de type Client
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

    // Messages d'erreur personnalisés traduits en français.
    public function messages(): array
    {
        return [
            'name.required'      => 'Le nom complet est obligatoire.',
            'email.required'     => 'L\'adresse e-mail de connexion est obligatoire.',
            'email.unique'       => 'Cette adresse e-mail est déjà attribuée à un compte existant.',
            'role.required'      => 'Veuillez sélectionner un profil de rôle valide.',
            'password.required'  => 'Le mot de passe de connexion est obligatoire.',
            'password.min'       => 'Le mot de passe doit comporter au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ];
    }
}
