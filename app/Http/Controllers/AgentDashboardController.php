<?php

namespace App\Http\Controllers;

use App\Models\Dossier;
use Illuminate\Support\Facades\Auth;

/**
 * Class AgentDashboardController
 * 
 * Gère le tableau de bord opérationnel pour les agents du Service Après-Vente (UC01 / UC05).
 * Fournit les KPI en temps réel et la liste des dossiers récents avec statut de prise en charge.
 */
class AgentDashboardController extends Controller
{
    /**
     * Affiche l'index du tableau de bord de l'agent.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();
        $total = Dossier::count();

        // ---------------------------------------------------------
        // 1. STATISTIQUES OPÉRATIONNELLES (KPIs)
        // ---------------------------------------------------------
        $stats = [
            'total'            => Dossier::count(),
            'recu'             => Dossier::where('statut', 'RECU')->count(),
            'affecte'          => Dossier::where('statut', 'AFFECTE')->count(),
            'en_diagnostic'    => Dossier::where('statut', 'EN_DIAGNOSTIC')->count(),
            'attente_devis'    => Dossier::where('statut', 'EN_ATTENTE_DEVIS')->count(),
            'en_reparation'    => Dossier::where('statut', 'EN_REPARATION')->count(),
            'attente_pieces'   => Dossier::where('statut', 'ATTENTE_PIECE')->count(),
            'prets'            => Dossier::whereIn('statut', ['REPARE', 'FACTURE', 'REMPLACEMENT_VALIDE', 'REMPLACEMENT_PRET', 'REMPLACEMENT_REFUSE'])->count(),
            'prets_aujourdhui' => Dossier::whereIn('statut', ['REPARE', 'FACTURE'])->whereDate('date_reparation', now())->count(),
            'facture'          => Dossier::where('statut', 'FACTURE')->count(),
            'cloture'          => Dossier::where('statut', 'CLOTURE')->count(),
        ];

        // Calcul des pourcentages par rapport au volume total
        $pct = [];
        foreach ($stats as $key => $value) {
            $pct[$key] = $total > 0 ? round(($value / $total) * 100, 1) : 0;
        }

        // Configuration visuelle des indicateurs clés (KPIs)
        $all_kpis = [
            ['label' => 'Total Tickets',  'val' => $stats['total'],            'icon' => 'fa-folder-open',          'class' => 'bg-soft-primary'],
            ['label' => 'À Affecter',     'val' => $stats['recu'],             'icon' => 'fa-plus-square',          'class' => 'bg-soft-secondary'],
            ['label' => 'Diagnostic',     'val' => $stats['en_diagnostic'],    'icon' => 'fa-microscope',           'class' => 'bg-soft-warning'],
            ['label' => 'Attente Devis',  'val' => $stats['attente_devis'],    'icon' => 'fa-file-invoice-dollar',  'class' => 'bg-soft-warning'],
            ['label' => 'En Réparation',  'val' => $stats['en_reparation'],    'icon' => 'fa-tools',                'class' => 'bg-soft-info'],
            ['label' => 'Attente Pièces', 'val' => $stats['attente_pieces'],   'icon' => 'fa-hourglass-half',       'class' => 'bg-soft-danger'],
            ['label' => 'Réparés / Jour', 'val' => $stats['prets_aujourdhui'], 'icon' => 'fa-check-circle',         'class' => 'bg-soft-success'],
            ['label' => 'Prêts à livrer', 'val' => $stats['prets'],            'icon' => 'fa-hand-holding-heart',   'class' => 'bg-soft-success'],
            ['label' => 'Facturés',       'val' => $stats['facture'],          'icon' => 'fa-file-invoice',         'class' => 'bg-soft-primary'],
            ['label' => 'Clôturés',       'val' => $stats['cloture'],          'icon' => 'fa-archive',              'class' => 'bg-soft-dark'],
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
            'REMPLACEMENT_PRET'               => 'Échange Prêt',
            'ATTENTE_VALIDATION_REMPLACEMENT' => 'Attente Échange'
        ];

        $recentDossiers->transform(function ($d) use ($statColors, $statLabels) {
            $d->badge_color = $statColors[$d->statut] ?? 'secondary';
            $d->badge_label = $statLabels[$d->statut] ?? $d->statut;
            return $d;
        });

        return view('dashboard.agent', compact('user', 'stats', 'pct', 'recentDossiers', 'all_kpis'));
    }
}
