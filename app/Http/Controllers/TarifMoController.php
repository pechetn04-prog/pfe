<?php

namespace App\Http\Controllers;

use App\Models\TarifMo;
use Illuminate\Http\Request;

// Ce contrôleur gère la grille tarifaire de la main d'œuvre pour les interventions techniques du SAV.
// Permet de configurer les types de prestations (Diagnostic standard, Réparation complexe, Soudure, etc.) et leur prix unitaire.
class TarifMoController extends Controller
{
    // Affiche la liste des tarifs de main d'œuvre configurés avec filtre de recherche optionnel.
    public function index(Request $request)
    {
        $search = $request->input('search');
        
        $tarifs = TarifMo::when($search, function ($query, $search) {
            return $query->where('type_intervention', 'like', "%{$search}%");
        })->get();
        
        return view('admin.parametres.tarifs_mo.index', compact('tarifs', 'search'));
    }

    // Enregistre un nouveau tarif de prestation technique dans le système.
    public function store(Request $request)
    {
        // Validation de base pour assurer l'intégrité des données financières
        $request->validate([
            'type_intervention' => 'required|string|max:255',
            'montant'           => 'required|numeric|min:0',
        ]);

        TarifMo::create($request->all());
        return back()->with('success', 'Nouveau tarif de main d\'œuvre ajouté avec succès.');
    }

    // Met à jour un tarif de prestation technique existant.
    public function update(Request $request, TarifMo $tarifMo)
    {
        // Validation des modifications
        $request->validate([
            'type_intervention' => 'required|string|max:255',
            'montant'           => 'required|numeric|min:0',
        ]);

        $tarifMo->update($request->all());
        return back()->with('success', 'Tarif de main d\'œuvre mis à jour avec succès.');
    }

    // Supprime un tarif de prestation de la base de données.
    public function destroy(TarifMo $tarifMo)
    {
        $tarifMo->delete();
        return back()->with('success', 'Tarif de main d\'œuvre supprimé de la grille.');
    }

    // Active ou désactive un tarif dans la grille pour empêcher son utilisation future.
    public function toggleStatus(TarifMo $tarifMo)
    {
        $tarifMo->update(['actif' => !$tarifMo->actif]);
        return back()->with('success', 'Le statut d\'activation du tarif a été mis à jour.');
    }
}
