<?php

namespace App\Http\Controllers;

use App\Models\Dossier;
use Illuminate\Support\Facades\Auth;

/**
 * 
 * Gère le tableau de bord opérationnel pour les agents du Service Après-Vente
 * Fournit les KPI en temps réel et la liste des dossiers récents avec statut de prise en charge.
 */
class AgentDashboardController extends Controller
{
    /**
     * Affiche l'index du tableau de bord de l'agent.
     */
    public function index()
    {
        $user = Auth::user();
        $total = Dossier::count();

        // ---------------------------------------------------------
        // 1. STATISTIQUES OPÉRATIONNELLES (KPIs)
        // ---------------------------------------------------------
        $stats = [
            'total'                => Dossier::count(),
            'affecte'              => Dossier::where('statut', 'AFFECTE')->count(),
            'attente_devis'        => Dossier::where('statut', 'EN_ATTENTE_DEVIS')->count(),
            'prets'                => Dossier::whereIn('statut', ['REPARE', 'FACTURE', 'REMPLACEMENT_VALIDE', 'REMPLACEMENT_PRET', 'REMPLACEMENT_REFUSE'])->count(),
            'attente_remplacement' => Dossier::where('statut', 'ATTENTE_VALIDATION_REMPLACEMENT')->count(),
            'cloture'              => Dossier::where('statut', 'CLOTURE')->count(),
        ];





        // ---------------------------------------------------------
        // 2. ACTIVITÉS RÉCENTES (DOSSIERS RÉCEMMENT MIS À JOUR)
        // ---------------------------------------------------------
        $recentDossiers = Dossier::with(['client', 'appareil', 'technicien'])
            ->latest()
            ->take(7)
            ->get();

        // Mapping des couleurs et libellés des statuts
        $statColors = [
            'RECU'                            => 'secondary',
            'AFFECTE'                         => 'info',
            'EN_DIAGNOSTIC'                   => 'warning', 
            'EN_REPARATION'                   => 'primary',
            'EN_ATTENTE_DEVIS'                => 'warning', 
            'REPARE'                          => 'success',
            'FACTURE'                         => 'success',
            'LIVRE'                           => 'success', 
            'CLOTURE'                         => 'dark',
            'IRREPARABLE'                     => 'danger',
            'DEVIS_REFUSE'                    => 'danger',
            'ATTENTE_PIECE'                   => 'danger',
            'REMPLACEMENT_PRET'               => 'success',
            'ATTENTE_VALIDATION_REMPLACEMENT' => 'warning'
        ];

        $statLabels = [
            'RECU'                            => 'Reçu',
            'AFFECTE'                         => 'Affecté',
            'EN_DIAGNOSTIC'                   => 'Diagnostic', 
            'EN_REPARATION'                   => 'Réparation',
            'EN_ATTENTE_DEVIS'                => 'Attente Devis', 
            'REPARE'                          => 'Réparé',
            'FACTURE'                         => 'Facturé',
            'LIVRE'                           => 'Livré', 
            'CLOTURE'                         => 'Clôturé',
            'IRREPARABLE'                     => 'Irréparable',
            'DEVIS_REFUSE'                    => 'Refusé',
            'ATTENTE_PIECE'                   => 'Attente Pièce',
            'REMPLACEMENT_PRET'               => 'Remplacement Prêt',
            'ATTENTE_VALIDATION_REMPLACEMENT' => 'Attente Remplacement'
        ];

        $recentDossiers->transform(function ($d) use ($statColors, $statLabels) {
            $d->badge_color = $statColors[$d->statut] ?? 'secondary';
            $d->badge_label = $statLabels[$d->statut] ?? $d->statut;
            return $d;
        });

        return view('dashboard.agent', compact('user', 'stats', 'recentDossiers'));
    }
}
