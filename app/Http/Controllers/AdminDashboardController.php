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

// Ce contrôleur gère le tableau de bord de l'administrateur (UC01) et le module analytique (UC15).
// Fournit les indicateurs clés de performance (KPIs) opérationnels et financiers pour le SAV.
class AdminDashboardController extends Controller
{
    // Affiche l'index du tableau de bord de l'administrateur avec synthèse des dossiers, alertes et indicateurs.
    public function index()
    {
        // Statistiques globales et volumétrie des dossiers
        $stats = [
            'total'          => Dossier::count(),
            'recu'           => Dossier::where('statut', 'RECU')->count(),
            'en_diagnostic'  => Dossier::where('statut', 'EN_DIAGNOSTIC')->count(),
            'attente_devis'  => Dossier::where('statut', 'EN_ATTENTE_DEVIS')->count(),
            'en_reparation'  => Dossier::where('statut', 'EN_REPARATION')->count(),
            'attente_pieces' => Dossier::where('statut', 'ATTENTE_PIECE')->count(),
            'cloture'        => Dossier::whereIn('statut', ['LIVRE', 'CLOTURE'])->count(),
            'users'          => User::count(),
            'irreparable'    => Dossier::where('statut', 'IRREPARABLE')->count(),
            
            // Flux spécifiques (demandes d'échange sous garantie et demandes de retrait)
            'attente_validation_remplacement' => Dossier::where('statut', 'ATTENTE_VALIDATION_REMPLACEMENT')->count(),
            'demandes_rejet_count'            => \App\Models\DemandeRejet::where('statut', 'EN_ATTENTE')->count(),
            'demandes_rejet_recent'           => \App\Models\DemandeRejet::with(['dossier', 'user'])
                ->where('statut', 'EN_ATTENTE')
                ->latest()
                ->take(3)
                ->get(),
        ];

        // Distribution globale de TOUS les statuts individuels pour le graphique de synthèse
        $statusLabels = [
            'RECU'                            => 'Reçu',
            'AFFECTE'                         => 'Affecté',
            'EN_DIAGNOSTIC'                   => 'En Diagnostic',
            'EN_ATTENTE_DEVIS'                => 'En Attente Devis',
            'DEVIS_REFUSE'                    => 'Devis Refusé',
            'EN_REPARATION'                   => 'En Réparation',
            'REPARE'                          => 'Réparé',
            'IRREPARABLE'                     => 'Irréparable',
            'ATTENTE_PIECE'                   => 'Attente Pièce',
            'ATTENTE_VALIDATION_REMPLACEMENT' => 'Attente Remplacement',
            'ATTENTE_REMPLACEMENT'            => 'Attente Remplacement Prêt',
            'REMPLACEMENT_PRET'               => 'Remplacement Prêt',
            'FACTURE'                         => 'Facturé',
            'LIVRE'                           => 'Restitué',
            'CLOTURE'                         => 'Clôturé',
            'ANNULE'                          => 'Annulé',
            'REMPLACEMENT_VALIDE'             => 'Remplacement Validé',
            'REMPLACEMENT_REFUSE'             => 'Remplacement Refusé'
        ];

        $stats['status_distribution'] = [];
        $dossierCounts = Dossier::select('statut', \DB::raw('count(*) as count'))
            ->groupBy('statut')
            ->get();

        foreach ($dossierCounts as $dc) {
            $label = $statusLabels[$dc->statut] ?? str_replace('_', ' ', $dc->statut);
            $stats['status_distribution'][$label] = $dc->count;
        }

        // Répartition des dossiers selon l'éligibilité à la garantie commerciale (UC03)
        $stats['warranty_distribution'] = [
            'Sous Garantie'   => Dossier::where('sous_garantie', true)->where('garantie_annulee', false)->count(),
            'Hors Garantie'   => Dossier::where('sous_garantie', false)->count(),
            'Garantie Exclue' => Dossier::where('garantie_annulee', true)->count(),
        ];

        // Alertes de stock critique (quantité en stock <= seuil d'alerte configuré) (UC14)
        $stockAlerts = Piece::where('actif', true)
            ->whereRaw('quantite <= seuil_alerte')
            ->orderBy('quantite', 'asc')
            ->take(5)
            ->get();

        // Dossiers au tout début du flux SAV (Nouveaux ou en cours d'analyse)
        $dossiersDiagnostique = Dossier::with(['client', 'appareil'])
            ->whereIn('statut', ['RECU', 'AFFECTE', 'EN_DIAGNOSTIC'])
            ->latest()
            ->take(6)
            ->get();

        // Dossiers actuellement en cours d'intervention sur table (UC09)
        $dossiersReparation = Dossier::with(['client', 'appareil'])
            ->where('statut', 'EN_REPARATION')
            ->latest()
            ->take(6)
            ->get();

        // Dossiers bloqués en attente de réapprovisionnement de pièces (UC14)
        $dossiersAttentePieces = Dossier::with(['client', 'appareil'])
            ->where('statut', 'ATTENTE_PIECE')
            ->latest()
            ->take(6)
            ->get();

        // Dossiers en attente de validation de remplacement sous garantie (UC04)
        $dossiersAttenteRemplacement = Dossier::with(['client', 'appareil'])
            ->where('statut', 'ATTENTE_VALIDATION_REMPLACEMENT')
            ->latest()
            ->take(6)
            ->get();

        // Historique récent des 5 derniers dossiers créés
        $recentDossiers = Dossier::with(['client', 'appareil'])->latest()->take(5)->get();

        // Liste des techniciens actifs pour les assignations rapides
        $techniciens = User::where('role', 'Technicien')->where('actif', true)->get();

        // Mapping esthétique des 10 KPIs pour la grille premium du dashboard
        $adminKpis = [
            ['label' => 'TOTAL TICKETS',  'val' => $stats['total'],          'icon' => 'fa-folder-open',          'class' => 'bg-soft-primary'],
            ['label' => 'NOUVEAUX REÇUS',  'val' => $stats['recu'],           'icon' => 'fa-inbox',                'class' => 'bg-soft-danger'],
            ['label' => 'EN DIAGNOSTIC',   'val' => $stats['en_diagnostic'],  'icon' => 'fa-microscope',           'class' => 'bg-soft-warning'],
            ['label' => 'ATTENTE DEVIS',   'val' => $stats['attente_devis'],  'icon' => 'fa-file-invoice-dollar',  'class' => 'bg-soft-info'],
            ['label' => 'EN RÉPARATION',   'val' => $stats['en_reparation'],  'icon' => 'fa-tools',                'class' => 'bg-soft-success'],
            ['label' => 'ATTENTE PIÈCES',  'val' => $stats['attente_pieces'], 'icon' => 'fa-clock',                'class' => 'bg-soft-danger'],
            ['label' => 'ATTENTE REMPLACEMENT', 'val' => $stats['attente_validation_remplacement'], 'icon' => 'fa-exchange-alt', 'class' => 'bg-soft-warning text-warning'],
            ['label' => 'IRRÉPARABLES',    'val' => $stats['irreparable'],    'icon' => 'fa-times-circle',         'class' => 'bg-soft-danger text-danger'],
            ['label' => 'LIVRÉS / CLOS',   'val' => $stats['cloture'],        'icon' => 'fa-check-double',         'class' => 'bg-soft-slate'],
            ['label' => 'UTILISATEURS',    'val' => $stats['users'],          'icon' => 'fa-users',                'class' => 'bg-soft-purple'],
        ];

        // Nomenclature des classes CSS pour les statuts
        $statClasses = [
            'RECU'             => 'bg-light text-muted',
            'AFFECTE'          => 'bg-secondary text-white',
            'EN_DIAGNOSTIC'    => 'bg-info text-dark',
            'EN_REPARATION'    => 'bg-primary text-white',
            'EN_ATTENTE_DEVIS' => 'bg-warning text-dark',
            'REPARE'           => 'bg-success text-white',
            'FACTURE'          => 'bg-success text-white',
            'LIVRE'            => 'bg-success text-white',
            'CLOTURE'          => 'bg-dark text-white',
            'IRREPARABLE'      => 'bg-danger text-white',
        ];

        return view('dashboard.admin', compact(
            'stats', 
            'dossiersDiagnostique', 
            'dossiersReparation', 
            'dossiersAttentePieces', 
            'dossiersAttenteRemplacement',
            'stockAlerts', 
            'recentDossiers', 
            'techniciens', 
            'adminKpis', 
            'statClasses'
        ));
    }

    // Génère les statistiques analytiques et financières de l'activité. Permet le filtrage par plage de dates.
    public function statistiques(Request $request)
    {
        // Plage temporelle par défaut : les 3 derniers mois jusqu'à aujourd'hui
        $dateDebut = $request->input('date_debut') 
            ? Carbon::parse($request->input('date_debut'))->startOfDay() 
            : now()->subMonths(3)->startOfDay();

        $dateFin = $request->input('date_fin') 
            ? Carbon::parse($request->input('date_fin'))->endOfDay() 
            : now()->endOfDay();

        // Requête de base sur la période sélectionnée
        $query = Dossier::whereBetween('dossiers.created_at', [$dateDebut, $dateFin]);

        // Volume total de dossiers pris en charge
        $totalDossiers = (clone $query)->count();

        // Chiffre d'affaires basé sur la somme des factures émises sur la période
        $chiffreAffaires = Facture::whereBetween('factures.created_at', [$dateDebut, $dateFin])
            ->sum('montant_total');

        // Taux d'acceptation commerciale des Devis (UC06)
        $totalDevis = Devis::whereBetween('devis.created_at', [$dateDebut, $dateFin])->count();
        $devisAcceptes = Devis::whereBetween('devis.created_at', [$dateDebut, $dateFin])
            ->where('statut', 'ACCEPTE')
            ->count();
        $tauxAcceptation = $totalDevis > 0 ? round(($devisAcceptes / $totalDevis) * 100, 1) : 0;

        // Analyse des délais de traitement (durées de résolution)
        $now = now();
        $retards = [
            '0-24h'   => (clone $query)->whereNotIn('statut', ['CLOTURE', 'LIVRE'])
                ->where('created_at', '>', $now->copy()->subHours(24))->count(),
            '24-48h'  => (clone $query)->whereNotIn('statut', ['CLOTURE', 'LIVRE'])
                ->where('created_at', '<=', $now->copy()->subHours(24))
                ->where('created_at', '>', $now->copy()->subHours(48))->count(),
            '48-72h'  => (clone $query)->whereNotIn('statut', ['CLOTURE', 'LIVRE'])
                ->where('created_at', '<=', $now->copy()->subHours(48))
                ->where('created_at', '>', $now->copy()->subHours(72))->count(),
            '>72h'    => (clone $query)->whereNotIn('statut', ['CLOTURE', 'LIVRE'])
                ->where('created_at', '<=', $now->copy()->subHours(72))->count(),
        ];

        // Calcul du taux de dossiers en retard (ouvert depuis plus de 24 heures)
        $totalOuvert = array_sum($retards);
        $totalRetard = $retards['24-48h'] + $retards['48-72h'] + $retards['>72h'];
        $tauxRetard  = $totalDossiers > 0 ? round(($totalRetard / $totalDossiers) * 100, 1) : 0;

        // Analyse de la performance des collaborateurs (Techniciens)
        $dossiersParTech = User::where('role', 'Technicien')
            ->withCount([
                'dossiers' => function ($q) use ($dateDebut, $dateFin) {
                    $q->whereBetween('created_at', [$dateDebut, $dateFin]);
                }
            ])->get();

        // Top 10 des pannes déclarées les plus fréquentes (Analyse des défaillances)
        $topPannes = (clone $query)->select('panne_declaree', DB::raw('count(*) as total'))
            ->whereNotNull('panne_declaree')
            ->groupBy('panne_declaree')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        // Top 5 des pièces détachées les plus consommées en atelier
        $topPieces = DB::table('ligne_pieces')
            ->join('pieces', 'pieces.id', '=', 'ligne_pieces.piece_id')
            ->whereBetween('ligne_pieces.created_at', [$dateDebut, $dateFin])
            ->select('pieces.nom', DB::raw('SUM(ligne_pieces.quantite) as total_qty'))
            ->groupBy('pieces.id', 'pieces.nom')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        // Top 10 des modèles de téléphones / appareils les plus fréquemment déposés en SAV
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
