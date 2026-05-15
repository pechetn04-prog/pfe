<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, $token)
    {
        return back()->with('error', 'Fonctionnalité non disponible. Contactez l\'administrateur.');
    }

    public function reset(Request $request)
    {
        return back()->with('error', 'Fonctionnalité non disponible. Contactez l\'administrateur.');
    }
}
