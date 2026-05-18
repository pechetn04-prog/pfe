<?php

namespace App\Http\Controllers;

use App\Models\Diagnostic;
use App\Models\Piece;
use App\Models\SuiviDossier;
use App\Models\TarifMo;
use App\Models\Dossier;
use App\Http\Requests\StoreDiagnosticRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Ce contrôleur gère l'évaluation technique et l'établissement des rapports de diagnostic (UC04).
// Pilote la logique de décision automatique (passages en Réparation, Attente Devis, Attente Remplacement, etc.).
class DiagnosticController extends Controller
{
    // Affiche le formulaire de saisie de diagnostic et passe automatiquement l'état à 'EN_DIAGNOSTIC' (UC04 - Point 1).
    public function create(Dossier $dossier)
    {
        // Règle de sécurité : Un technicien ne peut diagnostiquer que les dossiers qui lui sont attribués
        if ($dossier->technicien_id != Auth::id()) {
            abort(403, 'Ce dossier ne vous est pas assigné.');
        }

        // Changement automatique de l'état SAV lors de l'ouverture du dossier d'évaluation
        if ($dossier->statut === 'AFFECTE') {
            $dossier->update(['statut' => 'EN_DIAGNOSTIC']);

            SuiviDossier::create([
                'dossier_id'     => $dossier->id,
                'user_id'        => Auth::id(),
                'ancien_statut'  => 'AFFECTE',
                'nouveau_statut' => 'EN_DIAGNOSTIC',
                'commentaire'    => 'Démarrage de l\'évaluation et du diagnostic technique.',
            ]);
        }

        $pieces = Piece::all();
        $tarifsMo = TarifMo::where('actif', true)->get();

        return view('diagnostics.create', compact('dossier', 'pieces', 'tarifsMo'));
    }

    // Enregistre le rapport de diagnostic finalisé et applique les règles de décision automatique (UC04).
    public function store(StoreDiagnosticRequest $request, Dossier $dossier)
    {

        // Gestion de l'image justificative de la panne
        $photoPath = null;
        if ($request->hasFile('photo_panne')) {
            $photoPath = $request->file('photo_panne')->store('diagnostics', 'public');
        }

        // Création ou mise à jour du diagnostic
        $diagnostic = Diagnostic::updateOrCreate(
            ['dossier_id' => $dossier->id],
            [
                'technicien_id'         => Auth::id(),
                'constat'               => $request->constat_technique,
                'recommandation'        => $request->recommandation,
                'photo_panne'           => $photoPath ?? $dossier->diagnostic->photo_panne ?? null,
                'motif_exclusion'       => $request->has('exclusion_garantie') ? ($request->motif_exclusion ?? 'Usage non conforme') : null,
                'exclusion_commentaire' => $request->exclusion_commentaire,
                'date_diagnostic'       => now(),
            ]
        );

        // Nettoyage des anciennes relations pour prévenir des doublons en cas de réédition
        $diagnostic->pieces()->detach();
        $diagnostic->tarifsMo()->detach();

        // Association des pièces recommandées pour la future intervention ou devis (SNAPSHOT du prix de vente)
        if ($request->has('pieces')) {
            foreach ($request->pieces as $p) {
                if (empty($p['id'])) {
                    continue;
                }
                $piece = Piece::find($p['id']);
                if ($piece) {
                    $diagnostic->pieces()->attach($p['id'], [
                        'quantite'      => $p['quantite'] ?? 1,
                        'prix_unitaire' => $piece->prix_unitaire ?? 0
                    ]);
                }
            }
        }

        // Association des frais de main d'œuvre prévus
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

        // -------------------------------------------------------------
        // UC04 - Point 7 : Logique de décision automatique
        // -------------------------------------------------------------
        $isReparable       = $request->is_reparable == '1';
        $exclusionGarantie = $request->has('exclusion_garantie');
        $isGarantieValide  = $dossier->sous_garantie && !$exclusionGarantie;

        $nouveauStatut = 'EN_DIAGNOSTIC'; // État transitoire de secours

        if ($isReparable) {
            if ($isGarantieValide) {
                $nouveauStatut = 'EN_REPARATION'; // Réparable + garantie valide ➔ Réparation immédiate gratuite (sans devis)
            } else {
                $nouveauStatut = 'EN_ATTENTE_DEVIS'; // Réparable + hors garantie (ou exclu) ➔ Envoi au service commercial
            }
        } else {
            if ($isGarantieValide) {
                $nouveauStatut = 'ATTENTE_VALIDATION_REMPLACEMENT'; // Irréparable + garantie valide ➔ Demande d'échange de l'appareil
            } else {
                $nouveauStatut = 'IRREPARABLE'; // Irréparable + hors garantie ➔ Clôture sans solution possible
            }
        }

        // Enregistrement des informations sur le dossier
        $dossier->update([
            'statut'           => $nouveauStatut,
            'date_diagnostic'  => now(),
            'garantie_annulee' => $exclusionGarantie ? true : $dossier->garantie_annulee
        ]);

        // Audit Trail du dossier
        SuiviDossier::create([
            'dossier_id'     => $dossier->id,
            'user_id'        => Auth::id(),
            'ancien_statut'  => 'EN_DIAGNOSTIC',
            'nouveau_statut' => $nouveauStatut,
            'commentaire'    => $exclusionGarantie 
                ? 'Rapport de diagnostic finalisé — garantie non applicable (exclusion d\'oxydation ou casse retenue).' 
                : 'Rapport de diagnostic finalisé et soumis avec succès.',
        ]);

        // Envoi des notifications automatiques (Client & Administration)
        if ($dossier->client) {
            try {
                $dossier->client->notify(new \App\Notifications\SimpleNotification(
                    'Le diagnostic de votre appareil est terminé.',
                    $dossier
                ));
            } catch (\Exception $e) {
                // Fail-safe
            }
        }

        $adminsAgents = \App\Models\User::whereIn('role', ['Admin', 'Agent'])->get();
        try {
            \Illuminate\Support\Facades\Notification::send($adminsAgents, new \App\Notifications\SimpleNotification(
                'Diagnostic terminé pour le dossier #' . $dossier->id . '. Action requise selon le nouveau statut : ' . $nouveauStatut,
                $dossier
            ));
        } catch (\Exception $e) {
            // Fail-safe
        }

        return redirect()->route('technicien.dashboard')
            ->with('success', 'Diagnostic enregistré. Statut actuel du dossier : ' . $nouveauStatut);
    }

    // Affiche les résultats complets du diagnostic technique.
    public function show(Dossier $dossier)
    {
        $dossier->load('diagnostic.pieces', 'diagnostic.tarifsMo', 'client', 'technicien', 'appareil');

        if (!$dossier->diagnostic) {
            return redirect()->route('diagnostics.create', $dossier->id)
                ->with('error', 'Aucun diagnostic trouvé pour ce dossier.');
        }

        $diag = $dossier->diagnostic;
        $company = \App\Models\ParametreSociete::first();

        return view('diagnostics.show', compact('dossier', 'diag', 'company'));
    }
}
