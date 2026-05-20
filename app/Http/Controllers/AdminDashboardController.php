<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\Piece;
use App\Models\Dossier;
use App\Models\User;
use App\Models\Devis;
use App\Models\DemandeRejet;
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
            'total' => Dossier::count(),
            'recu' => Dossier::where('statut', 'RECU')->count(),
            'en_diagnostic' => Dossier::where('statut', 'EN_DIAGNOSTIC')->count(),
            'attente_devis' => Dossier::where('statut', 'EN_ATTENTE_DEVIS')->count(),
            'en_reparation' => Dossier::where('statut', 'EN_REPARATION')->count(),
            'attente_pieces' => Dossier::where('statut', 'ATTENTE_PIECE')->count(),
            'cloture' => Dossier::whereIn('statut', ['LIVRE', 'CLOTURE'])->count(),
            'users' => User::count(),
            'irreparable' => Dossier::where('statut', 'IRREPARABLE')->count(),

            // Flux spécifiques (demandes d'échange sous garantie et demandes de retrait)
            'attente_validation_remplacement' => Dossier::where('statut', 'ATTENTE_VALIDATION_REMPLACEMENT')->count(),
            'demandes_rejet_count' => DemandeRejet::where('statut', 'EN_ATTENTE')->count(),
            'demandes_rejet_recent' => DemandeRejet::with(['dossier', 'user'])
                ->where('statut', 'EN_ATTENTE')
                ->latest()
                ->take(3)
                ->get(),
        ];

        // Distribution globale de TOUS les statuts individuels pour le graphique de synthèse
        $statusLabels = [
            'RECU' => 'Reçu',
            'AFFECTE' => 'Affecté',
            'EN_DIAGNOSTIC' => 'En Diagnostic',
            'EN_ATTENTE_DEVIS' => 'En Attente Devis',
            'DEVIS_REFUSE' => 'Devis Refusé',
            'EN_REPARATION' => 'En Réparation',
            'REPARE' => 'Réparé',
            'IRREPARABLE' => 'Irréparable',
            'ATTENTE_PIECE' => 'Attente Pièce',
            'ATTENTE_VALIDATION_REMPLACEMENT' => 'Attente Remplacement',
            'ATTENTE_REMPLACEMENT' => 'Attente Remplacement Prêt',
            'REMPLACEMENT_PRET' => 'Remplacement Prêt',
            'FACTURE' => 'Facturé',
            'LIVRE' => 'Restitué',
            'CLOTURE' => 'Clôturé',
            'ANNULE' => 'Annulé',
            'REMPLACEMENT_VALIDE' => 'Remplacement Validé',
            'REMPLACEMENT_REFUSE' => 'Remplacement Refusé'
        ];

        $stats['status_distribution'] = [];
        $dossierCounts = Dossier::select('statut', DB::raw('count(*) as count'))
            ->groupBy('statut')
            ->get();

        foreach ($dossierCounts as $dc) {
            $label = $statusLabels[$dc->statut] ?? str_replace('_', ' ', $dc->statut);
            $stats['status_distribution'][$label] = $dc->count;
        }

        // Répartition des dossiers selon l'éligibilité à la garantie commerciale (UC03)
        $stats['warranty_distribution'] = [
            'Sous Garantie' => Dossier::where('sous_garantie', true)->where('garantie_annulee', false)->count(),
            'Hors Garantie' => Dossier::where('sous_garantie', false)->count(),
            'Garantie Exclue' => Dossier::where('garantie_annulee', true)->count(),
        ];

        // Alertes de stock critique (quantité en stock <= seuil d'alerte configuré) (UC14)
        $stockAlerts = Piece::where('actif', true)
            ->whereRaw('quantite <= seuil_alerte')
            ->orderBy('quantite', 'asc')
            ->take(10)
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

        // Historique récent des 10 derniers dossiers créés
        $recentDossiers = Dossier::with(['client', 'appareil'])->latest()->take(10)->get();

        // Liste des techniciens actifs pour les assignations rapides
        $techniciens = User::where('role', 'Technicien')->where('actif', true)->get();

        // Mapping esthétique des 7 KPIs pour la grille premium du dashboard
        $adminKpis = [
            ['label' => 'TOTAL DOSSIERS', 'val' => $stats['total'], 'icon' => 'fa-folder-open', 'class' => 'bg-soft-sky'],
            ['label' => 'ATTENTE DEVIS', 'val' => $stats['attente_devis'], 'icon' => 'fa-file-invoice-dollar', 'class' => 'bg-soft-info'],
            ['label' => 'ATTENTE PIÈCES', 'val' => $stats['attente_pieces'], 'icon' => 'fa-clock', 'class' => 'bg-soft-danger'],
            ['label' => 'ATTENTE REMPLACEMENT', 'val' => $stats['attente_validation_remplacement'], 'icon' => 'fa-exchange-alt', 'class' => 'bg-soft-warning text-warning'],
            ['label' => 'IRRÉPARABLES', 'val' => $stats['irreparable'], 'icon' => 'fa-times-circle', 'class' => 'bg-soft-danger text-danger'],
            ['label' => 'LIVRÉS / CLOS', 'val' => $stats['cloture'], 'icon' => 'fa-check-double', 'class' => 'bg-soft-slate'],
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
            'adminKpis'
        ));
    }

}

