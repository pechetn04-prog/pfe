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
 * Gère le suivi public et le portail client authentifié.
 */
class ClientController extends Controller
{
    // SUIVI PUBLIC (sans connexion) ───────────────────────────────

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
     * Affichage public simplifié du dossier (données sensibles masquées).
     */
    public function suiviPublic($id)
    {
        $dossier = Dossier::with([
            'suivi' => function ($q) {
                $q->orderBy('created_at', 'asc');
            }
        ])->findOrFail($id);

        $statusConfig = $this->getStatusConfig($dossier->statut, true);

        return view('public.suivi', compact('dossier', 'statusConfig'));
    }

    //  PORTAIL CLIENT AUTHENTIFIÉ ─────────────────────────────────

    /**
     * Détail d'un dossier pour le client connecté.
     */
    public function show($id)
    {
        $dossier = Dossier::with([
            'suivi' => fn($q) => $q->orderBy('created_at', 'asc'),
            'devis',
            'facture',
        ])->findOrFail($id);

        // Autoriser uniquement si l'utilisateur connecté est le propriétaire du dossier (ou Admin/Agent)
        $user = Auth::user();
        if (!$user || ($dossier->client_id !== $user->id && !in_array($user->role, ['Admin', 'Agent']))) {
            abort(403, 'Accès refusé à ce dossier.');
        }

        $statusConfig = $this->getStatusConfig($dossier->statut, false);

        return view('client.ticket', compact('dossier', 'statusConfig'));
    }

    //VALIDATION DEVIS PAR LE CLIENT ──────────────────────────────

    /**
     * Le client accepte le devis.
     */
    public function accepterDevis($id)
    {
        $dossier = Dossier::findOrFail($id);

        // Vérifier accès : l'utilisateur doit être connecté et être le propriétaire du dossier
        $user = Auth::user();
        if (!$user || $dossier->client_id !== $user->id) {
            abort(403, 'Accès refusé.');
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

        // Notification au technicien assigné que devis est accepté et que la réparation peut commencer
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
     * Le client refuse le devis.
     */
    public function refuserDevis($id)
    {
        $dossier = Dossier::findOrFail($id);

        // Vérifier accès : l'utilisateur doit être connecté et être le propriétaire du dossier
        $user = Auth::user();
        if (!$user || $dossier->client_id !== $user->id) {
            abort(403, 'Accès refusé.');
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

    /**
     * Récupère la configuration visuelle d'un statut (icône, étiquette, description).
     * Gère les versions pour le suivi public simplifié et le portail connecté complet.
     */
    private function getStatusConfig(string $statut, bool $isPublic = false): array
    {
        $configs = [
            'RECU' => [
                'icon' => 'fa-box-open',
                'label' => $isPublic ? 'Reçu' : 'Dossier Reçu',
                'desc' => $isPublic ? 'Appareil bien réceptionné.' : 'Votre appareil a bien été réceptionné.'
            ],
            'EN_DIAGNOSTIC' => [
                'icon' => 'fa-microscope',
                
                'label' => $isPublic ? 'Diagnostic' : 'En Diagnostic',
                'desc' => $isPublic ? 'Analyse technique en cours.' : 'Nos techniciens analysent la panne.'
            ],
            'EN_ATTENTE_DEVIS' => [
                'icon' => 'fa-file-invoice-dollar',
                'label' => $isPublic ? 'Devis Prêt' : 'Attente Devis',
                'desc' => $isPublic ? 'En attente de votre validation.' : 'Un devis est prêt pour validation.'
            ],
            'EN_REPARATION' => [
                'icon' => 'fa-wrench',
                'label' => $isPublic ? 'Réparation' : 'En Réparation',
                'desc' => $isPublic ? 'Intervention technique en cours.' : 'L\'intervention technique est en cours.'
            ],
            'REPARE' => [
                'icon' => 'fa-check-double',
                'label' => 'Réparé !',
                'desc' => $isPublic ? 'Prêt pour le retrait.' : 'Votre appareil est prêt pour le retrait.'
            ],
            'LIVRE' => [
                'icon' => 'fa-hand-holding-heart',
                'label' => $isPublic ? 'Livré' : 'Remis / Livré',
                'desc' => $isPublic ? 'Appareil restitué au client.' : 'Merci de votre confiance !'
            ],
            'ATTENTE_PIECE' => [
                'icon' => 'fa-hourglass-start',
                'label' => 'Attente Pièces',
                'desc' => $isPublic ? 'En attente de composants.' : 'Nous attendons les pièces détachées.'
            ],
            'IRREPARABLE' => [
                'icon' => 'fa-exclamation-triangle',
                'label' => 'Irréparable',
                'desc' => $isPublic ? 'Dossier classé non réparable.' : 'Malheureusement, l\'appareil n\'est pas réparable.'
            ],
            'REMPLACEMENT_PRET' => [
                'icon' => 'fa-sync-alt',
                'label' => 'Remplacement Prêt',
                'desc' => 'Nouvel appareil disponible.'
            ],
        ];

        return $configs[$statut] ?? ['icon' => 'fa-info-circle', 'label' => $statut, 'desc' => 'Suivi en cours...'];
    }
}
