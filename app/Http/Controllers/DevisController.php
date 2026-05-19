<?php

namespace App\Http\Controllers;

use App\Models\Devis;
use App\Models\Piece;
use App\Models\TarifMo;
use App\Models\Dossier;
use App\Models\SuiviDossier;
use App\Http\Requests\StoreDevisRequest;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DevisController extends Controller
{
    // UC05 — Formulaire d'établissement du devis (pré-rempli depuis le diagnostic).
    public function create(Dossier $dossier)
    {
        // Règle métier : Interdire la création si un devis existe déjà pour ce dossier
        if ($dossier->devis) {
            return redirect()->route('dossiers.show', $dossier->id)
                ->with('error', 'Un devis existe déjà pour ce dossier.');
        }

        // Chargement du diagnostic et des pièces et main d'œuvre associées
        $dossier->load('diagnostic.pieces', 'diagnostic.tarifsMo');

        // Liste des ressources actives pour d'éventuels ajustements en cours de devis
        $pieces = Piece::where('actif', true)->orderBy('nom')->get();
        $tarifsMo = TarifMo::where('actif', true)->orderBy('type_intervention')->get();

        return view('devis.create', compact('dossier', 'pieces', 'tarifsMo'));
    }

    // UC05 — Enregistrement du devis en base de données.
    public function store(StoreDevisRequest $request, Dossier $dossier)
    {
        // Règle métier : Bloquer si le devis existe déjà
        if ($dossier->devis) {
            return back()->with('error', 'Un devis existe déjà pour ce dossier.');
        }

        // Règle métier : Pas de devis sans diagnostic technique validé
        if (!$dossier->diagnostic) {
            return back()->with('error', 'Impossible de créer un devis sans diagnostic.');
        }

        $totalTtc = (float) $request->total_ttc;

        // Création du devis avec numéro séquentiel unique
        $devis = Devis::create([
            'dossier_id' => $dossier->id,
            'numero' => 'DEV-' . now()->format('Ymd') . '-' . str_pad(Devis::count() + 1, 4, '0', STR_PAD_LEFT),
            'montant_total' => $totalTtc,
            'frais_mod' => (float) $request->frais_mod ?? 0,
            'remise' => 0, // Pas de remise
            'statut' => 'EN_ATTENTE',
            'date_creation' => now(),
        ]);

        // 1. Sauvegarde des pièces (SNAPSHOT du prix unitaire au moment du devis)
        if ($request->has('pieces')) {
            foreach ($request->pieces as $p) {
                if (empty($p['id'])) {
                    continue;
                }
                $devis->pieces()->attach($p['id'], [
                    'quantite' => $p['quantite'] ?? 1,
                    'prix_unitaire' => $p['prix_unitaire'] ?? 0
                ]);
            }
        }

        // 2. Sauvegarde de la main d'œuvre
        if ($request->has('labors')) {
            foreach ($request->labors as $l) {
                if (empty($l['id'])) {
                    continue;
                }
                $devis->tarifsMo()->attach($l['id'], [
                    'montant' => $l['montant'] ?? 0
                ]);
            }
        }

        // Progression de l'état du dossier
        $dossier->update(['statut' => 'EN_ATTENTE_DEVIS']);

        // Tracing de l'historique SAV (Audit Trail)
        SuiviDossier::create([
            'dossier_id' => $dossier->id,
            'user_id' => auth()->id(),
            'ancien_statut' => 'EN_DIAGNOSTIC',
            'nouveau_statut' => 'EN_ATTENTE_DEVIS',
            'commentaire' => 'Devis établi et transmis au client pour validation.',
        ]);

        // Notification automatique si le client est enregistré
        if ($dossier->client) {
            try {
                $dossier->client->notify(new \App\Notifications\DevisDisponibleNotification($dossier, $devis));
            } catch (\Exception $e) {
                // Ignorer si l'envoi de mail échoue
            }
        }

        return redirect()->route('dossiers.show', $dossier->id)
            ->with('success', 'Devis créé et client notifié.');
    }

    // Afficher les détails d'un devis.
    public function show(Devis $devis)
    {
        $devis->load('dossier.client');

        $statutColors = [
            'EN_ATTENTE' => 'warning',
            'ACCEPTE' => 'success',
            'REFUSE' => 'danger'
        ];

        $badgeColor = $statutColors[$devis->statut] ?? 'secondary';

        return view('devis.show', compact('devis', 'badgeColor'));
    }

    public function accepterDevis(Request $request, Devis $devis)
    {
        // Règle métier : Seul un Agent SAV peut valider un devis
        if (auth()->user()->role !== 'Agent') {
            abort(403, 'Seul un Agent SAV peut valider un devis.');
        }

        // Règle métier : Empêcher de traiter à nouveau un devis déjà décidé
        if ($devis->statut !== 'EN_ATTENTE') {
            return back()->with('error', 'Ce devis a déjà été traité.');
        }

        $dossier = $devis->dossier;

        // Mise à jour du statut du devis
        $devis->update([
            'statut' => 'ACCEPTE',
            'date_decision' => now(),
        ]);

        // Le dossier passe en réparation
        $dossier->update(['statut' => 'EN_REPARATION']);

        // Enregistrement dans l'historique
        SuiviDossier::create([
            'dossier_id' => $dossier->id,
            'user_id' => auth()->id(),
            'ancien_statut' => 'EN_ATTENTE_DEVIS',
            'nouveau_statut' => 'EN_REPARATION',
            'commentaire' => 'Devis validé. Autorisation de réparation accordée et dossier transmis à l\'atelier.',
        ]);

        // Notification au technicien assigné
        if ($dossier->technicien) {
            $dossier->technicien->notify(new \App\Notifications\GenericNotification(
                "Devis accepté - Lancer réparation (#{$dossier->num_dossier})",
                "Le devis a été accepté pour le dossier #{$dossier->num_dossier}. Vous pouvez maintenant commencer la réparation.",
                route('dossiers.show', $dossier->id)
            ));
        }

        return back()->with('success', 'Devis accepté. Dossier passé en réparation.');
    }

    public function refuser(Request $request, Devis $devis)
    {
        // Règle métier : Seul un Agent SAV peut refuser un devis
        if (auth()->user()->role !== 'Agent') {
            abort(403, 'Seul un Agent SAV peut refuser un devis.');
        }

        // Règle métier : Empêcher de traiter à nouveau un devis déjà décidé
        if ($devis->statut !== 'EN_ATTENTE') {
            return back()->with('error', 'Ce devis a déjà été traité.');
        }

        $dossier = $devis->dossier;

        // Enregistrement du refus
        $devis->update([
            'statut' => 'REFUSE',
            'date_decision' => now(),
        ]);

        // Mise à jour du dossier avec le motif de refus
        $dossier->update([
            'statut' => 'DEVIS_REFUSE',
            'commentaire_refus' => $request->commentaire_refus
        ]);

        // Enregistrement dans l'historique
        SuiviDossier::create([
            'dossier_id' => $dossier->id,
            'user_id' => auth()->id(),
            'ancien_statut' => 'EN_ATTENTE_DEVIS',
            'nouveau_statut' => 'DEVIS_REFUSE',
            'commentaire' => 'Devis refusé. Motif : ' . $request->commentaire_refus,
        ]);

        return redirect()->route('dossiers.show', $dossier->id)
            ->with('success', 'Devis refusé. Dossier en attente de restitution.');
    }

    // Générer et télécharger le devis au format PDF.
    public function pdf(Devis $devis)
    {
        $devis->load('dossier.client', 'pieces', 'tarifsMo', 'dossier.appareil');
        $company = \App\Models\ParametreSociete::first();

        $devisData = $this->prepareDevisData($devis);

        // Chargement du template PDF avec les données
        $pdf = Pdf::loadView('devis.pdf', array_merge(compact('devis', 'company'), $devisData));
        return $pdf->stream('devis-' . $devis->numero . '.pdf');
    }

    public function edit(Devis $devis)
    {
        // Règle métier : On ne peut modifier un devis que s'il est encore en attente de décision
        if ($devis->statut !== 'EN_ATTENTE') {
            return redirect()->route('dossiers.show', $devis->dossier_id)
                ->with('error', 'Impossible de modifier un devis déjà traité (accepté ou refusé).');
        }

        $dossier = $devis->dossier;
        $dossier->load('diagnostic.pieces', 'diagnostic.tarifsMo');

        // Liste des ressources actives pour d'éventuels ajustements en cours de devis
        $pieces = Piece::where('actif', true)->orderBy('nom')->get();
        $tarifsMo = TarifMo::where('actif', true)->orderBy('type_intervention')->get();

        // Charger les pièces déjà sélectionnées dans le devis
        $devisPieces = $devis->pieces->pluck('pivot.quantite', 'id')->toArray();
        $devisLabors = $devis->tarifsMo->pluck('id')->toArray();

        return view('devis.edit', compact('devis', 'dossier', 'pieces', 'tarifsMo', 'devisPieces', 'devisLabors'));
    }

    public function update(Request $request, Devis $devis)
    {
        if ($devis->statut !== 'EN_ATTENTE') {
            return back()->with('error', 'Ce devis ne peut plus être modifié.');
        }

        $devis->update([
            'montant_total' => (float) $request->total_ttc,
            'frais_mod' => (float) $request->frais_mod ?? 0,
        ]);

        $devis->pieces()->detach();
        $devis->tarifsMo()->detach();

        // 1. Sauvegarde des pièces
        if ($request->has('pieces')) {
            foreach ($request->pieces as $p) {
                if (empty($p['id'])) {
                    continue;
                }
                $devis->pieces()->attach($p['id'], [
                    'quantite' => $p['quantite'] ?? 1,
                    'prix_unitaire' => $p['prix_unitaire'] ?? 0
                ]);
            }
        }

        // 2. Sauvegarde de la main d'œuvre
        if ($request->has('labors')) {
            foreach ($request->labors as $l) {
                if (empty($l['id'])) {
                    continue;
                }
                $devis->tarifsMo()->attach($l['id'], [
                    'montant' => $l['montant'] ?? 0
                ]);
            }
        }

        // Progression/Maintien de l'état du dossier (déjà EN_ATTENTE_DEVIS)
        $devis->dossier->update(['statut' => 'EN_ATTENTE_DEVIS']);

        SuiviDossier::create([
            'dossier_id' => $devis->dossier_id,
            'user_id' => auth()->id(),
            'ancien_statut' => 'EN_ATTENTE_DEVIS',
            'nouveau_statut' => 'EN_ATTENTE_DEVIS',
            'commentaire' => 'Mise à jour du devis #' . $devis->numero . ' par l\'administration.',
        ]);

        return redirect()->route('dossiers.show', $devis->dossier_id)
            ->with('success', 'Devis mis à jour avec succès.');
    }

    // Prépare et calcule les montants HT, TVA (19%) et TTC pour le PDF.
    private function prepareDevisData(Devis $devis)
    {
        $ttcTotalBrut = $devis->montant_total;

        // Calculs inverses pour retrouver la base HT (TVA à 19%)
        $htTotal = $ttcTotalBrut / 1.19;
        $tvaTotal = $ttcTotalBrut - $htTotal;

        $lignes = [];

        // Pièces détachées
        foreach ($devis->pieces as $piece) {
            $qty = $piece->pivot->quantite;
            $ttc = $piece->pivot->prix_unitaire;
            $ht = $ttc / 1.19;
            $tva = $ttc - $ht;
            $totalLigne = $qty * $ttc;

            $lignes[] = [
                'designation' => $piece->nom,
                'quantite' => $qty,
                'ht' => $ht,
                'tva' => $tva,
                'totalLigne' => $totalLigne
            ];
        }

        // Main d'œuvre
        foreach ($devis->tarifsMo as $mo) {
            $ttc = $mo->pivot->montant;
            $ht = $ttc / 1.19;
            $tva = $ttc - $ht;

            $lignes[] = [
                'designation' => "Main d'œuvre : " . $mo->type_intervention,
                'quantite' => 1,
                'ht' => $ht,
                'tva' => $tva,
                'totalLigne' => $ttc
            ];
        }

        return [
            'htTotal' => $htTotal,
            'tvaTotal' => $tvaTotal,
            'lignes' => $lignes
        ];
    }
}
