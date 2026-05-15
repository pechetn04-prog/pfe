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

        // Filtre par texte (nom, email, téléphone)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%");
            });
        }

        // Si c'est un agent, on force le filtre sur les clients uniquement
        if (auth()->user()->role === 'Agent') {
            $query->where('role', 'Client');
        } elseif ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(20)->withQueryString();

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
        $allowedRoles = auth()->user()->role === 'Agent' ? 'Client' : 'Admin,Agent,Technicien,Client';

        $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'role'      => 'required|in:' . $allowedRoles,
            'telephone' => 'nullable|string|max:20',
            'password'  => 'required|string|min:8|confirmed',
            'specialites' => 'nullable|array',
        ]);

        $role = auth()->user()->role === 'Agent' ? 'Client' : $request->role;

        $specialite = null;
        if ($role === 'Technicien' && $request->has('specialites')) {
            $specialite = implode(', ', $request->specialites);
        }

        User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'password'   => Hash::make($request->password),
            'role'       => $role,
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
        // Un agent ne peut éditer que des clients
        if (auth()->user()->role === 'Agent' && $user->role !== 'Client') {
            abort(403, 'Vous n\'êtes autorisé à modifier que les comptes clients.');
        }

        return view('users.edit', compact('user'));
    }

    /**
     * Mettre à jour un utilisateur.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        // Un agent ne peut modifier que des clients
        if (auth()->user()->role === 'Agent' && $user->role !== 'Client') {
            abort(403);
        }

        $data = $request->validated();

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Si c'est un agent, on force le rôle client pour éviter l'escalade
        if (auth()->user()->role === 'Agent') {
            $data['role'] = 'Client';
            $data['specialite'] = null;
        } else {
            if ($request->role === 'Technicien' && $request->has('specialites')) {
                $data['specialite'] = implode(', ', $request->specialites);
            } else {
                $data['specialite'] = null;
            }
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
        // Un agent ne peut supprimer que des clients
        if (auth()->user()->role === 'Agent' && $user->role !== 'Client') {
            abort(403);
        }

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
