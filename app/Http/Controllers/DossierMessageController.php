<?php

namespace App\Http\Controllers;

use App\Models\Dossier;
use App\Models\DossierMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Ce contrôleur gère la communication (messages internes de l'atelier et notes d'information publique) liée à un dossier SAV.
class DossierMessageController extends Controller
{
    // Enregistre un message ou une note dans le fil de discussion du dossier.
    public function store(Request $request, Dossier $dossier)
    {
        if ($dossier->statut === 'CLOTURE') {
            return back()->with('error', "Ce dossier est clôturé. L'envoi de messages est désactivé.");
        }
        $request->validate([
            'message' => 'required|string|max:1000',
            'type' => 'nullable|string|in:public,internal',
        ]);

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
