<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use Illuminate\Http\Request;

/**
 * VenteController
 * Gestion du registre des ventes (pour vérification garantie via IMEI — UC03).
 */
class VenteController extends Controller
{
    public function index(Request $request)
    {
        $query = Vente::query()->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('imei', 'like', "%{$search}%")
                    ->orWhere('client_nom', 'like', "%{$search}%");
            });
        }

        $ventes = $query->paginate(20);

        return view('ventes.index', compact('ventes'));
    }
}
