<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

// Ce formulaire de requête gère la validation stricte lors de la création d'un dossier SAV (UC03).
// Permet de séparer la logique de validation de la logique métier du contrôleur (Best Practice MVC).
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
            // IMEI requis pour tester l'éligibilité à la garantie commerciale (UC03)
            'imei' => ['required', 'string', 'min:5'],

            // Une panne doit être spécifiée soit via la liste prédéfinie, soit en saisie libre
            'pannes_list'            => ['required_without:panne_declaree_details', 'array'],
            'panne_declaree_details' => ['required_without:pannes_list', 'nullable', 'string', 'max:2000'],
            
            'accessoires'            => ['nullable', 'array'],
            'accessoires_autre'      => ['nullable', 'string', 'max:255'],
            
            // Coordonnées du client (saisie optionnelle si le client existe déjà ou est créé à la volée)
            'client_nom'             => ['nullable', 'string', 'max:255'],
            'client_tel'             => ['nullable', 'string', 'max:50'],
            'client_email'           => ['nullable', 'email', 'max:255'],
            
            // L'Agent SAV est obligé d'affecter un technicien dès la réception du ticket (UC03 - Point 4)
            'technicien_id' => [
                auth()->user()->role === 'Agent' ? 'required' : 'nullable',
                'integer',
                'exists:users,id'
            ],
            
            'article'                => ['nullable', 'string', 'max:255'],
            'reference_produit'      => ['nullable', 'string', 'max:255'],
        ];
    }

    // Définit les messages d'erreur personnalisés traduits en français pour l'utilisateur.
    public function messages(): array
    {
        return [
            'imei.required'                          => "Le numéro IMEI ou de série de l'appareil est obligatoire.",
            'pannes_list.required_without'           => "Veuillez sélectionner au moins une panne prédéfinie ou détailler le problème dans la zone de saisie libre.",
            'panne_declaree_details.required_without' => "Veuillez détailler le problème ou sélectionner au moins une panne prédéfinie.",
            'technicien_id.required'                 => "L'affectation à un technicien qualifié est obligatoire.",
            'technicien_id.exists'                   => "Le technicien sélectionné pour l'intervention n'existe pas dans le système.",
        ];
    }
}