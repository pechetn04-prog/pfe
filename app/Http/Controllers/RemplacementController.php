<?php

namespace App\Http\Controllers;

use App\Models\Dossier;
use App\Models\User;
use App\Models\Vente;
use App\Models\SuiviDossier;
use App\Notifications\GenericNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RemplacementController extends Controller
{
    /**
     * Formulaire de préparation du remplacement (irréparable sous garantie).
     */
    public function preparerRemplacement(Dossier $dossier)
    {
        return view('remplacements.preparer', compact('dossier'));
    }

    /**
     * Enregistrer le remplacement et basculer statut.
     */
    public function storeRemplacement(Request $request, Dossier $dossier)
    {
        $request->validate([
            'imei_remplacement' => 'required|string',
            'modele_remplacement' => 'nullable|string',
        ]);

        $ancienStatut = $dossier->statut;
        $modele = $request->modele_remplacement ?: $dossier->appareil->modele;

        // Enregistrer le nouvel appareil dans la table ventes avec type REMPLACEMENT
        Vente::create([
            'type' => 'REMPLACEMENT',
            'imei' => $request->imei_remplacement,
            'modele' => $modele,
            'client_nom' => $dossier->client->name,
            'date_vente' => now()->toDateString(),
            'duree_garantie_mois' => 12,
            'reference_produit' => $dossier->appareil->reference_produit ?? null,
            'numero_facture_vente' => 'SAV-REMP-' . $dossier->num_dossier,
        ]);

        // Mettre à jour le dossier
        $dossier->update([
            'statut' => 'REMPLACEMENT_PRET',
            'imei_remplacement' => $request->imei_remplacement,
            'modele_remplacement' => $modele,
        ]);

        SuiviDossier::create([
            'dossier_id' => $dossier->id,
            'user_id' => Auth::id(),
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => 'REMPLACEMENT_PRET',
            'commentaire' => "Appareil de substitution préparé — Modèle : {$modele} / IMEI : {$request->imei_remplacement}.",
        ]);

        return redirect()->route('dossiers.show', $dossier->id)
            ->with('success', "Remplacement enregistré. Appareil {$modele} (IMEI : {$request->imei_remplacement}) prêt pour livraison.");
    }

    /**
     * Valider la demande de remplacement (Admin).
     */
    public function validateReplacement(Dossier $dossier)
    {
        $dossier->update(['statut' => 'REMPLACEMENT_VALIDE']);

        SuiviDossier::create([
            'dossier_id' => $dossier->id,
            'user_id' => Auth::id(),
            'ancien_statut' => 'ATTENTE_VALIDATION_REMPLACEMENT',
            'nouveau_statut' => 'REMPLACEMENT_VALIDE',
            'commentaire' => 'Remplacement de l\'appareil approuvé par l\'administration.',
        ]);

        // Notification aux agents SAV
        $agents = User::where('role', 'Agent')->where('actif', true)->get();
        foreach ($agents as $agent) {
            $agent->notify(new GenericNotification(
                "Remplacement validé (#{$dossier->num_dossier})",
                "L'administration a validé le remplacement. Veuillez préparer un appareil neuf.",
                route('dossiers.show', $dossier->id)
            ));
        }

        return back()->with('success', 'Remplacement validé. L\'agent SAV a été notifié pour préparer l\'appareil.');
    }

    /**
     * Refuser la demande de remplacement (Admin).
     */
    public function refuseReplacement(Request $request, Dossier $dossier)
    {
        $request->validate([
            'raison' => 'required|string|min:5'
        ], [
            'raison.required' => 'Le motif du refus est obligatoire.'
        ]);

        $dossier->update(['statut' => 'REMPLACEMENT_REFUSE']);

        SuiviDossier::create([
            'dossier_id' => $dossier->id,
            'user_id' => Auth::id(),
            'ancien_statut' => 'ATTENTE_VALIDATION_REMPLACEMENT',
            'nouveau_statut' => 'REMPLACEMENT_REFUSE',
            'commentaire' => 'Remplacement refusé par l\'administration. Motif : ' . $request->raison,
        ]);

        // Notification aux agents SAV
        $agents = User::where('role', 'Agent')->where('actif', true)->get();
        foreach ($agents as $agent) {
            $agent->notify(new GenericNotification(
                "Remplacement refusé (#{$dossier->num_dossier})",
                "L'administration a refusé le remplacement. Motif : {$request->raison}. Veuillez informer le client.",
                route('dossiers.show', $dossier->id)
            ));
        }

        return back()->with('warning', 'Remplacement refusé. Le dossier est passé en statut Remplacement Refusé.');
    }
}
