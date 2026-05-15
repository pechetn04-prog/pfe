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
        $demandes = DemandeRejet::with(['ticket.client', 'user'])
            ->latest()
            ->get();

        $total    = $demandes->count();
        $enAttente= $demandes->where('statut', 'EN_ATTENTE')->count();
        $acceptees= $demandes->where('statut', 'APPROUVE')->count();
        $refusees = $demandes->where('statut', 'REFUSE')->count();

        // Vue : uniquement les EN_ATTENTE pour affichage actif
        $demandes = $demandes->where('statut', 'EN_ATTENTE')->values();

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
        $dossier = $demande->ticket;

        $demande->update([
            'statut'             => 'APPROUVE',
            'commentaire_admin'  => $request->commentaire_admin,
        ]);

        // Libérer le technicien du dossier
        $ancienStatut = $dossier->statut;
        $dossier->update([
            'technicien_id' => null,
            'statut'        => 'AFFECTE',
        ]);

        SuiviDossier::create([
            'dossier_id'    => $dossier->id,
            'user_id'       => Auth::id(),
            'ancien_statut' => $ancienStatut,
            'nouveau_statut'=> 'AFFECTE',
            'commentaire'   => 'Demande de retrait approuvée. Dossier désaffecté pour réaffectation.',
        ]);

        return back()->with('success', 'Demande approuvée. Le dossier est de nouveau disponible.');
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

        return back()->with('success', 'Demande refusée. Le technicien a été notifié.');
    }
}
