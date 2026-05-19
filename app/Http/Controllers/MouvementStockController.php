<?php

namespace App\Http\Controllers;

use App\Models\MouvementStock;
use App\Models\Piece;
use App\Http\Requests\StoreMouvementStockRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * MouvementStockController
 * 
 * Ce contrôleur pilote la traçabilité complète de l'inventaire matériel du SAV.
 * Il permet d'afficher l'historique complet des flux de pièces (UC03)
 * et d'enregistrer des ajustements manuels de stock (entrées de réapprovisionnement / sorties).
 */
class MouvementStockController extends Controller
{
    /**
     * Affiche l'historique complet et filtrable des mouvements de stock.
     * 
     * Cette vue affiche l'historique des flux matériels trié par ordre chronologique décroissant.
     * Des filtres de recherche permettent de cibler une pièce spécifique ou un type (ENTRÉE/SORTIE).
     */
    public function index(Request $request)
    {
        // Construction de la requête avec eager loading des relations clés pour éviter le problème N+1
        $query = MouvementStock::with(['piece', 'user', 'reference.dossier'])
            ->latest();

        // Filtrage dynamique selon la pièce détachée sélectionnée
        if ($request->filled('piece_id')) {
            $query->where('piece_id', $request->piece_id);
        }

        // Filtrage dynamique selon le type de flux (entrée/sortie)
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Pagination des flux à 25 entrées par page pour optimiser les performances de rendu
        $mouvements = $query->paginate(25);
        
        // Extraction de toutes les pièces pour le sélecteur d'inventaire
        $pieces = Piece::orderBy('nom')->get(['id', 'nom', 'reference']);

        return view('stock.mouvements', compact('mouvements', 'pieces'));
    }

    /**
     * Redirige vers l'inventaire en cas de tentative d'accès direct au formulaire de création.
     * 
     * Le workflow préconise de cliquer sur le bouton "Mouvement" depuis l'inventaire global pour une meilleure ergonomie.
     */
    public function create()
    {
        return redirect()->route('stock.index')->with('info', 'Utilisez le bouton "Mouvement" sur l\'inventaire pour enregistrer un mouvement.');
    }

    /**
     * Enregistre un mouvement de stock manuel (Ajustement d'inventaire).
     * 
     * Réalise la mise à jour physique de la quantité en magasin de la pièce détachée,
     * puis écrit la trace historique correspondante dans l'audit trail global des stocks.
     */
    public function store(StoreMouvementStockRequest $request)
    {
        // Récupération de la pièce détachée ciblée par l'ajustement
        $piece = Piece::findOrFail($request->piece_id);
        $quantite = $request->quantite;
        $type = strtolower($request->type); // Normalisation en minuscules ('entree' ou 'sortie')

        // Traitement sécurisé de la sortie de stock avec vérification de la disponibilité physique
        if ($type === 'sortie') {
            if ($piece->quantite < $quantite) {
                return back()->with('error', "Stock insuffisant. Stock actuel disponible : {$piece->quantite}");
            }
            // Décrémentation physique de la quantité en magasin
            $piece->decrement('quantite', $quantite);
        } else {
            // Incrémentation physique (entrée / réapprovisionnement)
            $piece->increment('quantite', $quantite);
        }

        // Création de l'enregistrement de traçabilité historique du flux
        MouvementStock::create([
            'piece_id' => $piece->id,
            'user_id'  => Auth::id(),
            'type'     => $type,
            'quantite' => $quantite,
            'motif'    => $request->motif ?? "Mouvement manuel d'ajustement d'inventaire",
        ]);

        return redirect()->route('stock.index')->with('success', "Mouvement de stock enregistré avec succès. Nouveau stock : {$piece->quantite}");
    }
}
