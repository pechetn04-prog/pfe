<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Ce formulaire de requête valide la saisie des rapports de diagnostic technique (UC04).
class StoreDiagnosticRequest extends FormRequest
{
    // Détermine si l'utilisateur est autorisé à soumettre ce diagnostic.
    public function authorize(): bool
    {
        return true;
    }

    // Règles de validation des constats, pannes et recommandations techniques.
    public function rules(): array
    {
        return [
            'constat_technique'     => ['required', 'string', 'max:3000'],
            'recommandation'        => ['nullable', 'string', 'max:3000'],
            'photo_panne'           => ['nullable', 'image', 'max:2048'],
            'is_reparable'          => ['required', 'in:0,1'],
            'exclusion_garantie'    => ['nullable', 'boolean'],
            'motif_exclusion'       => ['nullable', 'string', 'max:255'],
            'exclusion_commentaire' => ['nullable', 'string', 'max:1000'],
            'pieces'                => ['nullable', 'array'],
            'pieces.*.id'           => ['required_with:pieces', 'exists:pieces,id'],
            'pieces.*.quantite'     => ['required_with:pieces', 'integer', 'min:1'],
            'labors'                => ['nullable', 'array'],
            'labors.*'              => ['exists:tarif_mos,id'],
        ];
    }

    // Messages d'erreur personnalisés traduits en français.
    public function messages(): array
    {
        return [
            'constat_technique.required'  => "Le constat technique de la panne est obligatoire.",
            'constat_technique.max'       => "Le constat technique ne peut pas dépasser 3000 caractères.",
            'recommandation.max'          => "La recommandation ne peut pas dépasser 3000 caractères.",
            'is_reparable.required'       => "Vous devez obligatoirement spécifier si l'appareil est réparable ou non.",
            'photo_panne.image'           => "Le fichier de la photo de panne doit être une image valide.",
            'photo_panne.max'             => "La photo de panne ne doit pas dépasser 2 Mo.",
            'pieces.*.quantite.min'       => "La quantité suggérée doit être au moins de 1.",
        ];
    }
}