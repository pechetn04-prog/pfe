<?php

namespace App\Http\Controllers;

use App\Models\Dossier;
use App\Models\Piece;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Ce contrôleur gère la consultation des dossiers (tickets) assignés au technicien authentifié (UC13).
// Il formate les indicateurs de garantie et les styles des statuts pour l'affichage de l'espace atelier.
class TechnicienDossierController extends Controller
{
    // Affiche la liste paginée des tickets de réparation affectés au technicien avec filtres.
    public function index(Request $request)
    {
        $user = Auth::user();

        // Récupération des dossiers assignés, triés par date de mise à jour récente
        $query = Dossier::where('technicien_id', $user->id)
            ->with(['client'])
            ->latest('updated_at');

        // Filtrage dynamique par statut. Par défaut, on cache les dossiers clos pour ne pas surcharger la vue.
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        } else {
            $query->where('statut', '!=', 'CLOTURE');
        }

        $dossiers = $query->paginate(15);

        // Nomenclature des libellés de l'état SAV
        $statuts = [
            'AFFECTE' => 'Affecté',
            'EN_DIAGNOSTIC' => 'En diagnostic',
            'EN_ATTENTE_DEVIS' => 'Attente Devis',
            'EN_REPARATION' => 'En réparation',
            'ATTENTE_PIECE' => 'Attente pièce',
            'REPARE' => 'Réparé',
            'IRREPARABLE' => 'Irréparable',
            'LIVRE' => 'Restitué',
            'CLOTURE' => 'Clôturé',
        ];

        // Mapping des styles graphiques Bootstrap (couleurs des badges)
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

        // Formatage des données dans le contrôleur (respect du pattern MVC)
        $dossiers->getCollection()->transform(function ($d) use ($map, $statuts) {
            // Logique de calcul de l'éligibilité de la garantie commerciale (UC03)
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
}
