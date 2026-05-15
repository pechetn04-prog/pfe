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

        $dossiers = Dossier::where('client_id', $user->id)
            ->with(['devis', 'facture'])
            ->latest('updated_at')
            ->get();

        return view('client.dashboard', compact('user', 'dossiers'));
    }
}
