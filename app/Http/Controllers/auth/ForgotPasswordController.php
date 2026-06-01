<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

/**
 * Class ForgotPasswordController
 * 
 * Ce contrôleur gère l'envoi des liens de réinitialisation de mot de passe en cas d'oubli.
 * Il sollicite le courtier (broker) de mots de passe natif de Laravel pour générer un jeton (token)
 * de sécurité unique et l'envoyer par e-mail à l'utilisateur.
 */
class ForgotPasswordController extends Controller
{
    /**
     * Affiche le formulaire de saisie de l'e-mail pour demander la réinitialisation.
     */
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Valide la demande et envoie le lien de réinitialisation par email.
     * 
     * Cette méthode valide que l'adresse e-mail saisie est valide et présente dans le système,
     * puis envoie un lien temporaire contenant un jeton de réinitialisation sécurisé.
     */
    public function sendResetLinkEmail(Request $request)
    {
        // Validation de la présence et du format de l'adresse e-mail
        $request->validate(['email' => 'required|email']);
 
        // Sollicitation du broker (courtier) de mots de passe par défaut de Laravel
        $status = Password::broker()->sendResetLink(
            $request->only('email')
        );
 
        // Redirection avec un message de succès ou d'erreur en fonction du retour du broker
        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }
}

