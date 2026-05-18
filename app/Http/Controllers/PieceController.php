<?php

namespace App\Http\Controllers;

use App\Models\Piece;
use App\Models\MouvementStock;
use App\Http\Requests\StorePieceRequest;
use App\Http\Requests\UpdatePieceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Ce contrôleur pilote la gestion des stocks de pièces de rechange (UC14 - Gestion du stock).
// Gère l'index des pièces, les statistiques de valeur d'inventaire, les alertes de rupture, et le cycle de vie du catalogue.
class PieceController extends Controller
{
    // Affiche le catalogue du stock avec filtres multicritères, pagination et indicateurs clés (KPIs).
    public function index(Request $request)
    {
        $query = Piece::query();

        // Recherche par nom ou référence de pièce
        if ($request->filled('search')) {
            $query->where('nom', 'like', '%' . $request->search . '%')
                  ->orWhere('reference', 'like', '%' . $request->search . '%');
        }

        // Filtrage par catégorie
        if ($request->filled('categorie')) {
            $query->where('categorie', $request->categorie);
        }

        $pieces = $query->latest()->paginate(15)->withQueryString();
        
        // Indicateurs clés du stock (KPIs) pour l'aide à la décision
        $stats = [
            'total_items'  => Piece::where('actif', true)->count(),
            'out_of_stock' => Piece::where('actif', true)->where('quantite', 0)->count(),
            'alerts_count' => Piece::where('actif', true)->whereRaw('quantite <= seuil_alerte')->where('quantite', '>', 0)->count(),
            'total_value'  => Piece::where('actif', true)->get()->sum(function($p) { return $p->quantite * $p->prix_unitaire; }),
        ];
        
        // Liste distincte des catégories actives pour le menu de filtrage
        $categories = Piece::distinct()->pluck('categorie')->filter();
        $parametre = \App\Models\ParametreSociete::first();

        return view('stock.index', compact('pieces', 'categories', 'parametre', 'stats'));
    }

    // Affiche le formulaire d'ajout d'une nouvelle pièce au stock.
    public function create()
    {
        $parametre = \App\Models\ParametreSociete::first();
        return view('stock.create', compact('parametre'));
    }

    // Enregistre une nouvelle pièce en base de données avec validation stricte.
    public function store(StorePieceRequest $request)
    {

        Piece::create($request->all());
        return redirect()->route('stock.index')->with('success', 'La pièce a été ajoutée avec succès au catalogue.');
    }

    // Affiche le formulaire de modification d'une pièce.
    public function edit(Piece $piece)
    {
        $parametre = \App\Models\ParametreSociete::first();
        return view('stock.edit', compact('piece', 'parametre'));
    }

    // Met à jour les informations d'une pièce avec validation.
    public function update(UpdatePieceRequest $request, Piece $piece)
    {

        $piece->update($request->all());
        return redirect()->route('stock.index')->with('success', 'Les informations de la pièce ont été mises à jour.');
    }

    // Supprime définitivement une pièce du stock.
    public function destroy(Piece $piece)
    {
        $piece->delete();
        return redirect()->route('stock.index')->with('success', 'La pièce a été retirée définitivement du stock.');
    }

    // Active ou désactive une pièce (masque ou affiche la pièce dans les formulaires techniques).
    public function toggleStatus(Piece $piece)
    {
        $piece->update(['actif' => !$piece->actif]);
        $status = $piece->actif ? 'activée' : 'désactivée';
        return back()->with('success', "La pièce a été {$status} avec succès.");
    }
}
