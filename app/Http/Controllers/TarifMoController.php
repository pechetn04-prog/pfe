<?php

namespace App\Http\Controllers;

use App\Models\TarifMo;
use Illuminate\Http\Request;

class TarifMoController extends Controller
{
    public function index()
    {
        $tarifs = TarifMo::all();
        return view('admin.tarifs_mo.index', compact('tarifs'));
    }

    public function store(Request $request)
    {
        TarifMo::create($request->all());
        return back()->with('success', 'Tarif ajouté.');
    }

    public function update(Request $request, TarifMo $tarifMo)
    {
        $tarifMo->update($request->all());
        return back()->with('success', 'Tarif mis à jour.');
    }

    public function destroy(TarifMo $tarifMo)
    {
        $tarifMo->delete();
        return back()->with('success', 'Tarif supprimé.');
    }

    public function toggleStatus(TarifMo $tarifMo)
    {
        $tarifMo->update(['actif' => !$tarifMo->actif]);
        return back()->with('success', 'Statut mis à jour.');
    }
}
