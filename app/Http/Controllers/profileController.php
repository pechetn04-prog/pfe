<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
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
    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();

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
