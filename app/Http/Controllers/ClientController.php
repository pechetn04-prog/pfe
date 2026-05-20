<?php

namespace App\Http\Controllers;

use App\Models\Dossier;
use App\Models\Devis;
use App\Models\SuiviDossier;
use App\Notifications\GenericNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * ClientController
 * Gère le suivi public (UC11) et le portail client authentifié (UC10, UC06).
 */
class ClientController extends Controller
{
    // ─── UC11 : SUIVI PUBLIC (sans connexion) ───────────────────────────────

    /**
     * Page de recherche publique.
     */
    public function index()
    {
        return view('public.recherche');
    }

    /**
     * Recherche par numéro de dossier ou IMEI.
     */
    public function search(Request $request)
    {
        $query = trim($request->input('search'));

        if (empty($query)) {
            return back()->with('error', 'Veuillez saisir un numéro de dossier ou un IMEI.');
        }

        $dossier = Dossier::where('num_dossier', $query)
            ->orWhereHas('appareil', function($q) use ($query) {
                $q->where('imei', $query);
            })
            ->orWhereHas('client', function($q) use ($query) {
                $q->where('telephone', $query);
            })
            ->first();

        if (!$dossier) {
            return back()->withInput()
                ->with('error', 'Aucun dossier trouvé pour « ' . $query . ' ». Vérifiez le numéro ou l\'IMEI saisi.');
        }

        return redirect()->route('client.suivi.public', $dossier->id);
    }

    /**
     * UC11 — Affichage public simplifié du dossier (données sensibles masquées).
     */
    public function suiviPublic($id)
    {
        $dossier = Dossier::with([
            'suivi' => function ($q) {
                $q->orderBy('created_at', 'asc');
            }
        ])->findOrFail($id);

        $statusConfigs = [
            'RECU' => ['icon' => 'fa-box-open', 'class' => 'status-theme-recu', 'bg_class' => 'bg-status-theme-recu-soft', 'label' => 'Reçu', 'desc' => 'Appareil bien réceptionné.'],
            'EN_DIAGNOSTIC' => ['icon' => 'fa-microscope', 'class' => 'status-theme-en_diagnostic', 'bg_class' => 'bg-status-theme-en_diagnostic-soft', 'label' => 'Diagnostic', 'desc' => 'Analyse technique en cours.'],
            'EN_ATTENTE_DEVIS' => ['icon' => 'fa-file-invoice-dollar', 'class' => 'status-theme-en_attente_devis', 'bg_class' => 'bg-status-theme-en_attente_devis-soft', 'label' => 'Devis Prêt', 'desc' => 'En attente de votre validation.'],
            'EN_REPARATION' => ['icon' => 'fa-wrench', 'class' => 'status-theme-en_reparation', 'bg_class' => 'bg-status-theme-en_reparation-soft', 'label' => 'Réparation', 'desc' => 'Intervention technique en cours.'],
            'REPARE' => ['icon' => 'fa-check-double', 'class' => 'status-theme-repare', 'bg_class' => 'bg-status-theme-repare-soft', 'label' => 'Réparé !', 'desc' => 'Prêt pour le retrait.'],
            'LIVRE' => ['icon' => 'fa-hand-holding-heart', 'class' => 'status-theme-livre', 'bg_class' => 'bg-status-theme-livre-soft', 'label' => 'Livré', 'desc' => 'Appareil restitué au client.'],
            'ATTENTE_PIECE' => ['icon' => 'fa-hourglass-start', 'class' => 'status-theme-attente_piece', 'bg_class' => 'bg-status-theme-attente_piece-soft', 'label' => 'Attente Pièces', 'desc' => 'En attente de composants.'],
            'IRREPARABLE' => ['icon' => 'fa-exclamation-triangle', 'class' => 'status-theme-irreparable', 'bg_class' => 'bg-status-theme-irreparable-soft', 'label' => 'Irréparable', 'desc' => 'Dossier classé non réparable.'],
            'REMPLACEMENT_PRET' => ['icon' => 'fa-sync-alt', 'class' => 'status-theme-remplacement_pret', 'bg_class' => 'bg-status-theme-remplacement_pret-soft', 'label' => 'Remplacement Prêt', 'desc' => 'Nouvel appareil disponible.'],
        ];

        $statusConfig = $statusConfigs[$dossier->statut] ?? ['icon' => 'fa-info-circle', 'class' => 'status-theme-recu', 'bg_class' => 'bg-status-theme-recu-soft', 'label' => $dossier->statut, 'desc' => 'Suivi en cours...'];

        return view('public.suivi', compact('dossier', 'statusConfig'));
    }

    // ─── UC10 : PORTAIL CLIENT AUTHENTIFIÉ ─────────────────────────────────

    /**
     * UC10 — Détail d'un dossier pour le client connecté.
     */
    public function show($id)
    {
        $dossier = Dossier::with([
            'suivi' => fn($q) => $q->orderBy('created_at', 'asc'),
            'devis',
            'facture',
        ])->findOrFail($id);

        // Autoriser si l'utilisateur est le propriétaire OU s'il est Admin/Agent
        if (Auth::check()) {
            $user = Auth::user();
            if ($dossier->client_id !== $user->id && !in_array($user->role, ['Admin', 'Agent'])) {
                abort(403, 'Accès refusé à ce dossier.');
            }
        }

        $statusConfigs = [
            'RECU' => ['icon' => 'fa-box-open', 'class' => 'status-theme-recu', 'bg_class' => 'bg-status-theme-recu-soft', 'label' => 'Dossier Reçu', 'desc' => 'Votre appareil a bien été réceptionné.'],
            'EN_DIAGNOSTIC' => ['icon' => 'fa-microscope', 'class' => 'status-theme-en_diagnostic', 'bg_class' => 'bg-status-theme-en_diagnostic-soft', 'label' => 'En Diagnostic', 'desc' => 'Nos techniciens analysent la panne.'],
            'EN_ATTENTE_DEVIS' => ['icon' => 'fa-file-invoice-dollar', 'class' => 'status-theme-en_attente_devis', 'bg_class' => 'bg-status-theme-en_attente_devis-soft', 'label' => 'Attente Devis', 'desc' => 'Un devis est prêt pour validation.'],
            'EN_REPARATION' => ['icon' => 'fa-wrench', 'class' => 'status-theme-en_reparation', 'bg_class' => 'bg-status-theme-en_reparation-soft', 'label' => 'En Réparation', 'desc' => 'L\'intervention technique est en cours.'],
            'REPARE' => ['icon' => 'fa-check-double', 'class' => 'status-theme-repare', 'bg_class' => 'bg-status-theme-repare-soft', 'label' => 'Réparé !', 'desc' => 'Votre appareil est prêt pour le retrait.'],
            'LIVRE' => ['icon' => 'fa-hand-holding-heart', 'class' => 'status-theme-livre', 'bg_class' => 'bg-status-theme-livre-soft', 'label' => 'Remis / Livré', 'desc' => 'Merci de votre confiance !'],
            'ATTENTE_PIECE' => ['icon' => 'fa-hourglass-start', 'class' => 'status-theme-attente_piece', 'bg_class' => 'bg-status-theme-attente_piece-soft', 'label' => 'Attente Pièces', 'desc' => 'Nous attendons les pièces détachées.'],
            'IRREPARABLE' => ['icon' => 'fa-exclamation-triangle', 'class' => 'status-theme-irreparable', 'bg_class' => 'bg-status-theme-irreparable-soft', 'label' => 'Irréparable', 'desc' => 'Malheureusement, l\'appareil n\'est pas réparable.'],
        ];

        $statusConfig = $statusConfigs[$dossier->statut] ?? ['icon' => 'fa-info-circle', 'class' => 'status-theme-recu', 'bg_class' => 'bg-status-theme-recu-soft', 'label' => $dossier->statut, 'desc' => 'Suivi en cours...'];

        return view('client.ticket', compact('dossier', 'statusConfig'));
    }

    // ─── UC06 : VALIDATION DEVIS PAR LE CLIENT ──────────────────────────────

    /**
     * UC06 — Le client accepte le devis.
     */
    public function accepterDevis($id)
    {
        $dossier = Dossier::findOrFail($id);

        // Vérifier accès
        if (Auth::check() && $dossier->client_id !== Auth::id()) {
            abort(403);
        }

        $devis = Devis::where('dossier_id', $dossier->id)
            ->where('statut', 'EN_ATTENTE')
            ->first();

        if (!$devis) {
            return back()->with('error', 'Aucun devis en attente trouvé pour ce dossier.');
        }

        $devis->update([
            'statut' => 'ACCEPTE',
            'date_decision' => now(),
        ]);

        $dossier->update(['statut' => 'EN_REPARATION']);

        SuiviDossier::create([
            'dossier_id' => $dossier->id,
            'user_id' => Auth::id(),
            'ancien_statut' => 'EN_ATTENTE_DEVIS',
            'nouveau_statut' => 'EN_REPARATION',
            'commentaire' => 'Client a accepté le devis. Réparation autorisée.',
        ]);

        // Notification au technicien assigné
        if ($dossier->technicien) {
            $dossier->technicien->notify(new GenericNotification(
                "Devis accepté - Lancer réparation (#{$dossier->num_dossier})",
                "Le client a accepté le devis pour le dossier #{$dossier->num_dossier}. Vous pouvez maintenant commencer la réparation.",
                route('dossiers.show', $dossier->id)
            ));
        }

        return back()->with('success', 'Devis accepté. La réparation va commencer.');
    }

    /**
     * UC06 — Le client refuse le devis.
     */
    public function refuserDevis($id)
    {
        $dossier = Dossier::findOrFail($id);

        if (Auth::check() && $dossier->client_id !== Auth::id()) {
            abort(403);
        }

        $devis = Devis::where('dossier_id', $dossier->id)
            ->where('statut', 'EN_ATTENTE')
            ->first();

        if (!$devis) {
            return back()->with('error', 'Aucun devis en attente trouvé pour ce dossier.');
        }

        $devis->update([
            'statut' => 'REFUSE',
            'date_decision' => now(),
        ]);

        $dossier->update([
            'statut' => 'DEVIS_REFUSE',
            'commentaire_refus' => 'Refusé par le client depuis son espace de suivi.'
        ]);

        SuiviDossier::create([
            'dossier_id' => $dossier->id,
            'user_id' => Auth::id(),
            'ancien_statut' => 'EN_ATTENTE_DEVIS',
            'nouveau_statut' => 'DEVIS_REFUSE',
            'commentaire' => 'Client a refusé le devis. Appareil disponible pour restitution.',
        ]);

        return back()->with('success', 'Devis refusé. Nous vous contacterons pour la restitution.');
    }


}
