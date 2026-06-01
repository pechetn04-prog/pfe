<?php

namespace App\Http\Controllers;

use App\Models\Piece;
use App\Models\MouvementStock;
use App\Http\Requests\StorePieceRequest;
use App\Http\Requests\UpdatePieceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ParametreSociete;

//gestion du stock de pièces détachées de l'atelier SAV, avec fonctionnalités d'ajout, modification, suppression, et indicateurs clés pour la gestion proactive du stock.
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
            'total_items'  => Piece::count(),
            'out_of_stock' => Piece::where('quantite', 0)->count(),
            'alerts_count' => Piece::whereRaw('quantite <= seuil_alerte')->where('quantite', '>', 0)->count(),
            'total_value'  => Piece::all()->sum(function($p) { return $p->quantite * $p->prix_unitaire; }),
        ];
        
        // Liste distincte des catégories actives pour le menu de filtrage
        $categories = Piece::distinct()->pluck('categorie')->filter();
        $parametre = ParametreSociete::first();

        return view('stock.index', compact('pieces', 'categories', 'parametre', 'stats'));
    }

    // Affiche le formulaire d'ajout d'une nouvelle pièce au stock.
    public function create()
    {
        $parametre = ParametreSociete::first();
        return view('stock.create', compact('parametre'));
    }

    // Enregistre une nouvelle pièce en base de données avec validation stricte.
    public function store(StorePieceRequest $request)
    {

        Piece::create($request->all());
        return redirect()->route('stock.index')->with('success', 'La pièce a été ajoutée avec succès au catalogue.');
    }

//edit()::Affiche le formulaire de modification	
//update():::Enregistre les modifications

    // Affiche le formulaire de modification d'une pièce.
    public function edit(Piece $piece)
    {
        $parametre = ParametreSociete::first();
        return view('stock.edit', compact('piece', 'parametre'));
    }


    // Met à jour les informations d'une pièce avec validation.
    public function update(UpdatePieceRequest $request, Piece $piece)
    {

        $piece->update($request->all());
        return redirect()->route('stock.index')->with('success', 'Les informations de la pièce ont été mises à jour.');
    }


    // Active ou désactive une pièce (masque ou affiche la pièce dans les formulaires techniques).
    public function toggleStatus(Piece $piece)
    {
        $piece->update(['actif' => !$piece->actif]);
        $status = $piece->actif ? 'activée' : 'désactivée';
        return back()->with('success', "La pièce a été {$status} avec succès.");
    }
}
