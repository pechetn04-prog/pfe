<?php

namespace App\Http\Controllers;

use App\Models\Dossier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TechnicienDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $totalAssigne = Dossier::where('technicien_id', $user->id)->count();
        $aDiagnostiquer = Dossier::where('technicien_id', $user->id)->where('statut', 'AFFECTE')->count();
        $enReparation = Dossier::where('technicien_id', $user->id)->where('statut', 'EN_REPARATION')->count();
        $terminesMois = Dossier::where('technicien_id', $user->id)
            ->whereIn('statut', ['REPARE', 'IRREPARABLE', 'LIVRE'])
            ->whereMonth('updated_at', now()->month)
            ->count();
        $attentePieces = Dossier::where('technicien_id', $user->id)->where('statut', 'ATTENTE_PIECE')->count();
            
        $dossiersEnCours = Dossier::where('technicien_id', $user->id)
            ->whereNotIn('statut', ['LIVRE', 'CLOTURE'])
            ->count();

        $dossiersDiagnostique = Dossier::where('technicien_id', $user->id)
            ->whereIn('statut', ['AFFECTE', 'EN_DIAGNOSTIC'])
            ->with(['client', 'appareil'])
            ->latest('updated_at')
            ->take(5)
            ->get();

        $dossiersReparation = Dossier::where('technicien_id', $user->id)
            ->where('statut', 'EN_REPARATION')
            ->with(['client', 'appareil'])
            ->latest('updated_at')
            ->take(5)
            ->get();

        $dossiersTermines = Dossier::where('technicien_id', $user->id)
            ->whereIn('statut', ['REPARE', 'IRREPARABLE', 'LIVRE', 'CLOTURE', 'FACTURE'])
            ->with(['client', 'appareil'])
            ->latest('updated_at')
            ->take(10)
            ->get();

        return view('dashboard.technicien', compact(
            'totalAssigne', 'aDiagnostiquer', 'enReparation', 'attentePieces', 'terminesMois', 'dossiersEnCours', 
            'dossiersDiagnostique', 'dossiersReparation', 'dossiersTermines'
        ));
    }

    public function tickets(Request $request)
    {
        $query = Dossier::where('technicien_id', Auth::id())
            ->whereNotIn('statut', ['CLOTURE', 'LIVRE']) // Exclure les dossiers terminés
            ->with(['client', 'appareil'])
            ->latest();


        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('num_dossier', 'like', "%{$search}%")
                  ->orWhere('imei', 'like', "%{$search}%")
                  ->orWhereHas('client', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%")
                         ->orWhere('telephone', 'like', "%{$search}%");
                  });
            });
        }


        $dossiers = $query->paginate(20);
        
        $statuts = [
            'AFFECTE'        => 'Assigné',
            'EN_DIAGNOSTIC'  => 'En Diagnostic',
            'EN_ATTENTE_DEVIS' => 'Attente Devis',
            'EN_REPARATION'  => 'En Réparation',
            'ATTENTE_PIECE'  => 'En attente pièces',
            'REPARE'         => 'Réparé',
            'IRREPARABLE'    => 'Irréparable',
            'LIVRE'          => 'Restitué',
            'CLOTURE'        => 'Clôturé',
        ];

        return view('technicien.tickets', compact('dossiers', 'statuts'));
    }

    public function stock()
    {
        return redirect()->route('stock.index');
    }
}
