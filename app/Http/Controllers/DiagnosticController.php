<?php

namespace App\Http\Controllers;

use App\Models\Diagnostic;
use App\Models\Piece;
use App\Models\SuiviDossier;
use App\Models\TarifMo;
use App\Models\Dossier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiagnosticController extends Controller
{
    /**
     * Affiche le formulaire de diagnostic et change le statut en 'EN_DIAGNOSTIC'.
     */
    public function create(Dossier $dossier)
    {
        if ($dossier->technicien_id != Auth::id()) {
            abort(403, 'Ce dossier ne vous est pas assigné.');
        }

        // UC04 - Point 1 : L'ouverture du formulaire fait passer automatiquement le statut à 'En diagnostic'
        if ($dossier->statut === 'AFFECTE') {
            $dossier->update(['statut' => 'EN_DIAGNOSTIC']);
            
            SuiviDossier::create([
                'dossier_id' => $dossier->id,
                'user_id' => Auth::id(),
                'ancien_statut' => 'AFFECTE',
                'nouveau_statut' => 'EN_DIAGNOSTIC',
                'commentaire' => 'Ouverture du formulaire de diagnostic par le technicien.',
            ]);
        }

        $pieces = Piece::all();
        $tarifsMo = TarifMo::where('actif', true)->get();

        return view('diagnostics.create', compact('dossier', 'pieces', 'tarifsMo'));
    }

    /**
     * Enregistre le diagnostic avec la logique de décision UC04.
     */
    public function store(Request $request, Dossier $dossier)
    {
        $diagnostic = Diagnostic::updateOrCreate(
            ['dossier_id' => $dossier->id],
            [
                'technicien_id' => Auth::id(),
                'constat' => $request->constat_technique,
                'recommandation' => $request->recommandation,
                'motif_exclusion' => $request->has('exclusion_garantie') ? ($request->motif_exclusion ?? 'Usage non conforme') : null,
                'exclusion_commentaire' => $request->exclusion_commentaire,
                'date_diagnostic' => now(),
            ]
        );

        // Nettoyage des anciennes liaisons pour éviter les doublons si c'est une mise à jour
        $diagnostic->pieces()->detach();
        $diagnostic->tarifsMo()->detach();

        // Liaison des pièces et prestations
        if ($request->has('pieces')) {
            foreach ($request->pieces as $p) {
                if (empty($p['id'])) continue;
                $piece = Piece::find($p['id']);
                if ($piece) {
                    $diagnostic->pieces()->attach($p['id'], [
                        'quantite' => $p['quantite'] ?? 1,
                        'prix_unitaire' => $piece->prix_unitaire ?? 0
                    ]);
                }
            }
        }

        // Liaison des prestations (main d'œuvre)
        if ($request->has('labors')) {
            foreach ($request->labors as $lId) {
                $tarifMo = TarifMo::find($lId);
                if ($tarifMo) {
                    $diagnostic->tarifsMo()->attach($lId, [
                        'montant' => $tarifMo->montant ?? 0
                    ]);
                }
            }
        }

        // UC04 - Point 7 : Logique de décision finale
        $isReparable = $request->has('is_reparable');
        $exclusionGarantie = $request->has('exclusion_garantie');
        $isGarantieValide = $dossier->sous_garantie && !$exclusionGarantie;
        
        $nouveauStatut = 'EN_DIAGNOSTIC'; // Par défaut

        if ($isReparable) {
            if ($isGarantieValide) {
                $nouveauStatut = 'EN_REPARATION'; // Réparable + garantie valide
            } else {
                $nouveauStatut = 'EN_ATTENTE_DEVIS'; // Réparable + hors garantie (ou exclusion)
            }
        } else {
            if ($isGarantieValide) {
                $nouveauStatut = 'ATTENTE_VALIDATION_REMPLACEMENT'; // Irréparable + garantie valide
            } else {
                $nouveauStatut = 'IRREPARABLE'; // Irréparable + hors garantie
            }
        }

        // Mise à jour du dossier avec exclusion si nécessaire
        $dossier->update([
            'statut' => $nouveauStatut, 
            'date_diagnostic' => now(),
            'garantie_annulee' => $exclusionGarantie ? true : $dossier->garantie_annulee
        ]);

        SuiviDossier::create([
            'dossier_id' => $dossier->id,
            'user_id' => Auth::id(),
            'ancien_statut' => 'EN_DIAGNOSTIC',
            'nouveau_statut' => $nouveauStatut,
            'commentaire' => $exclusionGarantie ? 'Diagnostic terminé avec EXCLUSION de garantie.' : 'Diagnostic terminé.',
        ]);

        return redirect()->route('technicien.dashboard')->with('success', 'Diagnostic enregistré. Statut actuel : ' . $nouveauStatut);
    }

    /**
     * Afficher le résultat d'un diagnostic.
     */
    public function show(Dossier $dossier)
    {
        $dossier->load('diagnostic.pieces', 'diagnostic.tarifsMo', 'client', 'technicien');

        if (!$dossier->diagnostic) {
            return redirect()->route('diagnostics.create', $dossier->id)
                ->with('error', 'Aucun diagnostic trouvé pour ce dossier.');
        }

        return view('diagnostics.show', compact('dossier'));
    }
}
