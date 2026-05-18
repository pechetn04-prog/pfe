<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Ce formulaire de requête valide la modification d'une pièce détachée du catalogue (UC14).
class UpdatePieceRequest extends FormRequest
{
    // Détermine si l'utilisateur est autorisé à modifier cette pièce.
    public function authorize(): bool
    {
        return true;
    }

    // Règles de validation pour les propriétés de la pièce de rechange en cours de modification.
    public function rules(): array
    {
        // Récupération de l'ID de la pièce de manière ultra-robuste (objet ou ID brut)
        $piece = $this->route('piece');
        $pieceId = is_object($piece) ? $piece->id : $piece;

        return [
            'nom'           => ['required', 'string', 'max:255'],
            'reference'     => ['nullable', 'string', 'max:100', 'unique:pieces,reference,' . $pieceId],
            'categorie'     => ['required', 'string', 'max:100'],
            'prix_unitaire' => ['required', 'numeric', 'min:0'],
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
            'reference.unique'       => 'Cette référence de pièce est déjà attribuée à un autre produit.',
            'categorie.required'     => 'La catégorie est obligatoire.',
            'categorie.max'          => 'La catégorie ne peut pas dépasser 100 caractères.',
            'prix_unitaire.required' => 'Le prix unitaire est obligatoire.',
            'prix_unitaire.numeric'  => 'Le prix unitaire doit être une valeur numérique.',
            'prix_unitaire.min'      => 'Le prix unitaire ne peut pas être négatif.',
            'seuil_alerte.required'  => 'Le seuil de réapprovisionnement (alerte) est obligatoire.',
            'seuil_alerte.integer'   => 'Le seuil d’alerte doit être un nombre entier.',
            'seuil_alerte.min'       => 'Le seuil d’alerte doit être au moins de 1.',
        ];
    }
}
