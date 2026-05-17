<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

// Ce formulaire de requête valide la modification d'un compte utilisateur existant (UC11).
class UpdateUserRequest extends FormRequest
{
    // La modification des comptes utilisateurs est restreinte aux Admins et Agents SAV.
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['Admin', 'Agent']);
    }

    // Règles de validation des données du compte (exclut l'unicité de l'email pour le compte en cours d'édition).
    public function rules(): array
    {
        $user = $this->route('user');
        $userId = is_object($user) ? $user->id : $user;

        return [
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password'    => ['nullable', 'string', 'min:8', 'confirmed'], // Facultatif en cas d'édition
            'role'        => ['required', 'in:Admin,Agent,Technicien,Client'],
            'telephone'   => ['nullable', 'string', 'max:50'],
            'specialites' => ['nullable', 'array'],
            'actif'       => ['nullable', 'boolean'],
        ];
    }

    // Messages d'erreur personnalisés traduits en français.
    public function messages(): array
    {
        return [
            'name.required'      => 'Le nom complet de l\'utilisateur est obligatoire.',
            'email.required'     => 'L\'adresse e-mail de connexion est obligatoire.',
            'email.unique'       => 'Cette adresse e-mail est déjà attribuée à un compte existant.',
            'role.required'      => 'Veuillez sélectionner un profil de rôle valide.',
            'password.min'       => 'Le mot de passe modifié doit comporter au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ];
    }
}