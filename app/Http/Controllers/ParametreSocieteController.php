<?php

namespace App\Http\Controllers;

use App\Models\ParametreSociete;
use App\Http\Requests\UpdateParametreSocieteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

// Gestion des paramètres et coordonnées de l'entreprise.
class ParametreSocieteController extends Controller
{
    // Affiche le formulaire de modification des paramètres.
    public function edit()
    {
        // Récupère les paramètres existants ou crée une instance vide
        $parametre = ParametreSociete::first() ?: new ParametreSociete();
        return view('societe.edit', compact('parametre'));
    }

    
    // Met à jour les paramètres et gère le téléchargement du logo.
    public function update(UpdateParametreSocieteRequest $request)
    {
        $parametre = ParametreSociete::first() ?: new ParametreSociete();
        
        $data = $request->validated();

        // Gestion du logo de l'entreprise
        if ($request->hasFile('logo')) {
            // Supprime l'ancien logo si présent
            if ($parametre->logo) {
                Storage::disk('public')->delete($parametre->logo);
            }
            // Enregistre le nouveau logo
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $parametre->fill($data);
        $parametre->save();

        return back()->with('success', 'Les paramètres de la société ont été mis à jour avec succès.');
    }
}
