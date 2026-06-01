<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\Dossier;
use App\Models\User;
use App\Models\Devis;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// Ce contrôleur gère la génération des statistiques analytiques et financières du service après-vente (SAV).
// Il permet le suivi de la performance globale, des pièces consommées, des délais de traitement et du chiffre d'affaires.
class StatistiqueController extends Controller
{
    // Génère les rapports analytiques et financiers avec possibilité de filtrer sur une plage de dates personnalisée.
    public function index(Request $request)
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

        // Taux d'acceptation commerciale des Devis
        $totalDevis = Devis::whereBetween('devis.created_at', [$dateDebut, $dateFin])->count();
        $devisAcceptes = Devis::whereBetween('devis.created_at', [$dateDebut, $dateFin])
            ->where('statut', 'ACCEPTE')
            ->count();
        $tauxAcceptation = $totalDevis > 0 ? round(($devisAcceptes / $totalDevis) * 100, 1) : 0;





        // Analyse des délais de traitement (durées de résolution)

        //subHours(24) : retire 24 heures à une date.
        //copy() : fait une copie de la date pour ne pas modifier l'originale.
        //clone $query : fait une copie de la requête afin d'effectuer plusieurs calculs différents.
        $now = now();
        $retards = [
            '0-24h' => (clone $query)->whereNotIn('statut', ['CLOTURE', 'LIVRE'])
                ->where('created_at', '>', $now->copy()->subHours(24))->count(),
            '24-48h' => (clone $query)->whereNotIn('statut', ['CLOTURE', 'LIVRE'])
                ->where('created_at', '<=', $now->copy()->subHours(24))
                ->where('created_at', '>', $now->copy()->subHours(48))->count(),
            '48-72h' => (clone $query)->whereNotIn('statut', ['CLOTURE', 'LIVRE'])
                ->where('created_at', '<=', $now->copy()->subHours(48))
                ->where('created_at', '>', $now->copy()->subHours(72))->count(),
            '>72h' => (clone $query)->whereNotIn('statut', ['CLOTURE', 'LIVRE'])
                ->where('created_at', '<=', $now->copy()->subHours(72))->count(),
        ];

        // Calcul du taux de dossiers en retard (ouvert depuis plus de 24 heures)
        $totalOuvert = array_sum($retards);
        $totalRetard = $retards['24-48h'] + $retards['48-72h'] + $retards['>72h'];
        $tauxRetard = $totalDossiers > 0 ? round(($totalRetard / $totalDossiers) * 100, 1) : 0;


        // Analyse de la performance des collaborateurs (Techniciens)
        $dossiersParTech = User::where('role', 'Technicien')
            ->withCount([
                'dossiers' => function ($q) use ($dateDebut, $dateFin) {
                    $q->whereBetween('created_at', [$dateDebut, $dateFin]);
                }
            ])->get();

        // Top 10 des catégories de pannes prédéfinies les plus fréquentes
        
        $pannesRaw = (clone $query)
            ->select('panne_declaree')
            ->whereNotNull('panne_declaree')
            ->where('panne_declaree', 'like', '[%')
            ->get()
            ->pluck('panne_declaree');

        $pannesCount = [];
        foreach ($pannesRaw as $panneDeclaree) {
            // Extraire le contenu entre les premiers crochets : [Cat1, Cat2]
            if (preg_match('/^\[([^\]]+)\]/', $panneDeclaree, $matches)) {
                $categories = array_map('trim', explode(',', $matches[1]));
                foreach ($categories as $cat) {
                    if ($cat !== '') {
                        $pannesCount[$cat] = ($pannesCount[$cat] ?? 0) + 1;
                    }
                }
            }
        }
        // Trier par fréquence décroissante et prendre le top 10
        //arsort function php Trier un tableau par valeurs en ordre décroissant tout en conservant les clés.
        arsort($pannesCount);
        $topPannes = collect(array_slice($pannesCount, 0, 10, true))
            ->map(fn($count, $label) => (object)['panne_declaree' => $label, 'total' => $count])
            ->values();


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

        // Distribution globale de tous les statuts individuels sur la période filtrée
        // Traduction esthétique des statuts techniques pour le graphique
        $statusLabels = [
            'AFFECTE'                         => 'Affecté',
            'EN_DIAGNOSTIC'                   => 'En Diagnostic',
            'EN_ATTENTE_DEVIS'                => 'En Attente Devis',
            'DEVIS_REFUSE'                    => 'Devis Refusé',
            'EN_REPARATION'                   => 'En Réparation',
            'REPARE'                          => 'Réparé',
            'IRREPARABLE'                     => 'Irréparable',
            'ATTENTE_PIECE'                   => 'Attente Pièce',
            'FACTURE'                         => 'Facturé',
            'LIVRE'                           => 'Restitué',
            'CLOTURE'                         => 'Clôturé',
            'REMPLACEMENT_VALIDE'             => 'Remplacement Validé',
            'REMPLACEMENT_REFUSE'             => 'Remplacement Refusé'
        ];

        // Regroupement SQL et comptage des dossiers par statut sur la période filtrée
        $statusDistribution = [];
        // Initialisation de tous les statuts à 0 pour qu'ils soient tous affichés dans le graphique
        foreach ($statusLabels as $label) {
            $statusDistribution[$label] = 0;
        }

        $dossierCounts = (clone $query)->select('statut', \DB::raw('count(*) as count'))
            ->groupBy('statut')
            ->get();

        foreach ($dossierCounts as $dc) {
            if (isset($statusLabels[$dc->statut])) {
                $label = $statusLabels[$dc->statut];
                $statusDistribution[$label] = $dc->count;
            }
        }

        // Répartition des dossiers selon l'éligibilité à la garantie sur la période filtrée
        $warrantyDistribution = [
            //C'est comme une photocopie (clone $query) pour pouvoir faire plusieurs requêtes différentes à partir de la même base sans interférer les unes avec les autres.
            'Sous Garantie'   => (clone $query)->where('sous_garantie', true)->where('garantie_annulee', false)->count(),
            'Hors Garantie'   => (clone $query)->where('sous_garantie', false)->count(),
            'Garantie Exclue' => (clone $query)->where('garantie_annulee', true)->count(),
        ];
//envoyer les données du Controller vers la View
//return view() affiche la page Blade, tandis que compact() 
//permet de transmettre les données calculées dans le contrôleur 
//vers la vue afin de les afficher dans le tableau de bord des statistiques.
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
            'dateFin',
            'statusDistribution',
            'warrantyDistribution'
        ));
    }
}
