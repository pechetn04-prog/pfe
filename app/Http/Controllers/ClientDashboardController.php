<?php

namespace App\Http\Controllers;

use App\Models\Dossier;
use Illuminate\Support\Facades\Auth;

class ClientDashboardController extends Controller
{
    /**
     * UC10 — Tableau de bord client : liste de ses dossiers SAV.
     */
    public function index()
    {
        $user = Auth::user();

        $totalDossiers = Dossier::where('client_id', $user->id)->count();
        $dossiersEnCours = Dossier::where('client_id', $user->id)
            ->whereNotIn('statut', ['LIVRE', 'CLOTURE', 'IRREPARABLE', 'DEVIS_REFUSE'])
            ->count();
        $dossiersPrets = Dossier::where('client_id', $user->id)
            ->where('statut', 'REPARE')
            ->count();

        $dossiers = Dossier::where('client_id', $user->id)
            ->with(['devis', 'facture', 'appareil'])
            ->latest('updated_at')
            ->get();

        return view('client.dashboard', compact('user', 'dossiers', 'totalDossiers', 'dossiersEnCours', 'dossiersPrets'));
    }
}
