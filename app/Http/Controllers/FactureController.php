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
     * Afficher la page de création de facture.
     */
    public function create(Dossier $dossier)
    {
        // Charger l'intervention et ses pièces/mains d'oeuvre
        $dossier->load('intervention.pieces', 'intervention.tarifsMo');
        $tarifsMo = TarifMo::where('actif', true)->orderBy('type_intervention')->get();

        return view('factures.create', compact('dossier', 'tarifsMo'));
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
            'commentaire' => 'Facture générée.',
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
