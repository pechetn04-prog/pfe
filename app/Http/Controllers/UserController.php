<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\StoreUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * UserController
 *
 * Gère l'annuaire des utilisateurs et des clients du SAV.
 * Intègre des barrières de sécurité basées sur le rôle de l'utilisateur connecté.
 */
class UserController extends Controller
{
    /**
     * Affiche la liste des utilisateurs.
     */
    public function index(Request $request)
    {
        $query = User::query()->latest();

        // Filtrage multicritère par chaîne de recherche (Nom, Email, Téléphone)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%");
            });
        }

        // Règle de sécurité : Un agent SAV ne peut voir et gérer que les comptes Clients
        if (auth()->user()->role === 'Agent') {
            $query->where('role', 'Client');
        } elseif ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(10)->withQueryString();

        // Statistiques globales de l'annuaire
        $stats = [
            'total'   => User::count(),
            'actifs'  => User::where('actif', true)->count(),
            'clients' => User::where('role', 'Client')->count(),
            'staff'   => User::whereIn('role', ['Admin', 'Agent', 'Technicien'])->count(),
        ];

        return view('users.index', compact('users', 'stats'));
    }


    /**
     * Affiche le formulaire de création d'un compte.
     */
    public function create()
    {
        if (auth()->user()->role === 'Agent') {
            abort(403, 'Seul l\'administrateur peut créer des comptes manuellement.');
        }

        $specialitesSAV = User::SPECIALITES;
        return view('users.create', compact('specialitesSAV'));
    }

    

    /**
     * Enregistre un nouvel utilisateur.
     */
    public function store(StoreUserRequest $request)
    {
        if (auth()->user()->role === 'Agent') {
            abort(403, 'Seul l\'administrateur peut créer des comptes manuellement.');
        }

        // Règle de sécurité : Si c'est un agent qui crée le compte, on force le rôle 'Client' pour éviter les privilèges frauduleux.
        $role = $request->role;

        // Concaténation des spécialités pour les techniciens
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
     * Affiche le formulaire de modification d'un compte.
     */
    public function edit(User $user)
    {
        // Règle de sécurité : Un Agent ne peut modifier que des comptes de type Client
        if (auth()->user()->role === 'Agent' && $user->role !== 'Client') {
            abort(403, 'Vous n\'êtes autorisé à modifier que les comptes clients.');
        }

        $specialitesSAV = User::SPECIALITES;
        $currentSpecs = explode(', ', $user->specialite ?? '');

        return view('users.edit', compact('user', 'specialitesSAV', 'currentSpecs'));
    }



    /**
     * Met à jour les informations de l'utilisateur.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        // Barrière de sécurité pour l'Agent SAV
        if (auth()->user()->role === 'Agent' && $user->role !== 'Client') {
            abort(403);
        }

        $data = $request->validated();

        // Hachage du mot de passe uniquement s'il a été modifié
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        // Sécurité : Forcer le rôle Client pour éviter les escalades de privilèges par un agent
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
     * Active ou désactive un compte utilisateur.
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
