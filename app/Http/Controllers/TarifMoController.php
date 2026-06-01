<?php

namespace App\Http\Controllers;

use App\Models\TarifMo;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTarifMoRequest;
use App\Http\Requests\UpdateTarifMoRequest;

/**

 * Gère la grille tarifaire de la main-d'œuvre pour les interventions de l'atelier SAV.
 */
class TarifMoController extends Controller
{
    /**
     * Affiche la grille tarifaire de la main-d'œuvre.
     * 
     */
    public function index(Request $request)
    {

        $search = $request->input('search');
        //search
        $tarifs = TarifMo::when($search, function ($query, $search) {
            return $query->where('type_intervention', 'like', "%{$search}%");
        })->get();
        
        return view('tarifs_mo.index', compact('tarifs', 'search'));
    }

    /**
     * Enregistre un nouveau tarif.
     */
    public function store(StoreTarifMoRequest $request)
    {
        TarifMo::create($request->validated());
        return back()->with('success', 'Nouveau tarif de main d\'œuvre ajouté avec succès.');
    }


    
    /**
     * Met à jour un tarif existant.
     */
    public function update(UpdateTarifMoRequest $request, TarifMo $tarifMo)
    {
        $tarifMo->update($request->validated());
        return back()->with('success', 'Tarif de main d\'œuvre mis à jour avec succès.');
    }
}
