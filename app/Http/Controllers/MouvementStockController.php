<?php

namespace App\Http\Controllers;

use App\Models\MouvementStock;
use App\Models\Piece;
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
     * Formulaire d'un mouvement manuel (entrée en stock).
     */
    public function create()
    {
        $pieces = Piece::where('actif', true)->orderBy('nom')->get();
        return view('stock.mouvement-create', compact('pieces'));
    }

    /**
     * Enregistrer un mouvement manuel (réapprovisionnement).
     */
    public function store(Request $request)
    {
        $request->validate([
            'piece_id' => 'required|exists:pieces,id',
            'quantite' => 'required|integer|min:1',
            'type' => 'required|in:ENTREE,SORTIE,AJUSTEMENT',
            'motif' => 'nullable|string|max:255',
        ]);

        $piece = Piece::findOrFail($request->piece_id);

        if ($request->type === 'ENTREE' || $request->type === 'AJUSTEMENT') {
            $piece->increment('quantite', $request->quantite);
        } elseif ($request->type === 'SORTIE') {
            if ($piece->quantite < $request->quantite) {
                return back()->with('error', 'Stock insuffisant pour cette sortie.');
            }
            $piece->decrement('quantite', $request->quantite);
        }

        MouvementStock::create([
            'piece_id' => $piece->id,
            'user_id' => Auth::id(),
            'type' => $request->type,
            'quantite' => $request->quantite,
            'motif' => $request->motif ?? 'Mouvement manuel',
        ]);

        return redirect()->route('stock.mouvements')
            ->with('success', 'Mouvement de stock enregistré.');
    }
}
