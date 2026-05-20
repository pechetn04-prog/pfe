<?php

namespace App\Http\Controllers;

use App\Models\Dossier;
use App\Models\DemandeRejet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class TechnicienDashboardController
 * 
 * Gère le tableau de bord technique (UC01 / UC08 / UC09).
 * Centralise les dossiers assignés au technicien pour diagnostic, réparation, et historique.
 */
class TechnicienDashboardController extends Controller
{
    /**
     * Affiche l'index du tableau de bord pour le technicien connecté.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        // ---------------------------------------------------------
        // 1. STATISTIQUES ET INDICATEURS CLÉS (KPIs)
        // ---------------------------------------------------------
        $totalAssigne = Dossier::where('technicien_id', $user->id)->count();
        $aDiagnostiquer = Dossier::where('technicien_id', $user->id)->where('statut', 'AFFECTE')->count();
        $enReparation = Dossier::where('technicien_id', $user->id)->where('statut', 'EN_REPARATION')->count();
        $terminesMois = Dossier::where('technicien_id', $user->id)
            ->whereIn('statut', ['REPARE', 'IRREPARABLE', 'LIVRE'])
            ->whereMonth('updated_at', now()->month)
            ->count();
        $attentePieces = Dossier::where('technicien_id', $user->id)->where('statut', 'ATTENTE_PIECE')->count();

        $dossiersEnCours = Dossier::where('technicien_id', $user->id)
            ->whereNotIn('statut', ['LIVRE', 'CLOTURE'])
            ->count();

        // Tableau des KPIs structuré pour le rendu passif côté vue
        $tech_kpis = [
            ['label' => 'Total Assignés', 'val' => $totalAssigne, 'icon' => 'fa-briefcase', 'class' => 'bg-soft-primary'],
            ['label' => 'En Diagnostic', 'val' => $aDiagnostiquer, 'icon' => 'fa-search', 'class' => 'bg-soft-warning'],
            ['label' => 'En Réparation', 'val' => $enReparation, 'icon' => 'fa-tools', 'class' => 'bg-soft-success'],
            ['label' => 'Attente Pièces', 'val' => $attentePieces, 'icon' => 'fa-hourglass-half', 'class' => 'bg-soft-danger'],
            ['label' => 'Terminés (Mois)', 'val' => $terminesMois, 'icon' => 'fa-check-double', 'class' => 'bg-soft-info'],
        ];

        // ---------------------------------------------------------
        // 2. FLUX DE TRAVAIL TECHNIQUE (LISTES D'ACTIVITÉS)
        // ---------------------------------------------------------

        // Dossiers assignés en attente d'analyse technique (UC08)
        $dossiersDiagnostique = Dossier::where('technicien_id', $user->id)
            ->whereIn('statut', ['AFFECTE', 'EN_DIAGNOSTIC'])
            ->with(['client', 'appareil'])
            ->latest('updated_at')
            ->take(5)
            ->get();

        // Dossiers actuellement en cours d'intervention sur table (UC09)
        $dossiersReparation = Dossier::where('technicien_id', $user->id)
            ->where('statut', 'EN_REPARATION')
            ->with(['client', 'appareil'])
            ->latest('updated_at')
            ->take(5)
            ->get();

        // Activité et historique récent des dossiers de ce technicien
        $dossiersTermines = Dossier::where('technicien_id', $user->id)
            ->with(['client', 'appareil'])
            ->latest('updated_at')
            ->take(10)
            ->get();

        $dossiersTermines->transform(function ($d) {
            $d->garantie_color = $d->sous_garantie ? 'success' : 'danger';
            $d->garantie_text = $d->sous_garantie ? 'SOUS GARANTIE' : 'HORS GARANTIE';
            return $d;
        });

        return view('dashboard.technicien', compact(
            'tech_kpis',
            'dossiersEnCours',
            'dossiersDiagnostique',
            'dossiersReparation',
            'dossiersTermines'
        ));
    }

    // Affiche la liste paginée de toutes les demandes de réaffectation du technicien.
    public function demandesReaffectationList()
    {
        $user = Auth::user();
        
        // 1. Demandes en cours (statut = EN_ATTENTE)
        $demandesEnCours = DemandeRejet::where('user_id', $user->id)
            ->where('statut', 'EN_ATTENTE')
            ->with(['dossier.appareil'])
            ->latest()
            ->get();

        // 2. Historique (statut != EN_ATTENTE)
        $historique = DemandeRejet::where('user_id', $user->id)
            ->where('statut', '!=', 'EN_ATTENTE')
            ->with(['dossier.appareil'])
            ->latest()
            ->paginate(2);

        return view('technicien.demandes_reaffectation', compact('demandesEnCours', 'historique'));
    }

    // Recherche et filtre la liste des dossiers assignés au technicien (Tickets actifs).
    public function tickets(Request $request)
    {
        $query = Dossier::where('technicien_id', Auth::id())
            ->whereNotIn('statut', ['CLOTURE', 'LIVRE']) // Exclure les dossiers terminés / livrés
            ->with(['client', 'appareil'])
            ->latest();

        // Filtrage par statut
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // Recherche textuelle multi-critères
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('num_dossier', 'like', "%{$search}%")
                    ->orWhereHas('appareil', function ($q2) use ($search) {
                        $q2->where('imei', 'like', "%{$search}%");
                    })
                    ->orWhereHas('client', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")
                            ->orWhere('telephone', 'like', "%{$search}%");
                    });
            });
        }

        $dossiers = $query->paginate(20)->withQueryString();

        $statuts = [
            'AFFECTE' => 'Assigné',
            'EN_DIAGNOSTIC' => 'En Diagnostic',
            'EN_ATTENTE_DEVIS' => 'Attente Devis',
            'EN_REPARATION' => 'En Réparation',
            'ATTENTE_PIECE' => 'En attente pièces',
            'REPARE' => 'Réparé',
            'IRREPARABLE' => 'Irréparable',
            'LIVRE' => 'Restitué',
            'CLOTURE' => 'Clôturé',
        ];

        $map = [
            'AFFECTE' => 'bg-secondary',
            'EN_DIAGNOSTIC' => 'bg-info text-dark',
            'EN_ATTENTE_DEVIS' => 'bg-warning text-dark',
            'EN_REPARATION' => 'bg-primary',
            'ATTENTE_PIECE' => 'bg-dark',
            'REPARE' => 'bg-success',
            'IRREPARABLE' => 'bg-danger',
            'CLOTURE' => 'bg-dark',
            'LIVRE' => 'bg-success',
        ];

        $dossiers->getCollection()->transform(function ($d) use ($map, $statuts) {
            if ($d->garantie_annulee) {
                $d->garantie_color = 'warning';
                $d->garantie_text = 'GARANTIE EXCLUE';
            } elseif ($d->sous_garantie) {
                $d->garantie_color = 'success';
                $d->garantie_text = 'SOUS GARANTIE';
            } else {
                $d->garantie_color = 'danger';
                $d->garantie_text = 'HORS GARANTIE';
            }

            $d->statut_class = $map[$d->statut] ?? 'bg-secondary';
            $d->statut_label = $statuts[$d->statut] ?? $d->statut;

            return $d;
        });

        return view('technicien.tickets', compact('dossiers', 'statuts'));
    }

    /**
     * Raccourci de redirection vers la gestion des stocks de pièces détachées.
     */
    public function stock()
    {
        return redirect()->route('stock.index');
    }
}
