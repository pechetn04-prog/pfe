<?php

namespace App\Http\Controllers;

use App\Models\Devis;
use App\Models\Piece;
use App\Models\TarifMo;
use App\Models\Dossier;
use App\Models\SuiviDossier;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class DevisController extends Controller
{
    /**
     * UC05 — Formulaire de création du devis (pré-rempli depuis le diagnostic).
     */
    public function create(Dossier $dossier)
    {
        // UC05 — Vérifier que le devis n'existe pas déjà
        if ($dossier->devis) {
            return redirect()->route('dossiers.show', $dossier->id)
                ->with('error', 'Un devis existe déjà pour ce dossier.');
        }

        $dossier->load('diagnostic.pieces', 'diagnostic.tarifsMo');
        return view('devis.create', compact('dossier'));
    }

    /**
     * UC05 — Enregistrer le devis et notifier le client.
     */
    public function store(Request $request, Dossier $dossier)
    {
        // UC05 — Bloquer si devis déjà créé
        if ($dossier->devis) {
            return back()->with('error', 'Un devis existe déjà pour ce dossier.');
        }

        // UC05 — Bloquer si diagnostic manquant
        if (!$dossier->diagnostic) {
            return back()->with('error', 'Impossible de créer un devis sans diagnostic.');
        }

        $totalTtc = (float) $request->total_ttc;
        
        $devis = Devis::create([
            'dossier_id'   => $dossier->id,
            'numero'       => 'DEV-' . now()->format('Ymd') . '-' . str_pad(Devis::count() + 1, 4, '0', STR_PAD_LEFT),
            'montant_total'=> $totalTtc,
            'frais_mod'    => (float) $request->frais_mod ?? 0,
            'remise'       => (float) $request->remise ?? 0,
            'statut'       => 'EN_ATTENTE',
            'date_creation'=> now(),
        ]);

        // 1. Sauvegarde des pièces (SNAPSHOT du prix au moment du devis)
        if ($request->has('pieces')) {
            foreach ($request->pieces as $p) {
                if (empty($p['id'])) continue;
                $devis->pieces()->attach($p['id'], [
                    'quantite' => $p['quantite'] ?? 1,
                    'prix_unitaire' => $p['prix_unitaire'] ?? 0
                ]);
            }
        }

        // 2. Sauvegarde de la main d'œuvre
        if ($request->has('labors')) {
            foreach ($request->labors as $l) {
                if (empty($l['id'])) continue;
                $devis->tarifsMo()->attach($l['id'], [
                    'montant' => $l['montant'] ?? 0
                ]);
            }
        }

        $dossier->update(['statut' => 'EN_ATTENTE_DEVIS']);

        SuiviDossier::create([
            'dossier_id'    => $dossier->id,
            'user_id'       => auth()->id(),
            'ancien_statut' => 'EN_DIAGNOSTIC',
            'nouveau_statut'=> 'EN_ATTENTE_DEVIS',
            'commentaire'   => 'Devis généré et envoyé au client.',
        ]);

        // UC05 — Notification client
        if ($dossier->client) {
            try {
                $dossier->client->notify(new \App\Notifications\DevisDisponibleNotification($dossier, $devis));
            } catch (\Exception $e) {
                // Ignorer si notification échoue
            }
        }

        return redirect()->route('dossiers.show', $dossier->id)
            ->with('success', 'Devis créé et client notifié.');
    }

    /**
     * Afficher un devis.
     */
    public function show(Devis $devis)
    {
        $devis->load('dossier.client');
        return view('devis.show', compact('devis'));
    }

    /**
     * UC05/UC06 — Agent accepte le devis manuellement (comptoir).
     */
    public function accepterDevis(Request $request, Devis $devis)
    {
        // UC06 — Bloquer si devis déjà traité
        if ($devis->statut !== 'EN_ATTENTE') {
            return back()->with('error', 'Ce devis a déjà été traité.');
        }

        $dossier = $devis->dossier;

        $devis->update([
            'statut'        => 'ACCEPTE',
            'date_decision' => now(),
        ]);

        $dossier->update(['statut' => 'EN_REPARATION']);

        SuiviDossier::create([
            'dossier_id'    => $dossier->id,
            'user_id'       => auth()->id(),
            'ancien_statut' => 'EN_ATTENTE_DEVIS',
            'nouveau_statut'=> 'EN_REPARATION',
            'commentaire'   => 'Devis accepté (validation manuelle agent SAV). Réparation autorisée.',
        ]);

        return back()->with('success', 'Devis accepté. Dossier passé en réparation.');
    }

    /**
     * UC06 — Refuser le devis (agent ou client).
     */
    public function refuser(Request $request, Devis $devis)
    {
        if ($devis->statut !== 'EN_ATTENTE') {
            return back()->with('error', 'Ce devis a déjà été traité.');
        }

        $dossier = $devis->dossier;

        $devis->update([
            'statut'        => 'REFUSE',
            'date_decision' => now(),
        ]);

        $dossier->update(['statut' => 'DEVIS_REFUSE']);

        SuiviDossier::create([
            'dossier_id'    => $dossier->id,
            'user_id'       => auth()->id(),
            'ancien_statut' => 'EN_ATTENTE_DEVIS',
            'nouveau_statut'=> 'DEVIS_REFUSE',
            'commentaire'   => 'Devis refusé. Appareil en attente de restitution.',
        ]);

        return back()->with('success', 'Devis refusé. Dossier en attente de restitution.');
    }

    /**
     * PDF du devis.
     */
    public function pdf(Devis $devis)
    {
        $devis->load('dossier.client', 'dossier.diagnostic.pieces', 'dossier.diagnostic.tarifsMo');
        $company = \App\Models\ParametreSociete::first();
        $pdf = Pdf::loadView('devis.pdf', compact('devis', 'company'));
        return $pdf->stream('devis-' . $devis->numero . '.pdf');
    }
}
