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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

// Ce contrôleur pilote la réalisation technique des interventions et réparations physiques (UC09).
// Assure la saisie des rapports d'atelier, la gestion de la main d'œuvre, la consommation de pièces et le décrément des stocks.
class InterventionController extends Controller
{
    // Affiche le formulaire de saisie de l'intervention technique.
    public function create(Dossier $dossier)
    {
        $dossier->load('diagnostic.pieces', 'diagnostic.tarifsMo');
        $pieces = Piece::all();
        $tarifsMo = TarifMo::where('actif', true)->get();

        return view('interventions.create', compact('dossier', 'pieces', 'tarifsMo'));
    }

    // Enregistre l'intervention, applique les mouvements de stocks et met à jour le statut du dossier.
    public function store(Request $request, Dossier $dossier)
    {
        $request->validate([
            'compte_rendu'       => 'required|string',
            'statut_final'       => 'required|in:REPARE,IRREPARABLE,ATTENTE_PIECE',
            'photo_intervention' => 'nullable|image|max:2048',
        ]);
        
        $nouveauStatut = $request->statut_final;

        // Sauvegarde de la photo de l'appareil après réparation (preuve visuelle de l'état)
        $photoPath = null;
        if ($request->hasFile('photo_intervention')) {
            $photoPath = $request->file('photo_intervention')->store('interventions', 'public');
        }

        $intervention = Intervention::create([
            'dossier_id'         => $dossier->id,
            'technicien_id'      => Auth::id(),
            'compte_rendu'       => $request->compte_rendu,
            'photo_intervention' => $photoPath,
            'date_fin'           => now(),
        ]);

        // Gestion de la consommation des pièces détachées (uniquement si ce n'est pas en attente de pièce)
        if ($nouveauStatut !== 'ATTENTE_PIECE' && $request->has('pieces')) {
            foreach ($request->pieces as $p) {
                if (empty($p['id'])) {
                    continue;
                }
                
                $piece = Piece::findOrFail($p['id']);
                $quantite = $p['quantite'] ?? 1;

                // Validation physique des stocks en magasin
                if ($piece->quantite < $quantite) {
                    return back()->with('error', "Stock insuffisant pour la pièce : {$piece->nom}");
                }

                $intervention->pieces()->attach($piece->id, [
                    'quantite'      => $quantite,
                    'prix_unitaire' => $piece->prix_unitaire
                ]);

                // Décrémentation physique du stock (UC14)
                $piece->decrement('quantite', $quantite);
                
                // Tracing historique du mouvement de stock
                MouvementStock::create([
                    'piece_id' => $piece->id,
                    'type'     => 'SORTIE',
                    'quantite' => $quantite,
                    'motif'    => "Intervention Dossier #{$dossier->num_dossier}",
                    'user_id'  => Auth::id(),
                ]);
            }
        }

        // Association de la main d'œuvre effectuée
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

        // Logique de décision si déclaré irréparable en cours d'intervention (UC04)
        if ($nouveauStatut === 'IRREPARABLE') {
            // Si l'appareil est éligible sous garantie et que la garantie n'a pas été déchue par l'oxydation/casse
            if ($dossier->sous_garantie && !$dossier->garantie_annulee) {
                $nouveauStatut = 'ATTENTE_VALIDATION_REMPLACEMENT';
            }
        }

        $dossier->update([
            'statut'          => $nouveauStatut, 
            'date_reparation' => $nouveauStatut === 'REPARE' ? now() : null
        ]);

        // Audit Trail du dossier SAV
        SuiviDossier::create([
            'dossier_id'     => $dossier->id,
            'user_id'        => Auth::id(),
            'ancien_statut'  => $ancienStatut,
            'nouveau_statut' => $nouveauStatut,
            'commentaire'    => $nouveauStatut === 'REPARE' 
                ? 'Intervention terminée. Appareil réparé et prêt.' 
                : ($nouveauStatut === 'ATTENTE_PIECE' 
                    ? 'Pièce(s) requise(s) non disponible(s) — dossier suspendu.' 
                    : 'Verdict technique : appareil non réparable.'),
        ]);

        // Déclencher une alerte/notification aux administrateurs si des pièces manquent (UC14)
        if ($nouveauStatut === 'ATTENTE_PIECE') {
            $admins = User::where('role', 'Admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new \App\Notifications\PieceManquanteNotification($dossier));
            }
        }

        return redirect()->route('technicien.tickets')->with('success', 'Intervention enregistrée avec succès.');
    }

    // Affiche le rapport technique d'intervention d'un dossier.
    public function show(Dossier $dossier)
    {
        $intervention = $dossier->intervention;
        if (!$intervention) {
            return redirect()->route('dossiers.show', $dossier->id)->with('error', 'Aucune intervention trouvée.');
        }
        $intervention->load('pieces', 'technicien', 'dossier.client', 'dossier.appareil');
        return view('interventions.show', compact('intervention', 'dossier'));
    }

    // Génère le compte-rendu d'intervention au format PDF pour l'atelier.
    public function pdf(Intervention $intervention)
    {
        $intervention->load('dossier', 'pieces', 'technicien');
        $pdf = Pdf::loadView('interventions.intervention-pdf', compact('intervention'));
        return $pdf->stream("intervention-{$intervention->id}.pdf");
    }
}
