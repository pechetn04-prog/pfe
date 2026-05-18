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
            'reference'     => ['nullable', 'string', 'max:100', 'unique:pieces,reference'],
            'categorie'     => ['required', 'string', 'max:100'],
            'prix_unitaire' => ['required', 'numeric', 'min:0'],
            'quantite'      => ['required', 'integer', 'min:0'],
            'seuil_alerte'  => ['required', 'integer', 'min:1'],
        ];
    }

    // Messages d'erreur personnalisés traduits en français.
    public function messages(): array
    {
        return [
            'nom.required'           => 'Le nom descriptif de la pièce est obligatoire.',
            'nom.max'                => 'Le nom descriptif ne peut pas dépasser 255 caractères.',
            'reference.max'          => 'La référence ne peut pas dépasser 100 caractères.',
            'reference.unique'       => 'Cette référence de pièce existe déjà dans le catalogue.',
            'categorie.required'     => 'La catégorie est obligatoire.',
            'categorie.max'          => 'La catégorie ne peut pas dépasser 100 caractères.',
            'prix_unitaire.required' => 'Le prix unitaire est obligatoire.',
            'prix_unitaire.numeric'  => 'Le prix unitaire doit être une valeur numérique.',
            'prix_unitaire.min'      => 'Le prix unitaire ne peut pas être négatif.',
            'quantite.required'      => 'La quantité initiale en stock est obligatoire.',
            'quantite.integer'       => 'La quantité en stock doit être un nombre entier.',
            'quantite.min'           => 'La quantité en stock ne peut pas être négative.',
            'seuil_alerte.required'  => 'Le seuil de réapprovisionnement (alerte) est obligatoire.',
            'seuil_alerte.integer'   => 'Le seuil d’alerte doit être un nombre entier.',
            'seuil_alerte.min'       => 'Le seuil d’alerte doit être au moins de 1.',
        ];
    }
}