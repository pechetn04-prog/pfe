<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * UserController
 * Gestion des comptes utilisateurs du système SAV (Admin/Agent).
 */
class UserController extends Controller
{
    /**
     * Liste des utilisateurs avec filtre optionnel par rôle.
     */
    public function index(Request $request)
    {
        $query = User::query()->latest();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(20);

        return view('users.index', compact('users'));
    }

    /**
     * Formulaire de création d'un utilisateur.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Enregistrer un nouvel utilisateur.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'role'      => 'required|in:Admin,Agent,Technicien,Client',
            'telephone' => 'nullable|string|max:20',
            'password'  => 'required|string|min:8|confirmed',
            'specialites' => 'nullable|array',
        ]);

        $specialite = null;
        if ($request->role === 'Technicien' && $request->has('specialites')) {
            $specialite = implode(', ', $request->specialites);
        }

        User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => $request->role,
            'telephone'  => $request->telephone,
            'specialite' => $specialite,
            'actif'      => true,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Compte utilisateur créé avec succès.');
    }

    /**
     * Formulaire d'édition d'un utilisateur.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Mettre à jour un utilisateur.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        if ($request->role === 'Technicien' && $request->has('specialites')) {
            $data['specialite'] = implode(', ', $request->specialites);
        } else {
            $data['specialite'] = null;
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Supprimer un utilisateur.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Utilisateur supprimé.');
    }

    /**
     * Activer / désactiver un compte utilisateur.
     */
    public function toggleStatus(User $user)
    {
        $user->update(['actif' => !$user->actif]);

        return response()->json([
            'success' => true,
            'actif'   => $user->actif,
        ]);
    }
}
