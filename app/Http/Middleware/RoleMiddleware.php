<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// Ce middleware filtre les requêtes HTTP selon le rôle de l'utilisateur connecté (Admin, Agent, Technicien, Client).
// Assure la sécurité des routes et prévient les accès non autorisés (Rôles & Permissions).
class RoleMiddleware
{
    // Gère la requête entrante.
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Règle de sécurité : Si l'utilisateur n'est pas authentifié, redirection vers la page de connexion
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        // Règle de sécurité : Si le rôle de l'utilisateur ne figure pas dans les rôles autorisés, renvoi d'une erreur 403 Forbidden
        if (!in_array(auth()->user()->role, $roles)) {
            abort(403, 'Accès non autorisé.');
        }

        return $next($request);
    }
}