<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

// Contrôleur pour la gestion du profil utilisateur.
class ProfileController extends Controller
{
    // Affiche le formulaire d'édition du profil.
    public function edit()
    {
        return view('auth.profile');
    }

    // Met à jour les informations du profil.
    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();

        $data = [
            'name'      => $request->name,
            'email'     => $request->email,
            'telephone' => $request->telephone,
        ];

        // Hachage du mot de passe s'il est renseigné
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Votre profil a été mis à jour avec succès.');
    }
}
