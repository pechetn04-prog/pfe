<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Ce formulaire de requête gère la validation stricte lors de la création d'un dossier SAV (UC03).
class StoreDossierRequest extends FormRequest
{
    // Détermine si l'utilisateur est autorisé à effectuer cette requête.
    public function authorize(): bool
    {
        return true;
    }

    // Définit les règles de validation applicables pour la création du dossier.
    public function rules(): array
    {
        return [
            'imei'              => ['required', 'string', 'min:5', 'max:50'],
            'modele'            => ['required', 'string', 'max:255'],
            'client_nom'        => ['required', 'string', 'max:255'],
            'client_telephone'  => ['required', 'string', 'max:50'],
            'client_email'      => ['required', 'email', 'max:255'],
            'technicien_id'     => ['required', 'integer', 'exists:users,id'],
            'type_pannes'       => ['nullable', 'array'],
            'panne_declaree'    => ['required', 'string', 'max:2000'],
            'accessoires'       => ['nullable', 'array'],
            'accessoires_autre' => ['nullable', 'string', 'max:255'],
            'reference_produit' => ['nullable', 'string', 'max:255'],
        ];
    }

    // Définit les messages d'erreur personnalisés traduits en français pour l'utilisateur.
    public function messages(): array
    {
        return [
            'imei.required'             => "Le numéro IMEI ou de série de l'appareil est obligatoire.",
            'imei.min'                  => "Le numéro IMEI doit faire au moins 5 caractères.",
            'modele.required'           => "Le modèle de l'appareil est obligatoire.",
            'client_nom.required'       => "Le nom du client est obligatoire.",
            'client_telephone.required' => "Le numéro de téléphone du client est obligatoire.",
            'client_email.required'     => "L'adresse email du client est obligatoire pour l'envoi des notifications.",
            'client_email.email'        => "Veuillez saisir une adresse email valide.",
            'technicien_id.required'    => "L'affectation à un technicien qualifié est obligatoire.",
            'technicien_id.exists'      => "Le technicien sélectionné pour l'intervention n'existe pas dans le système.",
            'panne_declaree.required'   => "La description détaillée ou le constat de panne est obligatoire.",
            'panne_declaree.max'        => "La description ne peut pas dépasser 2000 caractères.",
        ];
    }
}