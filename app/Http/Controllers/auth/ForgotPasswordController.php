<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request)
    {
        // Non utilisé — gestion manuelle des mots de passe par l'administrateur
        return back()->with('error', 'La réinitialisation par email n\'est pas disponible. Contactez l\'administrateur.');
    }
}
