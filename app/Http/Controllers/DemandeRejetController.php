<?php

namespace App\Http\Controllers;

use App\Models\DemandeRejet;
use App\Models\Dossier;
use App\Models\SuiviDossier;
use App\Models\User;
use App\Http\Requests\StoreDemandeRejetRequest;
use App\Http\Requests\ApproveDemandeRejetRequest;
use App\Http\Requests\RejectDemandeRejetRequest;
use App\Notifications\DemandeRejetNotification;
use App\Notifications\GenericNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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
        $acceptees = (clone $query)->where('statut', 'ACCEPTE')->count();
        $refusees  = (clone $query)->where('statut', 'REFUSE')->count();

        // Afficher les demandes en attente de traitement en premier
        $demandes = DemandeRejet::with(['dossier.client', 'user', 'nouveauTechnicien'])
            ->orderByRaw("CASE WHEN statut = 'EN_ATTENTE' THEN 0 ELSE 1 END")
            ->latest()
            ->paginate(15);

        // Liste des techniciens qualifiés pour réaffectation directe
        $techniciens = User::where('role', 'Technicien')
            ->where('actif', true)
            ->withCount([
                'dossiers as dossiers_en_cours' => function ($q) {
                    $q->whereNotIn('statut', ['LIVRE', 'CLOTURE']);
                }
            ])
            ->get();

        return view('demandes_rejet.index', compact('demandes', 'total', 'enAttente', 'acceptees', 'refusees', 'techniciens'));
    }

    public function store(StoreDemandeRejetRequest $request, Dossier $dossier)
    {

        // Règle métier : Empêcher d'avoir plusieurs demandes actives en attente pour le même dossier
        $existante = DemandeRejet::where('dossier_id', $dossier->id)
            ->where('statut', 'EN_ATTENTE')
            ->first();

        if ($existante) {
            return back()->with('error', 'Une demande de retrait is déjà en cours d\'examen pour ce dossier.');
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
                $admin->notify(new DemandeRejetNotification($dossier, Auth::user()));
            } catch (\Exception $e) {
                // Fail-safe
            }
        }

        return back()->with('success', 'Votre demande de retrait a été soumise avec succès à l\'administrateur.');
    }

    // Approuve la demande de retrait : libère le dossier en retirant le technicien et le réaffecte immédiatement à un nouveau technicien (Admin).
    public function approve(ApproveDemandeRejetRequest $request, DemandeRejet $demande)
    {

        $dossier = $demande->dossier;

        if (!$dossier) {
            return back()->with('error', 'Le dossier associé à cette demande est introuvable.');
        }

        // Transaction SQL sécurisée pour assurer la cohérence de l'état SAV
        DB::transaction(function () use ($request, $demande, $dossier) {
            $demande->update([
                'statut'                => 'ACCEPTE',
                'commentaire_admin'     => $request->commentaire_admin,
                'nouveau_technicien_id' => $request->new_technicien_id,
            ]);

            $ancienStatut = $dossier->statut;
            $nouveauTech = User::find($request->new_technicien_id);
            
            // Réaffecter le nouveau technicien et passer le dossier en statut 'AFFECTE'
            $dossier->update([
                'technicien_id' => $request->new_technicien_id,
                'statut'        => 'AFFECTE',
            ]);

            // Audit Trail de l'opération
            SuiviDossier::create([
                'dossier_id'     => $dossier->id,
                'user_id'        => auth()->id(),
                'ancien_statut'  => $ancienStatut,
                'nouveau_statut' => 'AFFECTE',
                'commentaire'    => 'Retrait approuvé. Dossier réaffecté au technicien : ' . $nouveauTech->name . '. Commentaire : ' . $request->commentaire_admin,
            ]);

            // Notifications en temps réel
            // 1. Notifier le technicien demandeur que sa demande est acceptée
            if ($demande->user) {
                $demande->user->notify(new GenericNotification(
                    "Demande de désaffectation acceptée (#{$dossier->num_dossier})",
                    "Votre demande de retrait pour le dossier #{$dossier->num_dossier} a été approuvée par l'administrateur. Commentaire : " . $request->commentaire_admin,
                    route('dossiers.show', $dossier->id)
                ));
            }

            // 2. Notifier le nouveau technicien affecté
            if ($nouveauTech) {
                $nouveauTech->notify(new GenericNotification(
                    "Nouveau dossier assigné (#{$dossier->num_dossier})",
                    "Vous avez été assigné au dossier #{$dossier->num_dossier} suite à une réaffectation.",
                    route('dossiers.show', $dossier->id)
                ));
            }
        });

        return redirect()->route('admin.demandes_rejet.index')
            ->with('success', 'La demande a été approuvée et le dossier a été réaffecté au nouveau technicien avec succès.');
    }

    // Refuse la demande de retrait : le dossier reste attribué au technicien pour traitement (Admin).
    public function reject(RejectDemandeRejetRequest $request, DemandeRejet $demande)
    {

        $demande->update([
            'statut'            => 'REFUSE',
            'commentaire_admin' => $request->commentaire_admin,
        ]);

        // Tracing historique sur le dossier
        SuiviDossier::create([
            'dossier_id'     => $demande->dossier_id,
            'user_id'        => auth()->id(),
            'ancien_statut'  => $demande->dossier->statut,
            'nouveau_statut' => $demande->dossier->statut, // L'état du dossier reste inchangé
            'commentaire'    => 'Demande de désaffectation refusée par l\'administrateur. Motif : ' . $request->commentaire_admin,
        ]);

        // Notification en temps réel
        // Notifier le technicien demandeur que sa demande est refusée
        if ($demande->user) {
            $demande->user->notify(new GenericNotification(
                "Demande de désaffectation refusée (#{$demande->dossier->num_dossier})",
                "Votre demande de retrait pour le dossier #{$demande->dossier->num_dossier} a été refusée par l'administrateur. Motif : " . $request->commentaire_admin,
                route('dossiers.show', $demande->dossier_id)
            ));
        }

        return back()->with('success', 'La demande de retrait a été rejetée. Le dossier reste assigné au technicien.');
    }
}
