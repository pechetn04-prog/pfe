<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Gère la validation lors de la création d'un nouveau ticket SAV.
 */
class StoreDossierRequest extends FormRequest
{
    /**
     * Autorisation de la requête.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Règles de validation pour le ticket (IMEI, client, panne, technicien).
     */
    public function rules(): array
    {
        return [
            'imei' => ['required', 'string', 'min:5'],

            'pannes_list' => ['required_without:panne_declaree_details', 'array'],
            'panne_declaree_details' => ['required_without:pannes_list', 'nullable', 'string', 'max:2000'],
            'accessoires' => ['nullable', 'array'],
            'accessoires_autre' => ['nullable', 'string', 'max:255'],
            'client_nom' => ['nullable', 'string', 'max:255'],
            'client_tel' => ['nullable', 'string', 'max:50'],
            'client_email' => ['nullable', 'email', 'max:255'],
            'technicien_id' => [
                auth()->user()->role === 'Agent' ? 'required' : 'nullable',
                'integer',
                'exists:users,id'
            ],
            'article' => ['nullable', 'string', 'max:255'],
            'reference_produit' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'imei.required' => "L'IMEI est obligatoire.",
            'date_reception.required' => "La date de réception est obligatoire.",
            'panne_declaree.required' => "La panne déclarée est obligatoire.",
            'technicien_id.required' => "L'affectation à un technicien est obligatoire pour un Agent SAV.",
            'technicien_id.exists' => "Le technicien sélectionné est invalide.",
        ];
    }
}