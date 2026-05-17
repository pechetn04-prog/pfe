<?php

namespace App\Http\Controllers;

use App\Models\ParametreSociete;
use App\Http\Requests\UpdateParametreSocieteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ParametreSocieteController extends Controller
{
    // Affiche le formulaire d'édition des paramètres de la société (Identité, Logo, Coordonnées).
    public function edit()
    {
        // Récupère la première configuration existante, ou en crée une nouvelle instance vide
        $parametre = ParametreSociete::first() ?: new ParametreSociete();
        return view('admin.parametres.societe.edit', compact('parametre'));
    }

    // Met à jour les paramètres de la société avec gestion de l'upload du logo.
    public function update(UpdateParametreSocieteRequest $request)
    {
        $parametre = ParametreSociete::first() ?: new ParametreSociete();
        
        $data = $request->validated();

        // Gestion de l'upload et de la mise à jour du logo de l'entreprise
        if ($request->hasFile('logo')) {
            // Suppression de l'ancien fichier logo s'il existe déjà
            if ($parametre->logo) {
                Storage::disk('public')->delete($parametre->logo);
            }
            // Enregistrement du nouveau logo dans le disque public (dossier logos)
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $parametre->fill($data);
        $parametre->save();

        return back()->with('success', 'Les paramètres de la société ont été mis à jour avec succès.');
    }
}
