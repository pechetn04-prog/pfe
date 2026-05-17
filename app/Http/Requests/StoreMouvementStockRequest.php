<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Ce formulaire de requête valide l'enregistrement manuel des mouvements d'entrée/sortie de pièces (UC14).
class StoreMouvementStockRequest extends FormRequest
{
    // Seuls les rôles Admin et Agent ont l'autorisation de mouvementer manuellement les pièces du stock.
    public function authorize(): bool
    {
        return in_array(auth()->user()->role, ['Admin', 'Agent']);
    }

    // Règles de validation pour les entrées et sorties de pièces.
    public function rules(): array
    {
        return [
            'piece_id' => ['required', 'exists:pieces,id'],
            'type'     => ['required', 'in:entree,sortie'],
            'quantite' => ['required', 'integer', 'min:1'],
            'motif'    => ['nullable', 'string', 'max:255'],
        ];
    }

    // Messages d'erreur personnalisés traduits en français.
    public function messages(): array
    {
        return [
            'piece_id.required' => 'Vous devez impérativement sélectionner une pièce du stock.',
            'type.required'     => 'Le type de mouvement (entree/sortie) est obligatoire.',
            'quantite.min'      => 'La quantité à mouvementer doit être au moins de 1.',
        ];
    }
}
