<?php

namespace App\Http\Controllers;

use App\Models\Devis;
use App\Models\Piece;
use App\Models\TarifMo;
use App\Models\Dossier;
use App\Models\SuiviDossier;
use App\Models\ParametreSociete;
use App\Notifications\DevisDisponibleNotification;
use App\Notifications\DevisModifieNotification;
use App\Notifications\GenericNotification;
use App\Http\Requests\StoreDevisRequest;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Contrôleur pour gérer l'établissement, la validation et la modification des devis clients.
 */
class DevisController extends Controller
{
    /**
     * Affiche le formulaire de création du devis (rempli automatiquement avec le diagnostic).
     */
    public function create(Dossier $dossier)
    {
        // Règle métier : Interdire la création si un devis existe déjà pour ce dossier
        if ($dossier->devis) {
            return redirect()->route('dossiers.show', $dossier->id)
                ->with('error', 'Un devis existe déjà pour ce dossier.');
        }

        // Chargement du diagnostic technique et de ses relations (pièces recommandées et prestations main d'œuvre)
        $dossier->load('diagnostic.pieces', 'diagnostic.tarifsMo');

        // Récupération des ressources actives (pièces et tarifs MO) pour permettre d'éventuels ajustements
        $pieces = Piece::orderBy('nom')->get();
        $tarifsMo = TarifMo::orderBy('type_intervention')->get();

        return view('devis.create', compact('dossier', 'pieces', 'tarifsMo'));
    }

    /**
     * Enregistre le devis en base de données, bloque les tarifs des pièces/prestations et envoie un email au client.
     */
    public function store(StoreDevisRequest $request, Dossier $dossier)
    {
        // Règle métier : Bloquer si le devis existe déjà pour éviter les doublons
        if ($dossier->devis) {
            return back()->with('error', 'Un devis existe déjà pour ce dossier.');
        }

        // Règle métier : Pas de devis commercial sans diagnostic technique validé au préalable
        if (!$dossier->diagnostic) {
            return back()->with('error', 'Impossible de créer un devis sans diagnostic.');
        }

        $totalTtc = (float) $request->total_ttc;

        // Création du devis avec génération d'un numéro séquentiel unique (format: DEV-AAAAMMJJ-XXXX)
        $devis = Devis::create([
            'dossier_id' => $dossier->id,
            'numero' => 'DEV-' . now()->format('Ymd') . '-' . str_pad(Devis::count() + 1, 4, '0', STR_PAD_LEFT),
            'montant_total' => $totalTtc,
            'frais_mod' => (float) $request->frais_mod ?? 0,
            'remise' => 0, // Pas de remise commerciale par défaut
            'statut' => 'EN_ATTENTE',
            'date_creation' => now(),
        ]);

        // 1. Sauvegarde des pièces détachées associées au devis
        // Enregistre un SNAPSHOT du prix unitaire de la pièce au moment exact de la création du devis
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

        // 2. Sauvegarde des prestations de main d'œuvre associées au devis
        // Enregistre également un SNAPSHOT des tarifs horaires ou forfaitaires appliqués
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

        // Progression de l'état du dossier SAV
        $dossier->update(['statut' => 'EN_ATTENTE_DEVIS']);

        // Tracing de l'historique SAV (Audit Trail) pour le suivi opérationnel
        SuiviDossier::create([
            'dossier_id' => $dossier->id,
            'user_id' => auth()->id(),
            'ancien_statut' => 'EN_DIAGNOSTIC',
            'nouveau_statut' => 'EN_ATTENTE_DEVIS',
            'commentaire' => 'Devis établi et transmis au client pour validation.',
        ]);

        // Notification automatique du client par e-mail (et en base de données pour la cloche d'alertes)
        if ($dossier->client) {
            try {
                $dossier->client->notify(new DevisDisponibleNotification($dossier, $devis));
            } catch (\Exception $e) {
                // Silencieusement ignoré si les services de messagerie SMTP externe échouent temporairement
            }
        }

        return redirect()->route('dossiers.show', $dossier->id)
            ->with('success', 'Devis créé et client notifié.');
    }

    /**
     * Affiche les détails du devis avec un code couleur selon son statut (En attente, Accepté, Refusé).
     */
    public function show(Devis $devis)
    {
        // Chargement des relations du dossier et du profil client associé
        $devis->load('dossier.client');

        // Association des couleurs de badges Bootstrap/Tailwind selon le statut du devis
        $statutColors = [
            'EN_ATTENTE' => 'warning',
            'ACCEPTE' => 'success',
            'REFUSE' => 'danger'
        ];

        $badgeColor = $statutColors[$devis->statut] ?? 'secondary';

        return view('devis.show', compact('devis', 'badgeColor'));
    }

    /**
     * Accepte le devis : passe le dossier en statut "EN_REPARATION" et alerte le technicien pour commencer.
     */
    public function accepterDevis(Request $request, Devis $devis)
    {
        // Règle métier : Seul un Agent SAV ou un Administrateur est habilité à valider officiellement une décision de devis
        if ($request->user()?->role !== 'Agent') {
            abort(403, 'Seul un Agent SAV peut valider un devis.');
        }

        // Règle métier : Empêcher de traiter à nouveau un devis qui a déjà fait l'objet d'une décision
        if ($devis->statut !== 'EN_ATTENTE') {
            return back()->with('error', 'Ce devis a déjà été traité.');
        }

        $dossier = $devis->dossier;

        // Mise à jour du statut et de la date de décision du devis
        $devis->update([
            'statut' => 'ACCEPTE',
            'date_decision' => now(),
        ]);

        // Progression réglementaire du dossier vers la file d'attente ou l'atelier de réparation
        $dossier->update(['statut' => 'EN_REPARATION']);

        // Enregistrement de l'action dans l'historique général (Audit Trail)
        SuiviDossier::create([
            'dossier_id' => $dossier->id,
            'user_id' => auth()->id(),
            'ancien_statut' => 'EN_ATTENTE_DEVIS',
            'nouveau_statut' => 'EN_REPARATION',
            'commentaire' => 'Devis validé. Autorisation de réparation accordée et dossier transmis à l\'atelier.',
        ]);

        // Envoi d'une notification push ou email interne au technicien assigné pour qu'il commence le travail
        if ($dossier->technicien) {
            $dossier->technicien->notify(new GenericNotification(
                "Devis accepté - Lancer réparation (#{$dossier->num_dossier})",
                "Le devis a été accepté pour le dossier #{$dossier->num_dossier}. Vous pouvez maintenant commencer la réparation.",
                route('dossiers.show', $dossier->id)
            ));
        }

        return back()->with('success', 'Devis accepté. Dossier passé en réparation.');
    }

    /**
     * Refuse le devis : enregistre le motif et prépare l'appareil pour être rendu au client sans réparation.
     */
    public function refuser(Request $request, Devis $devis)
    {
        // Règle métier : Seul un Agent SAV ou Admin peut statuer sur un refus
        if ($request->user()?->role !== 'Agent') {
            abort(403, 'Seul un Agent SAV peut refuser un devis.');
        }

        // Règle métier : Interdire le traitement multiple d'un même devis
        if ($devis->statut !== 'EN_ATTENTE') {
            return back()->with('error', 'Ce devis a déjà été traité.');
        }

        $dossier = $devis->dossier;

        // Enregistrement du refus sur la fiche de devis
        $devis->update([
            'statut' => 'REFUSE',
            'date_decision' => now(),
        ]);

        // Mise à jour du dossier avec transition vers l'état 'DEVIS_REFUSE' et stockage du motif de refus
        $dossier->update([
            'statut' => 'DEVIS_REFUSE',
            'commentaire_refus' => $request->commentaire_refus
        ]);

        // Enregistrement de l'action dans l'historique SAV (Audit Trail)
        SuiviDossier::create([
            'dossier_id' => $dossier->id,
            'user_id' => auth()->id(),
            'ancien_statut' => 'EN_ATTENTE_DEVIS',
            'nouveau_statut' => 'DEVIS_REFUSE',
            'commentaire' => 'Devis refusé. Motif : ' . $request->commentaire_refus,
        ]);

        return redirect()->route('devis.show', $devis->id)
            ->with('success', 'Devis refusé. Dossier en attente de restitution.');
    }


    /**
     * Affiche le formulaire pour modifier le devis (seulement si le client n'a pas encore répondu).
     */
    public function edit(Devis $devis)
    {
        // Règle métier : On ne peut modifier un devis que s'il est encore en attente de décision (statut EN_ATTENTE)
        if ($devis->statut !== 'EN_ATTENTE') {
            return redirect()->route('dossiers.show', $devis->dossier_id)
                ->with('error', 'Impossible de modifier un devis déjà traité (accepté ou refusé).');
        }

        $dossier = $devis->dossier;
        $dossier->load('diagnostic.pieces', 'diagnostic.tarifsMo');

        // Chargement des pièces détachées et interventions de main d'œuvre actives pour peupler les sélecteurs
        $pieces = Piece::orderBy('nom')->get();
        $tarifsMo = TarifMo::orderBy('type_intervention')->get();

        // Récupération des pièces et des prestations déjà incluses dans la version actuelle du devis
        $devisPieces = $devis->pieces->pluck('pivot.quantite', 'id')->toArray();
        $devisLabors = $devis->tarifsMo->pluck('id')->toArray();

        return view('devis.edit', compact('devis', 'dossier', 'pieces', 'tarifsMo', 'devisPieces', 'devisLabors'));
    }

    /**
     * Enregistre les modifications du devis en mettant à jour les tarifs et envoie les détails au client.
     */
    public function update(Request $request, Devis $devis)
    {
        // Règle métier : Protection contre l'édition de devis archivés ou décidés
        if ($devis->statut !== 'EN_ATTENTE') {
            return back()->with('error', 'Ce devis ne peut plus être modifié.');
        }

        // Mise à jour des montants bruts du devis principal
        $devis->update([
            'montant_total' => (float) $request->total_ttc,
            'frais_mod' => (float) $request->frais_mod ?? 0,
        ]);

        // Nettoyage complet des relations existantes pour éviter les doublons orphelins avant ré-attachement
        $devis->pieces()->detach();
        $devis->tarifsMo()->detach();

        // 1. Sauvegarde de la nouvelle sélection de pièces détachées (Snapshot du prix unitaire)
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

        // 2. Sauvegarde de la nouvelle sélection de prestations de main d'œuvre (Snapshot du montant)
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

        // Maintien ou forçage de l'état opérationnel du dossier en attente de validation client
        $devis->dossier->update(['statut' => 'EN_ATTENTE_DEVIS']);

        // Enregistrement de la modification dans l'historique général (Audit Trail)
        SuiviDossier::create([
            'dossier_id' => $devis->dossier_id,
            'user_id' => auth()->id(),
            'ancien_statut' => 'EN_ATTENTE_DEVIS',
            'nouveau_statut' => 'EN_ATTENTE_DEVIS',
            'commentaire' => 'Mise à jour du devis #' . $devis->numero . ' par l\'administration.',
        ]);

        // Notification automatique du client par e-mail et cloche d'alertes locale (nouvelles valeurs ajustées)
        if ($devis->dossier->client) {
            try {
                $devis->dossier->client->notify(new DevisModifieNotification($devis->dossier, $devis));
            } catch (\Exception $e) {
                // Silencieusement ignoré si échec du protocole SMTP
            }
        }

        return redirect()->route('dossiers.show', $devis->dossier_id)
            ->with('success', 'Devis mis à jour avec succès et client notifié.');
    }

    /**
     * Calcule automatiquement le montant Hors Taxe (HT) et le montant de la TVA (19%) pour le PDF.
     */
    private function prepareDevisData(Devis $devis)
    {
        $ttcTotalBrut = $devis->montant_total;

        // Formules mathématiques inverses pour retrouver la base HT (TVA appliquée à 19%)
        $htTotal = $ttcTotalBrut / 1.19;
        $tvaTotal = $ttcTotalBrut - $htTotal;

        $lignes = [];

        // Traitement des pièces détachées et répartition des taxes correspondantes
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

        // Traitement des interventions/tarifs Main d'œuvre et répartition des taxes correspondantes
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

    /**
     * Génère et télécharge le document PDF officiel du devis.
     */
    public function pdf(Devis $devis)
    {
        // Chargement eager des relations nécessaires à l'édition de la facture PDF
        $devis->load('dossier.client', 'pieces', 'tarifsMo', 'dossier.appareil');
        $company = ParametreSociete::first();

        // Préparation des calculs financiers (conversion TTC -> HT / TVA 19%)
        $devisData = $this->prepareDevisData($devis);

        // Chargement du template Blade spécialisé pour le PDF et génération du flux binaire
        $pdf = Pdf::loadView('devis.pdf', array_merge(compact('devis', 'company'), $devisData));
        return $pdf->stream('devis-' . $devis->numero . '.pdf');
    }


}
