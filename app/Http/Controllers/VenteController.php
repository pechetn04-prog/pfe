<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\VentesImport;

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

    // Importation en masse de ventes depuis un fichier Excel (.xlsx, .xls, .csv)
    public function import(Request $request)
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv,txt|max:4096'
        ], [
            'excel_file.required' => 'Veuillez sélectionner un fichier Excel.',
            'excel_file.mimes'    => 'Le fichier doit être au format Excel (.xlsx, .xls) ou texte/CSV (.csv, .txt).',
            'excel_file.max'      => 'La taille du fichier ne doit pas dépasser 4 Mo.',
        ]);

        try {
            Excel::import(new VentesImport, $request->file('excel_file'));

            return redirect()->route('ventes.index')
                ->with('success', "Importation Excel réussie ! Les ventes ont été intégrées et mises à jour.");
        } catch (\Exception $e) {
            return redirect()->route('ventes.index')
                ->with('error', "Erreur lors de l'importation Excel : " . $e->getMessage());
        }
    }
}
