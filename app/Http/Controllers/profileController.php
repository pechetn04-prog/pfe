<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    /**
     * Afficher le formulaire de profil.
     */
    public function edit()
    {
        return view('auth.profile');
    }

    /**
     * Mettre à jour le profil de l'utilisateur connecté.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|max:255|unique:users,email,' . $user->id,
            'telephone' => 'nullable|string|max:50',
            'password'  => 'nullable|string|min:6|confirmed',

        ]);

        $data = [
            'name'      => $request->name,
            'email'     => $request->email,
            'telephone' => $request->telephone,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return back()->with('success', 'Votre profil a été mis à jour avec succès.');
    }

}
