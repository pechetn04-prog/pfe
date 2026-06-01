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
use App\Models\ParametreSociete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Notifications\PieceManquanteNotification;

/**
 * Réalisation de l'Intervention
 * Ce contrôleur pilote la réalisation technique des interventions et réparations physiques.
 * Il assure la saisie des rapports d'atelier, la gestion de la main d'œuvre, 
 * la consommation de pièces et le décrément des stocks physiques en temps réel.
 */
class InterventionController extends Controller
{
    /**
     * Affiche le formulaire de création  d'une intervention.
     */
    public function create(Dossier $dossier)
    {
        $pieces = Piece::all();
        $tarifsMo = TarifMo::all();

        return view('interventions.create', compact('dossier', 'pieces', 'tarifsMo'));
    }

    /**
     * Enregistre ou met à jour le rapport d'intervention technique d'un technicien.
     * Effectue la gestion des stocks (décrémentation en cas de réparation effective, saut en cas d'attente de pièces).
     * Gère les transitions automatiques d'états (ex: éligibilité échange sous garantie).
     */
    public function store(StoreInterventionRequest $request, Dossier $dossier)
    {
        $nouveauStatut = $request->statut_final;

        // 1. Stockage physique de la photo témoin de l'intervention en atelier
        $photoPath = null;
        if ($request->hasFile('photo_intervention')) {
            $photoPath = $request->file('photo_intervention')->store('interventions', 'public');
        }

        // 2. Création ou mise à jour de la fiche d'intervention technique
        $intervention = Intervention::updateOrCreate(
            ['dossier_id' => $dossier->id],
            [
                'technicien_id' => Auth::id(),
                'compte_rendu' => $request->compte_rendu,
                'photo_intervention' => $photoPath ?? ($dossier->intervention->photo_intervention ?? null),
                'date_fin' => now(),
            ]
        );

        // Réinitialisation des relations pour éviter les doublons lors des mises à jour successives
        $intervention->pieces()->detach();
        $intervention->tarifsMo()->detach();

        // 3. Traitement des pièces détachées déclarées consommées
        if ($request->has('pieces')) {
            foreach ($request->pieces as $p) {
                if (empty($p['id'])) {
                    continue;
                }

                $piece = Piece::findOrFail($p['id']);
                $quantite = $p['quantite'] ?? 1;

                // Si le dossier n'est pas suspendu pour attente de pièces, on vérifie la disponibilité
                if ($nouveauStatut !== 'ATTENTE_PIECE' && $piece->quantite < $quantite) {
                    return back()->with('error', "Stock insuffisant pour la pièce : {$piece->nom}");
                }

                // Liaison de la pièce consommée à l'intervention en enregistrant le tarif unitaire du moment
                $intervention->pieces()->attach($piece->id, [
                    'quantite' => $quantite,
                    'prix_unitaire' => $piece->prix_unitaire
                ]);

                // Si la réparation est finalisée ou déclarée irréparable, on décrémente le stock physique
                if ($nouveauStatut !== 'ATTENTE_PIECE') {
                    $piece->decrement('quantite', $quantite);

                    // Enregistrement du mouvement de stock (Gestion de stock)
                    MouvementStock::create([
                        'piece_id' => $piece->id,
                        'type' => 'SORTIE',
                        'quantite' => $quantite,
                        'motif' => "Sortie pour Intervention pour dossier #{$dossier->num_dossier}",
                        'user_id' => Auth::id(),
                        'reference_id' => $intervention->id,
                        'reference_type' => 'App\Models\Intervention',
                    ]);
                }
            }
        }

        // 4. Traitement des prestations de main d'œuvre effectuées
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

        // 5. Gestion des transitions de statuts et logiques métier complexes
        $ancienStatut = $dossier->statut;

        // Règle métier : Si l'appareil est irréparable mais sous garantie valide,
        // il passe en attente de validation d'un échange (et non directement irréparable)
        if ($nouveauStatut === 'IRREPARABLE') {
            if ($dossier->sous_garantie && !$dossier->garantie_annulee) {
                $nouveauStatut = 'ATTENTE_VALIDATION_REMPLACEMENT';
            }
        }

        // Mise à jour de l'état global du dossier SAV
        $dossier->update([
            'statut' => $nouveauStatut,
            'date_reparation' => $nouveauStatut === 'REPARE' ? now() : null
        ]);

        // 6. Historisation de l'action dans le journal de suivi
        SuiviDossier::create([
            'dossier_id' => $dossier->id,
            'user_id' => Auth::id(),
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => $nouveauStatut,
            'commentaire' => $nouveauStatut === 'REPARE'
                ? 'Intervention terminée. Appareil réparé et prêt.'
                : ($nouveauStatut === 'ATTENTE_PIECE'
                    ? 'Pièce(s) requise(s) non disponible(s) — dossier suspendu.'
                    : 'Verdict technique : appareil non réparable.'),
        ]);

        // 7. Notification instantanée des administrateurs en cas de rupture/manque de pièces
        if ($nouveauStatut === 'ATTENTE_PIECE') {
            $admins = User::where('role', 'Admin')->get();
            foreach ($admins as $admin) {
                $admin->notify(new PieceManquanteNotification($dossier));
            }
        }

        return redirect()->route('technicien.tickets')->with('success', 'Intervention enregistrée avec succès.');
    }

    /**
     * Affiche le rapport d'intervention technique complet d'un dossier.
     */
    public function show(Dossier $dossier)
    {
        $intervention = $dossier->intervention;
        if (!$intervention) {
            return redirect()->route('dossiers.show', $dossier->id)->with('error', 'Aucune intervention trouvée.');
        }
        $intervention->load('pieces', 'tarifsMo', 'technicien', 'dossier.client', 'dossier.appareil');

        // Détermination du verdict logique pour la vue (MVC Pur)
        $status = $dossier->statut;
        $verdict = 'default';

        if ($status === 'REPARE' || $status === 'FACTURE' || $status === 'LIVRE' || $status === 'CLOTURE') {
            $verdict = 'repare';
        } elseif ($status === 'IRREPARABLE' || $status === 'ATTENTE_VALIDATION_REMPLACEMENT') {
            $verdict = 'irreparable';
        } elseif ($status === 'ATTENTE_PIECE') {
            $verdict = 'attente';
        }

        return view('interventions.show', compact('intervention', 'dossier', 'verdict'));
    }

    /**
     * Génère la fiche technique d'intervention au format PDF pour l'archivage ou l'atelier.
     */
    public function pdf(Dossier $dossier)
    {
        $dossier->load('client', 'technicien', 'appareil', 'intervention.pieces', 'intervention.tarifsMo');
        $company = ParametreSociete::first();
        
        $pdf = Pdf::loadView('interventions.intervention-pdf', compact('dossier', 'company'))
            ->setPaper('a4', 'portrait');
            
        return $pdf->stream("intervention-{$dossier->num_dossier}.pdf");
    }

    /**
     * Lancer la réparation (basculer statut EN_REPARATION).
     */
    public function start(Dossier $dossier)
    {
        $ancienStatut = $dossier->statut;
        $dossier->update(['statut' => 'EN_REPARATION']);

        SuiviDossier::create([
            'dossier_id' => $dossier->id,
            'user_id' => Auth::id(),
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => 'EN_REPARATION',
            'commentaire' => 'Lancement de l\'intervention technique approuvé.',
        ]);

        return back()->with('success', 'Statut mis à jour : En Réparation.');
    }
}
