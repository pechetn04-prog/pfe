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
            $query->where('imei', 'like', '%' . $request->search . '%')
                  ->orWhere('client_nom', 'like', '%' . $request->search . '%');
        }

        $ventes = $query->paginate(20);

        return view('ventes.index', compact('ventes'));
    }

    public function create()
    {
        return view('ventes.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'imei'               => 'required|string|unique:ventes,imei',
            'modele'             => 'required|string|max:255',
            'client_nom'         => 'required|string|max:255',
            'client_email'       => 'nullable|email',
            'date_vente'         => 'required|date',
            'duree_garantie_mois'=> 'required|integer|min:0',
        ]);

        Vente::create($request->only([
            'imei', 'modele', 'reference_produit', 'client_nom',
            'client_email', 'date_vente', 'duree_garantie_mois',
        ]));

        return redirect()->route('ventes.index')
            ->with('success', 'Vente enregistrée avec succès.');
    }
}
