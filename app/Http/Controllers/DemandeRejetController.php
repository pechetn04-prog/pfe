<?php

namespace App\Http\Controllers;

use App\Models\DemandeRejet;
use App\Models\Dossier;
use App\Models\SuiviDossier;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Ce contrôleur pilote le traitement des demandes de retrait/désaffectation de dossiers par les techniciens (UC12).
// Permet aux techniciens de soumettre un dossier pour désaffectation et aux administrateurs de l'approuver ou le refuser.
class DemandeRejetController extends Controller
{
    // Affiche la liste paginée de toutes les demandes de retrait de dossiers avec indicateurs d'état.
    public function index()
    {
        $query = DemandeRejet::query();

        $total     = $query->count();
        $enAttente = (clone $query)->where('statut', 'EN_ATTENTE')->count();
        $acceptees = (clone $query)->where('statut', 'APPROUVE')->count();
        $refusees  = (clone $query)->where('statut', 'REFUSE')->count();

        // Afficher les demandes en attente de traitement en premier
        $demandes = DemandeRejet::with(['dossier.client', 'user'])
            ->orderByRaw("CASE WHEN statut = 'EN_ATTENTE' THEN 0 ELSE 1 END")
            ->latest()
            ->paginate(15);

        return view('admin.demandes_rejet.index', compact('demandes', 'total', 'enAttente', 'acceptees', 'refusees'));
    }

    // Enregistre une demande de retrait de dossier formulée par un technicien de l'atelier (UC12).
    public function store(Request $request, Dossier $dossier)
    {
        $request->validate([
            'raison' => 'required|string|min:10|max:1000',
        ]);

        // Règle métier : Empêcher d'avoir plusieurs demandes actives en attente pour le même dossier
        $existante = DemandeRejet::where('dossier_id', $dossier->id)
            ->where('statut', 'EN_ATTENTE')
            ->first();

        if ($existante) {
            return back()->with('error', 'Une demande de retrait est déjà en cours d\'examen pour ce dossier.');
        }

        DemandeRejet::create([
            'dossier_id' => $dossier->id,
            'user_id'    => Auth::id(),
            'raison'     => $request->raison,
            'statut'     => 'EN_ATTENTE',
        ]);

        // Notification automatique aux administrateurs du système
        $admins = User::where('role', 'Admin')->get();
        foreach ($admins as $admin) {
            try {
                $admin->notify(new \App\Notifications\DemandeRejetNotification($dossier, Auth::user()));
            } catch (\Exception $e) {
                // Fail-safe
            }
        }

        return back()->with('success', 'Votre demande de retrait a été soumise avec succès à l\'administrateur.');
    }

    // Approuve la demande de retrait : libère le dossier en retirant le technicien et le remet en statut initial 'RECU' pour réaffectation (Admin).
    public function approve(Request $request, DemandeRejet $demande)
    {
        $dossier = $demande->dossier;

        if (!$dossier) {
            return back()->with('error', 'Le dossier associé à cette demande est introuvable.');
        }

        // Transaction SQL sécurisée pour assurer la cohérence de l'état SAV
        \Illuminate\Support\Facades\DB::transaction(function () use ($request, $demande, $dossier) {
            $demande->update([
                'statut'             => 'APPROUVE',
                'commentaire_admin'  => $request->commentaire_admin,
            ]);

            $ancienStatut = $dossier->statut;
            
            // Libérer le technicien et repasser le dossier dans le pool d'attente d'affectation
            $dossier->update([
                'technicien_id' => null,
                'statut'        => 'RECU',
            ]);

            // Audit Trail de l'opération
            SuiviDossier::create([
                'dossier_id'     => $dossier->id,
                'user_id'        => auth()->id(),
                'ancien_statut'  => $ancienStatut,
                'nouveau_statut' => 'RECU',
                'commentaire'    => 'Désaffectation du technicien approuvée. Motif : ' . ($request->commentaire_admin ?? 'Non spécifié'),
            ]);
        });

        return redirect()->route('admin.demandes_rejet.index')
            ->with('success', 'La demande a été approuvée. Le dossier est à nouveau disponible pour affectation.');
    }

    // Refuse la demande de retrait : le dossier reste attribué au technicien pour traitement (Admin).
    public function reject(Request $request, DemandeRejet $demande)
    {
        $request->validate([
            'commentaire_admin' => 'required|string|min:5',
        ]);

        $demande->update([
            'statut'            => 'REFUSE',
            'commentaire_admin' => $request->commentaire_admin,
        ]);

        // Tracing historique sur le dossier
        \App\Models\SuiviDossier::create([
            'dossier_id'     => $demande->dossier_id,
            'user_id'        => auth()->id(),
            'ancien_statut'  => $demande->dossier->statut,
            'nouveau_statut' => $demande->dossier->statut, // L'état du dossier reste inchangé
            'commentaire'    => 'Demande de désaffectation refusée par l\'administrateur. Motif : ' . $request->commentaire_admin,
        ]);

        return back()->with('success', 'La demande de retrait a été rejetée. Le dossier reste assigné au technicien.');
    }
}
