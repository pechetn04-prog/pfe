<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMouvementStockRequest extends FormRequest
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
            'piece_id' => 'required|exists:pieces,id',
            'type'     => 'required|in:entree,sortie,ajustement',
            'quantite' => 'required|integer|min:1',
            'motif'    => 'nullable|string|max:255',
        ];
    }

    /**
     * Messages personnalisés.
     */
    public function messages(): array
    {
        return [
            'piece_id.required' => 'Vous devez sélectionner une pièce.',
            'type.required'     => 'Le type de mouvement est obligatoire.',
            'quantite.min'      => 'La quantité doit être au moins de 1.',
        ];
    }
}
