<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Ce formulaire de requête valide les données lors de la modification de son propre profil (UC11).
class UpdateProfileRequest extends FormRequest
{
    // Tout utilisateur authentifié a l'autorisation de mettre à jour son propre profil.
    public function authorize(): bool
    {
        return auth()->check();
    }

    // Règles de validation des informations personnelles.
    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', 'unique:users,email,' . $this->user()->id],
            'telephone' => ['nullable', 'string', 'max:50'],
            'password'  => ['nullable', 'string', 'min:6', 'confirmed'],
        ];
    }

    // Messages d'erreur personnalisés traduits en français.
    public function messages(): array
    {
        return [
            'name.required'      => 'Le nom complet est obligatoire.',
            'email.required'     => 'L\'adresse e-mail est obligatoire.',
            'email.unique'       => 'Cette adresse e-mail est déjà attribuée à un autre compte.',
            'password.min'       => 'Le mot de passe de profil doit contenir au moins 6 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ];
    }
}
