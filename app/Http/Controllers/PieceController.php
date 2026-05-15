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

        $pieces = $query->latest()->get();
        
        // Liste unique des catégories pour le filtre
        $categories = Piece::distinct()->pluck('categorie')->filter();
        $parametre = \App\Models\ParametreSociete::first();

        return view('stock.index', compact('pieces', 'categories', 'parametre'));
    }

    public function create()
    {
        return view('stock.create');
    }

    public function store(Request $request)
    {
        $piece = Piece::create($request->all());
        return redirect()->route('stock.index')->with('success', 'Pièce ajoutée.');
    }

    public function edit(Piece $piece)
    {
        return view('stock.edit', compact('piece'));
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
}
