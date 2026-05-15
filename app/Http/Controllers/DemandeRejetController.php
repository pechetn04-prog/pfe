<?php

namespace App\Http\Controllers;

use App\Models\DemandeRejet;
use App\Models\Dossier;
use App\Models\SuiviDossier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * DemandeRejetController
 * UC12 (partiel) : Gestion des demandes de rejet/retrait soumises par les techniciens.
 * L'administrateur peut approuver ou refuser.
 */
class DemandeRejetController extends Controller
{
    /**
     * Liste des demandes de rejet en attente (Admin).
     */
    public function index()
    {
        $query = DemandeRejet::query();

        $total    = $query->count();
        $enAttente= (clone $query)->where('statut', 'EN_ATTENTE')->count();
        $acceptees= (clone $query)->where('statut', 'APPROUVE')->count();
        $refusees = (clone $query)->where('statut', 'REFUSE')->count();

        $demandes = DemandeRejet::with(['dossier.client', 'user'])
            ->orderByRaw("CASE WHEN statut = 'EN_ATTENTE' THEN 0 ELSE 1 END")
            ->latest()
            ->paginate(15);

        return view('admin.demandes_rejet.index', compact('demandes', 'total', 'enAttente', 'acceptees', 'refusees'));
    }

    /**
     * Soumettre une demande de rejet (Technicien) — UC15 alternatif.
     */
    public function store(Request $request, Dossier $dossier)
    {
        $request->validate([
            'raison' => 'required|string|min:10|max:1000',
        ]);

        // Vérifier qu'une demande n'existe pas déjà
        $existante = DemandeRejet::where('dossier_id', $dossier->id)
            ->where('statut', 'EN_ATTENTE')
            ->first();

        if ($existante) {
            return back()->with('error', 'Une demande de retrait est déjà en cours pour ce dossier.');
        }

        DemandeRejet::create([
            'dossier_id' => $dossier->id,
            'user_id'    => Auth::id(),
            'raison'     => $request->raison,
            'statut'     => 'EN_ATTENTE',
        ]);

        // Notifier les admins
        $admins = User::where('role', 'Admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\DemandeRejetNotification($dossier, Auth::user()));
        }

        return back()->with('success', 'Demande de retrait soumise à l\'administrateur.');
    }

    /**
     * Approuver la demande : retirer le dossier du technicien (Admin).
     * UC15 : réaffectation ou annulation.
     */
    public function approve(Request $request, DemandeRejet $demande)
    {
        $dossier = $demande->dossier;

        if (!$dossier) {
            return back()->with('error', 'Le dossier associé à cette demande est introuvable.');
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $demande, $dossier) {
            $demande->update([
                'statut'             => 'ACCEPTE',
                'commentaire_admin'  => $request->commentaire_admin,
            ]);

            $ancienStatut = $dossier->statut;
            
            // Libérer le technicien et remettre en RECU pour réaffectation
            $dossier->update([
                'technicien_id' => null,
                'statut'        => 'RECU',
            ]);

            SuiviDossier::create([
                'dossier_id'    => $dossier->id,
                'user_id'       => auth()->id(),
                'ancien_statut' => $ancienStatut,
                'nouveau_statut'=> 'RECU',
                'commentaire'   => 'Désaffectation du technicien approuvée. Motif : ' . ($request->commentaire_admin ?? 'Non spécifiée'),
            ]);
        });

        return redirect()->route('admin.demandes_rejet.index')->with('success', 'Demande approuvée. Le dossier est à nouveau disponible pour affectation.');
    }

    /**
     * Refuser la demande (Admin).
     */
    public function reject(Request $request, DemandeRejet $demande)
    {
        $request->validate([
            'commentaire_admin' => 'required|string|min:5',
        ]);

        $demande->update([
            'statut'            => 'REFUSE',
            'commentaire_admin' => $request->commentaire_admin,
        ]);

        \App\Models\SuiviDossier::create([
            'dossier_id'    => $demande->dossier_id,
            'user_id'       => auth()->id(),
            'ancien_statut' => $demande->dossier->statut,
            'nouveau_statut'=> $demande->dossier->statut, // Statut inchangé
            'commentaire'   => 'Demande de désaffectation refusée. Motif : ' . $request->commentaire_admin,
        ]);

        return back()->with('success', 'Demande refusée. Le technicien a été notifié.');
    }
}
