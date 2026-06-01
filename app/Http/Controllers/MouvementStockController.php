<?php

namespace App\Http\Controllers;

use App\Models\MouvementStock;
use App\Models\Piece;
use App\Http\Requests\StoreMouvementStockRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Gère l'historique et les mouvements manuels (entrées/sorties) du stock.
class MouvementStockController extends Controller
{
    // Affiche la liste des mouvements de stock avec filtres.
    public function index(Request $request)
    {
        $query = MouvementStock::with(['piece', 'user', 'reference.dossier'])
            ->latest();

        // Filtrer par pièce
        if ($request->filled('piece_id')) {
            $query->where('piece_id', $request->piece_id);
        }

        // Filtrer par type (entrée / sortie)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $mouvements = $query->paginate(25);
        
        $pieces = Piece::orderBy('nom')->get(['id', 'nom', 'reference']);

        return view('stock.mouvements', compact('mouvements', 'pieces'));
    }

    // Redirige vers la liste principale du stock.
    public function create()
    {
        return redirect()->route('stock.index')->with('info', 'Utilisez le bouton "Mouvement" sur l\'inventaire pour enregistrer un mouvement.');
    }

    // Enregistre une entrée ou une sortie de stock.
    public function store(StoreMouvementStockRequest $request)
    {
        $piece = Piece::findOrFail($request->piece_id);
        $quantite = $request->quantite;
        $type = strtolower($request->type); // 'entree' ou 'sortie'

        // Gestion de la sortie de stock
        if ($type === 'sortie') {
            if ($piece->quantite < $quantite) {
                return back()->with('error', "Stock insuffisant. Stock actuel disponible : {$piece->quantite}");
            }
            $piece->decrement('quantite', $quantite); // Diminuer la quantité
        } else {
            $piece->increment('quantite', $quantite); // Augmenter la quantité
        }

        // Enregistrer l'historique du mouvement
        MouvementStock::create([
            'piece_id' => $piece->id,
            'user_id'  => Auth::id(),
            'type'     => $type,
            'quantite' => $quantite,
            'motif'    => $request->motif ?? "Mouvement manuel d'ajustement d'inventaire",
        ]);

        return redirect()->route('stock.index')->with('success', "Mouvement de stock enregistré avec succès. Nouveau stock : {$piece->quantite}");
    }
}
