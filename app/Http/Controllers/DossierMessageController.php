<?php

namespace App\Http\Controllers;

use App\Models\Dossier;
use App\Models\DossierMessage;
use App\Http\Requests\StoreDossierMessageRequest;
use Illuminate\Support\Facades\Auth;

/**
 * Class DossierMessageController
 * 
 * Ce contrôleur pilote la communication collaborative rattachée à une fiche SAV.
 * Il permet d'échanger des notes d'atelier à caractère strictement interne (visibles par le staff)
 * ou des messages publics (visibles par le client sur son espace de suivi).
 */
class DossierMessageController extends Controller
{
    /**
     * Enregistre une note ou un message dans le fil de discussion du dossier SAV.
     * 
     * Cette méthode valide la saisie, applique le préfixe spécial "[INT] " s'il s'agit d'un message interne
     * afin de le masquer pour les clients, persiste l'enregistrement et effectue une redirection
     * ciblée en fonction du rôle de l'utilisateur pour préserver la cohérence de l'interface utilisateur.
     * Bloque toute écriture si le dossier est clôturé.
     */
    public function store(StoreDossierMessageRequest $request, Dossier $dossier)
    {

        if ($dossier->statut === 'CLOTURE') {
            return back()->with('error', "Ce dossier est clôturé. L'envoi de messages est désactivé.");
        }

        // Préfixe distinctif pour marquer visuellement les messages internes de l'atelier
        $prefix = ($request->type === 'internal') ? '[INT] ' : '';

        DossierMessage::create([
            'dossier_id' => $dossier->id,
            'user_id' => Auth::id(),
            'message' => $prefix . $request->message,
        ]);

        // Redirection ciblée selon le rôle pour éviter les erreurs d'accès (403)
        if (Auth::user()->role === 'Client') {
            return redirect()
                ->route('client.ticket', $dossier->id)
                ->with('success', 'Message envoyé avec succès.');
        }

        return redirect()
            ->to(route('dossiers.show', $dossier->id) . '?tab=communication')
            ->with('success', 'Message envoyé avec succès.');
    }
}
