<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePieceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:255'],
            'categorie' => ['nullable', 'string', 'max:255'],
            'quantite' => ['required', 'integer', 'min:0'],
            'prix_unitaire' => ['required', 'numeric', 'min:0'],
            'seuil_alerte' => ['required', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom de la pièce est obligatoire.',
            'quantite.required' => 'La quantité est obligatoire.',
            'quantite.integer' => 'La quantité doit être un nombre entier.',
            'quantite.min' => 'La quantité ne peut pas être négative.',
            'prix_unitaire.required' => 'Le prix unitaire est obligatoire.',
            'prix_unitaire.numeric' => 'Le prix unitaire doit être un nombre.',
            'prix_unitaire.min' => 'Le prix unitaire ne peut pas être négatif.',
            'seuil_alerte.required' => 'Le seuil d’alerte est obligatoire.',
            'seuil_alerte.integer' => 'Le seuil d’alerte doit être un nombre entier.',
            'seuil_alerte.min' => 'Le seuil d’alerte ne peut pas être négatif.',
        ];
    }
}