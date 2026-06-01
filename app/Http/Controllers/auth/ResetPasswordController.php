<?php
 
namespace App\Http\Controllers\Auth;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
 
/**
 * Class ResetPasswordController
 * 
 * Ce contrôleur gère la saisie et l'enregistrement du nouveau mot de passe après validation
 * du jeton (token) de sécurité reçu par email. Il s'appuie sur le broker natif de Laravel.
 */
class ResetPasswordController extends Controller
{
    /**
     * Affiche le formulaire de réinitialisation de mot de passe.
     */
    public function showResetForm(Request $request, $token)
    {
        return view('auth.reset-password')->with(
            ['token' => $token, 'email' => $request->email]
        );
    }
 
    /**
     * Traite la demande de réinitialisation et met à jour le mot de passe de l'utilisateur.
     * 
     * Cette méthode valide la présence du token, vérifie l'e-mail, impose une complexité minimale
     * pour le mot de passe (8 caractères minimum et validation double de confirmation), hache le nouveau
     * mot de passe de manière sécurisée et l'enregistre en base de données avant de rediriger.
     */
    public function reset(Request $request)
    {
        // Validation des règles de complexité et de sécurité du nouveau mot de passe
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ]);
 
        // Exécution de la réinitialisation via le broker de mots de passe
        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // Callback exécuté si le token est valide : Hachage Bcrypt et sauvegarde du modèle User
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
 
                $user->save();
            }
        );
 
        // Redirection conditionnelle selon le retour du broker
        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}

