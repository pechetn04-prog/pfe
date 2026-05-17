<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use Illuminate\Http\Request;

// Ce contrôleur gère le registre des ventes d'appareils et des remplacements validés.
// Il joue un rôle crucial dans le cas d'utilisation d'éligibilité à la garantie commerciale (UC03) via l'IMEI.
class VenteController extends Controller
{
    // Affiche le registre général des ventes et des échanges d'appareils avec filtre de recherche.
    public function index(Request $request)
    {
        $query = Vente::query()->latest();

        // Filtrage multicritère (par IMEI ou par nom de l'acheteur initial)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('imei', 'like', "%{$search}%")
                    ->orWhere('client_nom', 'like', "%{$search}%");
            });
        }

        $ventes = $query->paginate(20);

        // Enrichissement de la collection pour alléger le traitement côté vue Blade (MVC)
        $ventes->getCollection()->transform(function ($vente) {
            // Calcul de la date exacte d'expiration de la garantie commerciale (UC03)
            $finGarantie = $vente->date_vente->copy()->addMonths($vente->duree_garantie_mois);
            $vente->fin_garantie_formatted = $finGarantie->format('d/m/Y');
            
            // Association visuelle selon la nature de l'enregistrement (Vente initiale ou Échange de SAV)
            $vente->badge_class = $vente->type === 'REMPLACEMENT' ? 'bg-soft-info text-info' : 'bg-light text-dark';
            return $vente;
        });

        return view('ventes.index', compact('ventes'));
    }
}
