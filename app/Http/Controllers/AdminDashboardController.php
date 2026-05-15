<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\Piece;
use App\Models\Dossier;
use App\Models\User;
use App\Models\Devis;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Statistiques pour les 8 cartes du dashboard
        $stats = [
            'total' => Dossier::count(),
            'recu' => Dossier::where('statut', 'RECU')->count(),
            'en_diagnostic' => Dossier::where('statut', 'EN_DIAGNOSTIC')->count(),
            'attente_devis' => Dossier::where('statut', 'EN_ATTENTE_DEVIS')->count(),
            'en_reparation' => Dossier::where('statut', 'EN_REPARATION')->count(),
            'attente_pieces' => Dossier::where('statut', 'ATTENTE_PIECE')->count(),
            'cloture' => Dossier::whereIn('statut', ['LIVRE', 'CLOTURE'])->count(),
            'users' => User::count(),
        ];

        // Statistiques pour les graphiques
        $stats['labels_7_days'] = [];
        $stats['data_7_days'] = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $stats['labels_7_days'][] = $date->format('d/m');
            $stats['data_7_days'][] = Dossier::whereDate('created_at', $date)->count();
        }

        $stats['status_distribution'] = [
            'Nouveaux' => $stats['recu'],
            'En Cours' => Dossier::whereIn('statut', ['AFFECTE', 'EN_DIAGNOSTIC', 'EN_REPARATION', 'ATTENTE_PIECE'])->count(),
            'Terminés' => Dossier::whereIn('statut', ['REPARE', 'FACTURE', 'LIVRE', 'CLOTURE'])->count(),
        ];

        $stats['warranty_distribution'] = [
            'Sous Garantie'   => Dossier::where('sous_garantie', true)->where('garantie_annulee', false)->count(),
            'Hors Garantie'   => Dossier::where('sous_garantie', false)->count(),
            'Garantie Exclue' => Dossier::where('garantie_annulee', true)->count(),
        ];

        // Alertes de stock (Pièces sous le seuil)
        $stockAlerts = Piece::where('actif', true)
            ->whereRaw('quantite <= seuil_alerte')
            ->orderBy('quantite', 'asc')
            ->take(5)
            ->get();

        // Dossiers à diagnostiquer (Nouveaux ou Affectés)
        $dossiersDiagnostique = Dossier::with(['client', 'appareil'])
            ->whereIn('statut', ['RECU', 'AFFECTE', 'EN_DIAGNOSTIC'])
            ->latest()
            ->take(6)
            ->get();

        // Dossiers à réparer (En cours de réparation)
        $dossiersReparation = Dossier::with(['client', 'appareil'])
            ->where('statut', 'EN_REPARATION')
            ->latest()
            ->take(6)
            ->get();

        // Dossiers en attente de pièces
        $dossiersAttentePieces = Dossier::with(['client', 'appareil'])
            ->where('statut', 'ATTENTE_PIECE')
            ->latest()
            ->take(6)
            ->get();

        $recentDossiers = Dossier::with(['client', 'appareil'])->latest()->take(10)->get();

        return view('dashboard.admin', compact('stats', 'dossiersDiagnostique', 'dossiersReparation', 'dossiersAttentePieces', 'stockAlerts', 'recentDossiers'));
    }

    public function statistiques(Request $request)
    {
        $dateDebut = $request->input('date_debut') ? Carbon::parse($request->input('date_debut'))->startOfDay() : now()->subMonths(3)->startOfDay();
        $dateFin = $request->input('date_fin') ? Carbon::parse($request->input('date_fin'))->endOfDay() : now()->endOfDay();

        $query = Dossier::whereBetween('dossiers.created_at', [$dateDebut, $dateFin]);

        $totalDossiers = (clone $query)->count();

        // Chiffre d'affaires basé sur les factures générées dans la période
        $chiffreAffaires = Facture::whereBetween('factures.created_at', [$dateDebut, $dateFin])->sum('montant_total');

        // Taux d'acceptation des devis
        $totalDevis = Devis::whereBetween('devis.created_at', [$dateDebut, $dateFin])->count();
        $devisAcceptes = Devis::whereBetween('devis.created_at', [$dateDebut, $dateFin])->where('statut', 'ACCEPTE')->count();
        $tauxAcceptation = $totalDevis > 0 ? round(($devisAcceptes / $totalDevis) * 100, 1) : 0;

        // Analyse des retards (Donut Chart)
        $now = now();
        $retards = [
            '24-48h' => (clone $query)->whereNotIn('statut', ['CLOTURE', 'LIVRE'])
                ->where('date_reception', '<=', $now->copy()->subHours(24))
                ->where('date_reception', '>', $now->copy()->subHours(48))->count(),
            '48-72h' => (clone $query)->whereNotIn('statut', ['CLOTURE', 'LIVRE'])
                ->where('date_reception', '<=', $now->copy()->subHours(48))
                ->where('date_reception', '>', $now->copy()->subHours(72))->count(),
            '>72h' => (clone $query)->whereNotIn('statut', ['CLOTURE', 'LIVRE'])
                ->where('date_reception', '<=', $now->copy()->subHours(72))->count(),
        ];

        $totalRetard = array_sum($retards);
        $tauxRetard = $totalDossiers > 0 ? round(($totalRetard / $totalDossiers) * 100, 1) : 0;

        // Données pour les graphiques : Dossiers par Tech
        $dossiersParTech = User::where('role', 'Technicien')
            ->withCount([
                'dossiers' => function ($q) use ($dateDebut, $dateFin) {
                    $q->whereBetween('created_at', [$dateDebut, $dateFin]);
                }
            ])->get();

        // Top 10 Pannes Fréquentes
        $topPannes = (clone $query)->select('panne_declaree', DB::raw('count(*) as total'))
            ->whereNotNull('panne_declaree')
            ->groupBy('panne_declaree')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        // Consommation Pièces (Top 5)
        $topPieces = DB::table('ligne_pieces')
            ->join('pieces', 'pieces.id', '=', 'ligne_pieces.piece_id')
            ->join('interventions', 'interventions.id', '=', 'ligne_pieces.source_id')
            ->where('ligne_pieces.source_type', 'App\Models\Intervention')
            ->whereBetween('ligne_pieces.created_at', [$dateDebut, $dateFin])
            ->select('pieces.nom', DB::raw('SUM(ligne_pieces.quantite) as total_qty'))
            ->groupBy('pieces.id', 'pieces.nom')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // Top 10 Modèles Fréquents
        $topModeles = (clone $query)->join('appareils', 'appareils.id', '=', 'dossiers.appareil_id')
            ->select('appareils.modele', DB::raw('count(*) as total'))
            ->groupBy('appareils.modele')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        return view('admin.statistiques', compact(
            'totalDossiers',
            'chiffreAffaires',
            'tauxAcceptation',
            'tauxRetard',
            'retards',
            'dossiersParTech',
            'topPannes',
            'topPieces',
            'topModeles',
            'dateDebut',
            'dateFin'
        ));
    }
}
