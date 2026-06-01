<?php

namespace App\Http\Controllers;

use App\Models\Dossier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class ClientDashboardController
 * 
 * Gère le tableau de bord du portail client authentifié 
 * Fournit la liste des dossiers de réparation d'un client avec indicateurs en temps réel.
 */
class ClientDashboardController extends Controller
{
    /**
     * Affiche l'index du tableau de bord client avec ses dossiers.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // ---------------------------------------------------------
        // 1. STATISTIQUES CLIENTS
        // ---------------------------------------------------------
        $totalDossiers   = Dossier::where('client_id', $user->id)->count();
        $dossiersEnCours = Dossier::where('client_id', $user->id)
            ->whereNotIn('statut', ['LIVRE', 'CLOTURE', 'IRREPARABLE', 'DEVIS_REFUSE'])
            ->count();
        $dossiersClotures = Dossier::where('client_id', $user->id)
            ->where('statut', 'CLOTURE')
            ->count();

        // ---------------------------------------------------------
        // 2. RECUPERATION ET FORMATAGE DES DOSSIERS
        // ---------------------------------------------------------
        $query = Dossier::where('client_id', $user->id)
            ->with(['devis', 'facture', 'appareil'])
            ->latest('updated_at');

        $isArchive = $request->get('archive') == 1;

        if ($isArchive) {
            $query->whereIn('statut', ['LIVRE', 'CLOTURE', 'IRREPARABLE', 'DEVIS_REFUSE']);
        } else {
            $query->whereNotIn('statut', ['LIVRE', 'CLOTURE']);
        }

        $dossiers = $query->get();

        return view('dashboard.client', compact(
            'user', 
            'dossiers', 
            'totalDossiers', 
            'dossiersEnCours', 
            'dossiersClotures',
            'isArchive'
        ));
    }
}
