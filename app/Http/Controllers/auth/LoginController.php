<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class LoginController
 * 
 * Ce contrôleur pilote la sécurité des sessions, la connexion et la déconnexion des utilisateurs.
 * Il vérifie le statut du compte et redirige les utilisateurs vers l'espace de travail (dashboard)
 * approprié selon leur rôle (Administrateur, Agent SAV, Technicien, Client).
 */
class LoginController extends Controller
{
    /**
     * Affiche le formulaire de connexion.
     */
    public function getFormLogin()
    {
        return view('auth.login');
    }

    /**
     * Traite la demande de connexion de l'utilisateur.
     * 
     * Cette méthode valide les identifiants saisis, effectue la tentative de connexion,
     * vérifie si le compte est actuellement actif et effectue la redirection sécurisée.
     */
    public function loginUser(Request $request)
    {
        // Validation stricte des données soumises
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Tentative d'authentification de l'utilisateur
        if (Auth::attempt($credentials)) {
            // Regénération de la session pour contrer les attaques par fixation de session
            $request->session()->regenerate();

            $user = Auth::user();
            
            // Règle de sécurité métier : Interdire l'accès immédiat si le compte est désactivé
            if (!$user->actif) {
                Auth::logout();
                return back()->withErrors(['email' => 'Votre compte est désactivé. Veuillez contacter l\'administrateur.']);
            }

            // Redirection intelligente vers la route prévue à l'origine ou le dashboard dédié
            return redirect()->intended($this->getRedirectPath($user->role));
        }

        // Retour avec message d'erreur si la tentative échoue
        return back()->withErrors([
            'email' => 'Les identifiants ne correspondent pas à nos enregistrements.',
        ]);
    }

    /**
     * Déconnexion sécurisée de l'utilisateur et fermeture de session.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    /**
     * Définit le chemin de redirection selon le rôle de l'utilisateur.
     * 
     * Méthode d'aiguillage appelée lors d'une connexion réussie.
     */
    protected function getRedirectPath($role)
    {
        switch ($role) {
            case 'Admin':
                return route('admin.dashboard');
            case 'Agent':
                return route('agent.dashboard');
            case 'Technicien':
                return route('technicien.dashboard');
            case 'Client':
                return route('client.dashboard');
            default:
                return '/';
        }
    }
}

