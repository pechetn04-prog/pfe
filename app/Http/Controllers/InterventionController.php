<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInterventionRequest;
use App\Models\Intervention;
use App\Models\Piece;
use App\Models\SuiviDossier;
use App\Models\TarifMo;
use App\Models\Dossier;
use App\Models\MouvementStock;
use App\Models\User;
use App\Notifications\StockInsuffisantNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * InterventionController
 * 
 * Gère l'exécution technique des réparations.
 * Fonctionnalités :
 * - Saisie du compte-rendu technique.
 * - Gestion des pièces consommées avec mise à jour automatique du stock.
 * - Suivi des temps de main d'œuvre.
 * - Validation finale de la réparation.
 */
class InterventionController extends Controller
{
    public function create(Dossier $dossier)
    {
        $dossier->load('diagnostic.pieces', 'diagnostic.tarifsMo');
        $pieces = Piece::all();
        $tarifsMo = TarifMo::where('actif', true)->get();

        return view('interventions.create', compact('dossier', 'pieces', 'tarifsMo'));
    }

    public function store(Request $request, Dossier $dossier)
    {
        $request->validate([
            'compte_rendu' => 'required|string',
            'statut_final' => 'required|in:REPARE,IRREPARABLE,ATTENTE_PIECE',
            'photo_intervention' => 'nullable|image|max:2048',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo_intervention')) {
            $photoPath = $request->file('photo_intervention')->store('interventions', 'public');
        }

        $intervention = Intervention::create([
            'dossier_id' => $dossier->id,
            'technicien_id' => Auth::id(),
            'compte_rendu' => $request->compte_rendu,
            'photo_intervention' => $photoPath,
            'date_fin' => now(),
        ]);

        // Gestion des pièces consommées
        if ($request->has('pieces')) {
            foreach ($request->pieces as $p) {
                if (empty($p['id'])) continue;
                
                $piece = Piece::findOrFail($p['id']);
                $quantite = $p['quantite'] ?? 1;

                if ($piece->quantite < $quantite) {
                    return back()->with('error', "Stock insuffisant pour la pièce : {$piece->nom}");
                }

                $intervention->pieces()->attach($piece->id, [
                    'quantite' => $quantite,
                    'prix_unitaire' => $piece->prix_unitaire
                ]);

                // Sortie de stock
                $piece->decrement('quantite', $quantite);
                
                MouvementStock::create([
                    'piece_id' => $piece->id,
                    'type' => 'SORTIE',
                    'quantite' => $quantite,
                    'motif' => "Intervention Dossier #{$dossier->num_dossier}",
                    'user_id' => Auth::id(),
                ]);
            }
        }

        // Gestion de la main d'œuvre (Tarifs MO)
        if ($request->has('labors')) {
            foreach ($request->labors as $laborId) {
                $tarif = TarifMo::find($laborId);
                if ($tarif) {
                    $intervention->tarifsMo()->attach($tarif->id, [
                        'montant' => $tarif->montant
                    ]);
                }
            }
        }

        $ancienStatut = $dossier->statut;
        $nouveauStatut = $request->statut_final;

        // Si déclaré irréparable pendant l'intervention
        if ($nouveauStatut === 'IRREPARABLE') {
            // Vérifier si la garantie est toujours valide (pas de garantie annulée par le diagnostic)
            if ($dossier->sous_garantie && !$dossier->garantie_annulee) {
                $nouveauStatut = 'ATTENTE_VALIDATION_REMPLACEMENT';
            }
        }

        $dossier->update([
            'statut' => $nouveauStatut, 
            'date_reparation' => $nouveauStatut === 'REPARE' ? now() : null
        ]);

        SuiviDossier::create([
            'dossier_id' => $dossier->id,
            'user_id' => Auth::id(),
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => $nouveauStatut,
            'commentaire' => $nouveauStatut === 'REPARE' ? 'Réparation effectuée avec succès.' : ($nouveauStatut === 'ATTENTE_PIECE' ? 'Mis en attente de pièce.' : 'Appareil déclaré irréparable.'),
        ]);

        return redirect()->route('technicien.tickets')->with('success', 'Intervention enregistrée avec succès.');
    }

    public function show(Intervention $intervention)
    {
        return view('interventions.show', compact('intervention'));
    }

    public function pdf(Intervention $intervention)
    {
        $intervention->load('dossier', 'pieces', 'technicien');
        $pdf = Pdf::loadView('interventions.pdf', compact('intervention'));
        return $pdf->stream("intervention-{$intervention->id}.pdf");
    }
}
