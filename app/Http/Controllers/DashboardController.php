<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Redirige l'utilisateur vers le tableau de bord approprié selon son rôle.
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        switch ($user->role) {
            case 'Admin':
                return redirect()->route('admin.dashboard');
            case 'Agent':
                return redirect()->route('agent.dashboard');
            case 'Technicien':
                return redirect()->route('technicien.dashboard');
            case 'Client':
                return redirect()->route('client.dashboard');
            default:
                return redirect('/');
        }
    }
}
