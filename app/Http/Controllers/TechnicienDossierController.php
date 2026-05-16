<?php

namespace App\Http\Controllers;

use App\Models\Dossier;
use App\Models\Piece;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * TechnicienDossierController
 * UC13 : Consulter la liste des dossiers assignés au technicien connecté.
 */
class TechnicienDossierController extends Controller
{
    /**
     * Liste des dossiers assignés au technicien connecté.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Dossier::where('technicien_id', $user->id)
            ->with(['client'])
            ->latest('updated_at');

        // Filtre par statut : si non spécifié, on exclut les dossiers clôturés
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        } else {
            $query->where('statut', '!=', 'CLOTURE');
        }

        $dossiers = $query->paginate(15);

        $statuts = [
            'AFFECTE'            => 'Affecté',
            'EN_DIAGNOSTIC'      => 'En diagnostic',
            'EN_ATTENTE_DEVIS'   => 'Attente Devis',
            'EN_REPARATION'      => 'En réparation',
            'ATTENTE_PIECE'      => 'Attente pièce',
            'REPARE'             => 'Réparé',
            'IRREPARABLE'        => 'Irréparable',
            'LIVRE'              => 'Restitué',
            'CLOTURE'            => 'Clôturé',
        ];

        return view('technicien.tickets', compact('dossiers', 'statuts'));
    }
}
