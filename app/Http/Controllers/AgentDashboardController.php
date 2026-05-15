<?php

namespace App\Http\Controllers;

use App\Models\Dossier;
use Illuminate\Support\Facades\Auth;

class AgentDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $total = Dossier::count();

        $stats = [
            'total' => Dossier::count(),
            'recu' => Dossier::where('statut', 'RECU')->count(),
            'affecte' => Dossier::where('statut', 'AFFECTE')->count(),
            'en_diagnostic' => Dossier::where('statut', 'EN_DIAGNOSTIC')->count(),
            'attente_devis' => Dossier::where('statut', 'EN_ATTENTE_DEVIS')->count(),
            'en_reparation' => Dossier::where('statut', 'EN_REPARATION')->count(),
            'attente_pieces' => Dossier::where('statut', 'ATTENTE_PIECE')->count(),
            'prets' => Dossier::whereIn('statut', ['REPARE', 'FACTURE', 'REMPLACEMENT_VALIDE', 'REMPLACEMENT_PRET', 'REMPLACEMENT_REFUSE'])->count(),
            'prets_aujourdhui' => Dossier::whereIn('statut', ['REPARE', 'FACTURE'])->whereDate('date_reparation', now())->count(),
        ];

        $pct = [];
        foreach ($stats as $key => $value) {
            $pct[$key] = $total > 0 ? round(($value / $total) * 100, 1) : 0;
        }

        $recentDossiers = Dossier::with(['client', 'appareil', 'technicien'])->latest()->take(10)->get();

        return view('dashboard.agent', compact('user', 'stats', 'pct', 'recentDossiers'));
    }
}
