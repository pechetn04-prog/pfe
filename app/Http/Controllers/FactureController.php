<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\SuiviDossier;
use App\Models\Dossier;
use App\Models\TarifMo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class FactureController extends Controller
{
    /**
     * UC08 - Afficher le formulaire de création de facture.
     * Prépare les données financières de l'intervention (pièces, main d'œuvre) et vérifie 
     * l'état de la garantie pour pré-remplir la facture. (Logique métier centralisée).
     *
     * @param Dossier $dossier Le dossier SAV concerné
     * @return \Illuminate\View\View
     */
    public function create(Dossier $dossier)
    {
        // 1. Chargement des relations nécessaires pour optimiser les requêtes (Eager Loading)
        $dossier->load('intervention.pieces', 'intervention.tarifsMo');
        $tarifsMo = TarifMo::where('actif', true)->orderBy('type_intervention')->get();

        // 2. Calcul du coût total des pièces de rechange consommées lors de l'intervention
        $totalPieces = 0;
        if ($dossier->intervention && $dossier->intervention->pieces) {
            foreach ($dossier->intervention->pieces as $piece) {
                $qty = $piece->pivot->quantite ?? 1;
                $pu = $piece->pivot->prix_unitaire ?? $piece->prix_vente;
                $totalPieces += ($qty * $pu);
            }
        }

        // 3. Calcul du coût total de la main d'œuvre (frais d'intervention technique)
        $totalMO = 0;
        if ($dossier->intervention && $dossier->intervention->tarifsMo) {
            foreach ($dossier->intervention->tarifsMo as $mo) {
                $totalMO += $mo->pivot->montant ?? $mo->montant;
            }
        }

        // 4. Gestion automatique de la facturation sous garantie (Gratuité à 100%)
        // Si l'appareil est sous garantie et que la garantie n'a pas été annulée (ex: oxydation), on applique 100% de remise.
        $isGarantieValide = $dossier->sous_garantie && !$dossier->garantie_annulee;
        $defaultRemise = $isGarantieValide ? 100 : 0;

        // 5. Index initial pour le système d'ajout dynamique de lignes (JavaScript Front-end)
        $initialLaborIndex = ($dossier->intervention && $dossier->intervention->tarifsMo)
            ? max(1, $dossier->intervention->tarifsMo->count())
            : 1;

        // Transmission des variables calculées à la vue (Respect strict du design pattern MVC)
        return view('factures.create', compact(
            'dossier',
            'tarifsMo',
            'totalPieces',
            'totalMO',
            'isGarantieValide',
            'defaultRemise',
            'initialLaborIndex'
        ));
    }

    /**
     * Enregistrer la facture finale.
     */
    public function store(Request $request, Dossier $dossier)
    {
        // Charger l'intervention et les pièces associées
        $dossier->load('intervention.pieces');
        $intervention = $dossier->intervention;

        // Vérifier qu'une intervention existe
        if (!$intervention) {
            return back()->with('error', 'Aucune intervention trouvée.');
        }

        // Empêcher de créer plusieurs factures pour le même dossier
        if ($dossier->facture) {
            return back()->with('error', 'Une facture existe déjà pour ce dossier.');
        }

        // Création de la facture
        $next = Facture::count() + 1;
        $numero = 'FAC-' . now()->format('Ymd') . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);

        $facture = Facture::create([
            'dossier_id' => $dossier->id,
            'montant_total' => 0, // Sera mis à jour après calcul des pivots
            'remise' => (float) $request->input('remise', 0),
            'date_facture' => now()->toDateString(),
            'numero' => $numero,
        ]);

        // 1. Calcul et liaison des pièces (copie depuis l'intervention pour figer la facture)
        $totalPieces = 0;
        $piecesToAttach = [];
        foreach ($intervention->pieces as $piece) {
            $totalPieces += $piece->pivot->quantite * $piece->pivot->prix_unitaire;

            $piecesToAttach[$piece->id] = [
                'quantite' => $piece->pivot->quantite,
                'prix_unitaire' => $piece->pivot->prix_unitaire
            ];
        }
        $facture->pieces()->sync($piecesToAttach);

        // 2. Calcul et liaison de la main d'œuvre (depuis la requête)
        $totalMO = 0;
        if ($request->has('labors')) {
            foreach ($request->labors as $l) {
                if (empty($l['id']))
                    continue;
                $tarif = TarifMo::findOrFail($l['id']);
                $montantLigne = $l['montant'] ?? $tarif->montant;
                $facture->tarifsMo()->attach($tarif->id, ['montant' => $montantLigne]);
                $totalMO += $montantLigne;
            }
        }

        $remisePourcentage = (float) $request->input('remise', 0);
        $totalBrutTTC = $totalPieces + $totalMO;
        $montantRemise = $totalBrutTTC * ($remisePourcentage / 100);
        $montantNetTTC = $totalBrutTTC - $montantRemise;

        $facture->update([
            'montant_total' => $montantNetTTC,
            'remise' => $remisePourcentage
        ]);

        // Générer le PDF et le sauvegarder
        $company = \App\Models\ParametreSociete::first();
        $pdf = Pdf::loadView('factures.pdf', compact('facture', 'company'));

        $directory = storage_path('app/public/factures');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $pdfPath = $directory . '/facture-' . $facture->id . '.pdf';
        $pdf->save($pdfPath);


        // Historique et statut
        $ancienStatut = $dossier->statut;
        $dossier->update(['statut' => 'FACTURE']);

        SuiviDossier::create([
            'dossier_id' => $dossier->id,
            'user_id' => auth()->id(),
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => 'FACTURE',
            'commentaire' => 'Facture de réparation générée et enregistrée.',
        ]);

        // Notification au client
        $client = $dossier->client;
        if ($client && $client->email && !str_contains($client->email, '@maisontel.dz')) {
            $client->notify(new \App\Notifications\FactureCreatedNotification($facture));
        }

        return redirect()
            ->route('dossiers.show', $dossier)
            ->with('success', 'Facture créée avec succès.');
    }

    /**
     * Afficher les détails d'une facture.
     */
    public function show(Facture $facture)
    {
        $facture->load('dossier.intervention.pieces');
        return view('factures.show', compact('facture'));
    }

    /**
     * Générer un PDF de la facture.
     */
    public function pdf(Facture $facture)
    {
        $facture->load('dossier.intervention.pieces');
        $company = \App\Models\ParametreSociete::first();

        $pdf = Pdf::loadView('factures.pdf', compact('facture', 'company'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream('facture-' . $facture->numero . '.pdf');
    }
}
