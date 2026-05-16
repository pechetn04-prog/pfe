<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    /**
     * Seuls les Admins et Agents peuvent modifier des utilisateurs.
     */
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['Admin', 'Agent']);
    }

    /**
     * Règles de validation.
     */
    public function rules(): array
    {
        $user = $this->route('user');
        $userId = is_object($user) ? $user->id : $user;

        return [
            'name'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($userId)],
            'password'   => ['nullable', 'string', 'min:8', 'confirmed'],
            'role'       => ['required', 'in:Admin,Agent,Technicien,Client'],
            'telephone'  => ['nullable', 'string', 'max:50'],
            'specialites'=> ['nullable', 'array'],
            'actif'      => ['nullable', 'boolean'],
        ];
    }

    /**
     * Messages d'erreur personnalisés.
     */
    public function messages(): array
    {
        return [
            'name.required'      => 'Le nom est obligatoire.',
            'email.required'     => 'L\'adresse e-mail est obligatoire.',
            'email.unique'       => 'Cette adresse e-mail est déjà utilisée.',
            'role.required'      => 'Le rôle est obligatoire.',
            'password.min'       => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
        ];
    }
}