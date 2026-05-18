<?php

namespace App\Http\Controllers;

use App\Models\MouvementStock;
use App\Models\Piece;
use App\Http\Requests\StoreMouvementStockRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * MouvementStockController
 * UC03 — Consulter l'historique des mouvements de stock.
 */
class MouvementStockController extends Controller
{
    /**
     * Historique des mouvements.
     */
    public function index(Request $request)
    {
        $query = MouvementStock::with(['piece', 'user', 'reference.dossier'])
            ->latest();

        if ($request->filled('piece_id')) {
            $query->where('piece_id', $request->piece_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $mouvements = $query->paginate(25);
        $pieces = Piece::orderBy('nom')->get(['id', 'nom', 'reference']);

        return view('stock.mouvements', compact('mouvements', 'pieces'));
    }

    /**
     * Formulaire d'un mouvement manuel (DÉSACTIVÉ).
     */
    public function create()
    {
        abort(403, "La saisie manuelle de mouvements de stock a été désactivée. Tous les flux de stock doivent être tracés automatiquement par le système SAV.");
    }

    /**
     * Enregistrer un mouvement manuel (DÉSACTIVÉ).
     */
    public function store(StoreMouvementStockRequest $request)
    {
        abort(403, "La saisie manuelle de mouvements de stock a été désactivée. Tous les flux de stock doivent être tracés automatiquement par le système SAV.");
    }
}
