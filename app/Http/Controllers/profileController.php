<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

// Ce contrôleur gère la consultation et la mise à jour des informations de profil de l'utilisateur connecté.
class ProfileController extends Controller
{
    // Affiche le formulaire d'édition du profil personnel (Nom, Email, Téléphone, Mot de passe).
    public function edit()
    {
        return view('auth.profile');
    }

    // Met à jour les informations du profil de l'utilisateur connecté avec validation.
    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();

        $data = [
            'name'      => $request->name,
            'email'     => $request->email,
            'telephone' => $request->telephone,
        ];

        // Hachage sécurisé du mot de passe uniquement s'il est spécifié
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Votre profil a été mis à jour avec succès.');
    }
}
