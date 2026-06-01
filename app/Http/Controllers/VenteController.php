<?php

namespace App\Http\Controllers;

use App\Models\Vente;
use Illuminate\Http\Request;
use App\Http\Requests\ImportVenteRequest;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\VentesImport;
use App\Exports\VentesTemplateExport;

/**
 * VenteController
 * 
 * Ce contrôleur pilote le registre des ventes d'appareils et des remplacements validés.
 * Il joue un rôle crucial dans la vérification de l'éligibilité à la garantie commerciale (UC03)
 * via le numéro IMEI de l'appareil lors de la réception SAV.
 */
class VenteController extends Controller
{
    /**
     * Affiche le registre général des ventes et des échanges d'appareils.
     * 
     * Propose un filtrage multicritère (recherche par IMEI ou nom de l'acheteur initial).
     * Les données sont enrichies dynamiquement pour faciliter le rendu dans la vue Blade.
     */
    public function index(Request $request)
    {
        $query = Vente::query()->latest();

        // Application du filtre de recherche (IMEI ou nom du client)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('imei', 'like', "%{$search}%")
                    ->orWhere('client_nom', 'like', "%{$search}%");
            });
        }

        // Pagination à 20 enregistrements par page
        $ventes = $query->paginate(20);

        // Transformation de la collection pour intégrer les calculs métier
        $ventes->getCollection()->transform(function ($vente) {
            // Calcul de la date d'expiration de la garantie commerciale (UC03) via le contrôleur
            $finGarantie = self::dateExpiration($vente);
            $vente->fin_garantie_formatted = $finGarantie ? $finGarantie->format('d/m/Y') : '—';
            
            // Attribution dynamique de la classe CSS du badge selon le type d'enregistrement
            $vente->badge_class = $vente->type === 'REMPLACEMENT' ? 'bg-soft-info text-info' : 'bg-light text-dark';
            return $vente;
        });

        return view('ventes.index', compact('ventes'));
    }

    /**
     * Importation en masse de ventes depuis un fichier Excel ou CSV.
     * 
     * Utilise le FormRequest de validation ImportVenteRequest pour sécuriser l'envoi du fichier,
     * puis délègue le traitement à l'importateur VentesImport.
     */
    public function import(ImportVenteRequest $request)
    {
        try {
            // Exécution de l'importation via la librairie Excel
            Excel::import(new VentesImport, $request->file('excel_file'));

            return redirect()->route('ventes.index')
                ->with('success', "Importation Excel réussie ! Les ventes ont été intégrées et mises à jour.");
        } catch (\Exception $e) {
            return redirect()->route('ventes.index')
                ->with('error', "Erreur lors de l'importation Excel : " . $e->getMessage());
        }
    }

    /**
     * Télécharge le modèle Excel vierge (gabarit) pour l'importation en masse.
     * 
     * Permet aux administrateurs de récupérer un fichier structuré pré-rempli d'exemples.
     */
    public function downloadTemplate()
    {
        return Excel::download(new VentesTemplateExport, 'modele_import_ventes.xlsx');
    }

    /**
     * Vérifie si une vente est encore sous garantie.
     */
    public static function estSousGarantie(Vente $vente): bool
    {
        if (!$vente->date_vente || !$vente->duree_garantie_mois) {
            return false;
        }
        
        $expiration = \Carbon\Carbon::parse($vente->date_vente)->addMonths($vente->duree_garantie_mois);
        return now()->lt($expiration);
    }

    /**
     * Calcule la date d'expiration de la garantie.
     */
    public static function dateExpiration(Vente $vente): ?\Carbon\Carbon
    {
        if (!$vente->date_vente || !$vente->duree_garantie_mois) {
            return null;
        }
        
        return \Carbon\Carbon::parse($vente->date_vente)->addMonths($vente->duree_garantie_mois);
    }
}
