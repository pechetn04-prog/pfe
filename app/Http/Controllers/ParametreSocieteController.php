<?php

namespace App\Http\Controllers;

use App\Models\ParametreSociete;
use App\Http\Requests\UpdateParametreSocieteRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ParametreSocieteController extends Controller
{
    public function edit()
    {
        $parametre = ParametreSociete::first() ?: new ParametreSociete();
        return view('admin.parametres-societe.edit', compact('parametre'));
    }

    public function update(UpdateParametreSocieteRequest $request)
    {
        $parametre = ParametreSociete::first() ?: new ParametreSociete();
        
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            // Supprimer l'ancien logo si nécessaire
            if ($parametre->logo) {
                Storage::disk('public')->delete($parametre->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $parametre->fill($data);
        $parametre->save();

        return back()->with('success', 'Les paramètres de la société ont été mis à jour avec succès.');
    }
}
