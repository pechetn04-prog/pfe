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

/**
 * Class AdminDashboardController
 * 
 * Ce contrôleur gère le tableau de bord de l'administrateur et le module analytique
 * Il centralise et fournit l'ensemble des indicateurs clés de performance (KPIs) opérationnels,
 * logistiques et financiers pour le pilotage stratégique du Service Après-Vente (SAV).
 */
class AdminDashboardController extends Controller
{
    /**
     * Affiche le tableau de bord de l'administrateur.
     * 
     * Cette méthode extrait de nombreuses statistiques indispensables à la prise de décision :
     * - Volumétrie des dossiers par statut individuel (pour les graphiques)
     * - Répartition selon l'éligibilité à la garantie commerciale (UC03)
     * - Alertes de stock critique (composants sous le seuil d'alerte, UC14)
     * - Dossiers récents en diagnostic, en réparation atelier et en attente de réapprovisionnement
     * - Demandes de retrait ou réaffectation de dossiers soumises par les techniciens (UC12)
     * - Liste des techniciens actifs pour des assignations rapides
     */
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
        // Initialisation de tous les statuts à 0 pour assurer leur affichage complet
        foreach ($statusLabels as $label) {
            $stats['status_distribution'][$label] = 0;
        }

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
        $stockAlerts = Piece::whereRaw('quantite <= seuil_alerte')
            ->orderBy('quantite', 'asc')
            ->take(10)
            ->get();

        // Dossiers au tout début du flux SAV (Nouveaux ou en cours d'analyse)
        $dossiersDiagnostique = Dossier::with(['client', 'appareil'])
            ->whereIn('statut', ['RECU', 'AFFECTE', 'EN_DIAGNOSTIC'])
            ->latest()
            ->take(6)
            ->get();

        // Dossiers actuellement en cours d'intervention sur table
        $dossiersReparation = Dossier::with(['client', 'appareil'])
            ->where('statut', 'EN_REPARATION')
            ->latest()
            ->take(6)
            ->get();

        // Dossiers bloqués en attente de réapprovisionnement de pièces
        $dossiersAttentePieces = Dossier::with(['client', 'appareil'])
            ->where('statut', 'ATTENTE_PIECE')
            ->latest()
            ->take(6)
            ->get();

        // Dossiers en attente de validation de remplacement sous garantie
        $dossiersAttenteRemplacement = Dossier::with(['client', 'appareil'])
            ->where('statut', 'ATTENTE_VALIDATION_REMPLACEMENT')
            ->latest()
            ->take(6)
            ->get();

        // Historique récent des 10 derniers dossiers créés
        $recentDossiers = Dossier::with(['client', 'appareil'])->latest()->take(10)->get();

        // Liste des techniciens actifs pour les assignations rapides
        $techniciens = User::where('role', 'Technicien')->where('actif', true)->get();

        return view('dashboard.admin', compact(
            'stats',
            'dossiersDiagnostique',
            'dossiersReparation',
            'dossiersAttentePieces',
            'dossiersAttenteRemplacement',
            'stockAlerts',
            'recentDossiers',
            'techniciens'
        ));
    }

}

