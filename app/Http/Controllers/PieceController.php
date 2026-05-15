<?php

namespace App\Http\Controllers;

use App\Models\Piece;
use App\Models\MouvementStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PieceController extends Controller
{
    public function index(Request $request)
    {
        $query = Piece::query();

        if ($request->filled('search')) {
            $query->where('nom', 'like', '%' . $request->search . '%')
                  ->orWhere('reference', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        $pieces = $query->latest()->paginate(15)->withQueryString();
        
        // Statistiques pour les cartes
        $stats = [
            'total_items' => Piece::where('actif', true)->count(),
            'out_of_stock' => Piece::where('actif', true)->where('quantite', 0)->count(),
            'alerts_count' => Piece::where('actif', true)->whereRaw('quantite <= seuil_alerte')->where('quantite', '>', 0)->count(),
            'total_value' => Piece::where('actif', true)->get()->sum(function($p) { return $p->quantite * $p->prix_unitaire; }),
        ];
        
        // Liste unique des catégories pour le filtre
        $categories = Piece::distinct()->pluck('categorie')->filter();
        $parametre = \App\Models\ParametreSociete::first();

        return view('stock.index', compact('pieces', 'categories', 'parametre', 'stats'));
    }

    public function create()
    {
        $parametre = \App\Models\ParametreSociete::first();
        return view('stock.create', compact('parametre'));
    }

    public function store(Request $request)
    {
        $piece = Piece::create($request->all());
        return redirect()->route('stock.index')->with('success', 'Pièce ajoutée.');
    }

    public function edit(Piece $piece)
    {
        $parametre = \App\Models\ParametreSociete::first();
        return view('stock.edit', compact('piece', 'parametre'));
    }

    public function update(Request $request, Piece $piece)
    {
        $piece->update($request->all());
        return redirect()->route('stock.index')->with('success', 'Pièce mise à jour.');
    }

    public function destroy(Piece $piece)
    {
        $piece->delete();
        return redirect()->route('stock.index')->with('success', 'Pièce supprimée.');
    }

    public function toggleStatus(Piece $piece)
    {
        $piece->update(['actif' => !$piece->actif]);
        $status = $piece->actif ? 'activée' : 'désactivée';
        return back()->with('success', "Pièce {$status} avec succès.");
    }
}
