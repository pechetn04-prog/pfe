<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Ce formulaire de requête valide l'approbation et la réaffectation d'une demande de retrait par l'administrateur (UC12).
class ApproveDemandeRejetRequest extends FormRequest
{
    // Détermine si l'utilisateur (Admin) est autorisé à approuver cette demande.
    public function authorize(): bool
    {
        return true;
    }

    // Règles de validation pour approuver le retrait et affecter le nouveau technicien.
    public function rules(): array
    {
        return [
            'commentaire_admin' => ['required', 'string', 'min:5'],
            'new_technicien_id' => ['required', 'exists:users,id'],
        ];
    }

    // Messages d'erreur personnalisés traduits en français.
    public function messages(): array
    {
        return [
            'commentaire_admin.required' => 'Le commentaire d\'approbation est obligatoire.',
            'commentaire_admin.min'      => 'Le commentaire doit faire au moins 5 caractères.',
            'new_technicien_id.required' => 'Le choix d\'un nouveau technicien est obligatoire pour la réaffectation.',
            'new_technicien_id.exists'   => 'Le technicien sélectionné est invalide.',
        ];
    }
}
