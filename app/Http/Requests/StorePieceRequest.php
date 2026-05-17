<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Ce formulaire de requête valide l'ajout d'une nouvelle pièce détachée au catalogue du stock (UC14).
class StorePieceRequest extends FormRequest
{
    // Détermine si l'utilisateur est autorisé à créer cette pièce.
    public function authorize(): bool
    {
        return true;
    }

    // Règles de validation pour les propriétés de la pièce de rechange.
    public function rules(): array
    {
        return [
            'nom'           => ['required', 'string', 'max:255'],
            'reference'     => ['nullable', 'string', 'max:255'],
            'categorie'     => ['nullable', 'string', 'max:255'],
            'quantite'      => ['required', 'integer', 'min:0'],
            'prix_unitaire' => ['required', 'numeric', 'min:0'],
            'seuil_alerte'  => ['required', 'integer', 'min:0'],
        ];
    }

    // Messages d'erreur personnalisés traduits en français.
    public function messages(): array
    {
        return [
            'nom.required'           => 'Le nom descriptif de la pièce est obligatoire.',
            'quantite.required'      => 'La quantité initiale en stock est obligatoire.',
            'quantite.integer'       => 'La quantité en stock doit être un nombre entier.',
            'quantite.min'           => 'La quantité en stock ne peut pas être négative.',
            'prix_unitaire.required' => 'Le prix unitaire hors taxes de la pièce est obligatoire.',
            'prix_unitaire.numeric'  => 'Le prix unitaire doit être une valeur numérique.',
            'prix_unitaire.min'      => 'Le prix unitaire ne peut pas être négatif.',
            'seuil_alerte.required'  => 'Le seuil de réapprovisionnement (alerte) est obligatoire.',
            'seuil_alerte.integer'   => 'Le seuil d’alerte doit être un nombre entier.',
            'seuil_alerte.min'       => 'Le seuil d’alerte ne peut pas être négatif.',
        ];
    }
}