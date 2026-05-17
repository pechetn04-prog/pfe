<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Ce formulaire de requête valide les paramètres d'identité et de configuration fiscale de l'entreprise (UC01 / PFE).
class UpdateParametreSocieteRequest extends FormRequest
{
    // Seul le rôle Administrateur est autorisé à modifier la fiche d'identité de la société.
    public function authorize(): bool
    {
        return auth()->user()->role === 'Admin';
    }

    // Règles de validation des coordonnées et de la devise locale (DT à 3 décimales).
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

    // Messages d'erreur personnalisés traduits en français.
    public function messages(): array
    {
        return [
            'email.email' => 'L\'adresse e-mail saisie doit être au format valide.',
            'logo.image'  => 'Le logo téléversé doit être une image.',
            'logo.mimes'  => 'Les formats d\'image acceptés sont : JPG, JPEG, PNG, WEBP.',
            'logo.max'    => 'Le fichier du logo ne doit pas excéder 2 Mo.',
        ];
    }
}
