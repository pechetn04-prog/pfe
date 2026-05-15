<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateParametreSocieteRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à faire cette requête.
     */
    public function authorize(): bool
    {
        return auth()->user()->role === 'Admin';
    }

    /**
     * Règles de validation en Français.
     */
    public function rules(): array
    {
        return [
            'nom_societe'   => ['nullable', 'string', 'max:255'],
            'adresse'       => ['nullable', 'string', 'max:255'],
            'telephone'     => ['nullable', 'string', 'max:50'],
            'email'         => ['nullable', 'email', 'max:255'],
            'ville'         => ['nullable', 'string', 'max:100'],
            'pays'          => ['nullable', 'string', 'max:100'],
            'site_web'      => ['nullable', 'string', 'max:255'],
            'numero_fiscal' => ['nullable', 'string', 'max:100'],
            'devise'        => ['nullable', 'string', 'max:10'],
            'logo'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /**
     * Messages d'erreur personnalisés.
     */
    public function messages(): array
    {
        return [
            'email.email' => 'L\'adresse email doit être valide.',
            'logo.image' => 'Le fichier doit être une image.',
            'logo.mimes' => 'Formats acceptés : jpg, jpeg, png, webp.',
            'logo.max'   => 'Le logo ne doit pas dépasser 2 Mo.',
        ];
    }
}
