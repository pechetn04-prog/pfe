<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * Traite la demande de connexion.
     */
    public function loginUser(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Redirection selon le rôle
            $user = Auth::user();
            
            if (!$user->actif) {
                Auth::logout();
                return back()->withErrors(['email' => 'Votre compte est désactivé.']);
            }

            return redirect()->intended($this->getRedirectPath($user->role));
        }

        return back()->withErrors([
            'email' => 'Les identifiants ne correspondent pas à nos enregistrements.',
        ]);
    }

    /**
     * Déconnexion.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    /**
     * Définit le chemin de redirection selon le rôle.
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
