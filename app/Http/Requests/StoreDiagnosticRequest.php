<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Gère la validation des données lors de la création d'un diagnostic technique.
 */
class StoreDiagnosticRequest extends FormRequest
{
    /**
     * Détermine si l'utilisateur est autorisé à effectuer cette requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Définit les règles de validation applicables à la requête.
     */
    public function rules(): array
    {
        return [
            'constat' => ['required', 'string', 'max:3000'],
            'recommandation' => ['required', 'string', 'max:3000'],

            'garantie_annulee' => ['nullable', 'boolean'],
            'motif_exclusion' => ['required_if:garantie_annulee,1', 'nullable', 'string', 'in:casse,oxydation,mauvaise_manipulation,ouvert_par_tiers,utilisation_abusive,autre'],
            'exclusion_commentaire' => ['nullable', 'string', 'max:1000'],
            'photo_panne' => ['nullable', 'image', 'max:2048'],
            'decision' => ['required', 'string', 'in:reparable,irreparable'],

            // Lignes pièces
            'pieces' => ['nullable', 'array'],
            'pieces.*.piece_id' => ['required_with:pieces', 'exists:pieces,id'],
            'pieces.*.quantite' => ['required_with:pieces', 'integer', 'min:1'],

            // Lignes main d'œuvre
            'tarifs_mo' => ['nullable', 'array'],
            'tarifs_mo.*.tarif_mo_id' => ['required_with:tarifs_mo', 'exists:tarif_mos,id'],
            'tarifs_mo.*.montant' => ['required_with:tarifs_mo', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'constat.required' => "Le constat du diagnostic est obligatoire.",
            'recommandation.required' => "La recommandation est obligatoire.",
            'decision.required' => "Vous devez prendre une décision (réparable, etc.).",
            'motif_exclusion.required_if' => "Veuillez préciser le motif de l'exclusion de garantie.",
        ];
    }
}