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
            'constat'               => ['required', 'string', 'max:3000'],
            'recommandation'        => ['required', 'string', 'max:3000'],
            
            'garantie_annulee'      => ['nullable', 'boolean'],
            'motif_exclusion'       => ['required_if:garantie_annulee,1', 'nullable', 'string', 'in:casse,oxydation,mauvaise_manipulation,ouvert_par_tiers,utilisation_abusive,autre'],
            'exclusion_commentaire' => ['nullable', 'string', 'max:1000'],
            'photo_panne'           => ['nullable', 'image', 'max:2048'],
            'decision'              => ['required', 'string', 'in:reparable,irreparable'],

            // Consommation de pièces suggérées
            'pieces'                => ['nullable', 'array'],
            'pieces.*.piece_id'     => ['required_with:pieces', 'exists:pieces,id'],
            'pieces.*.quantite'     => ['required_with:pieces', 'integer', 'min:1'],

            // Estimations de main d'œuvre prévues
            'tarifs_mo'             => ['nullable', 'array'],
            'tarifs_mo.*.tarif_mo_id' => ['required_with:tarifs_mo', 'exists:tarif_mos,id'],
            'tarifs_mo.*.montant'   => ['required_with:tarifs_mo', 'numeric', 'min:0'],
        ];
    }

    // Messages d'erreur personnalisés traduits en français.
    public function messages(): array
    {
        return [
            'constat.required'             => "Le constat technique de la panne est obligatoire.",
            'recommandation.required'      => "La recommandation technique ou procédure recommandée est obligatoire.",
            'decision.required'            => "Vous devez obligatoirement spécifier si l'appareil est réparable ou non.",
            'motif_exclusion.required_if'  => "Veuillez préciser le motif justifiant l'exclusion de la garantie commerciale.",
        ];
    }
}