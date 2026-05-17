<?php

namespace App\Http\Controllers;

use App\Models\Dossier;
use App\Models\Devis;
use App\Models\Avis;
use App\Models\SuiviDossier;
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

        return view('public.suivi', compact('dossier'));
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

        return view('client.ticket', compact('dossier'));
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

        $dossier->update(['statut' => 'DEVIS_REFUSE']);

        SuiviDossier::create([
            'dossier_id' => $dossier->id,
            'user_id' => Auth::id(),
            'ancien_statut' => 'EN_ATTENTE_DEVIS',
            'nouveau_statut' => 'DEVIS_REFUSE',
            'commentaire' => 'Client a refusé le devis. Appareil disponible pour restitution.',
        ]);

        return back()->with('success', 'Devis refusé. Nous vous contacterons pour la restitution.');
    }

    // ─── AVIS CLIENT ─────────────────────────────────────────────────────────

    /**
     * Soumettre un avis après livraison.
     */
    public function submitAvis(Request $request, $id)
    {
        $request->validate([
            'note' => 'required|integer|min:1|max:5',
            'commentaire' => 'nullable|string|max:500',
        ]);

        $dossier = Dossier::findOrFail($id);

        if (Auth::check() && $dossier->client_id !== Auth::id()) {
            abort(403);
        }

        Avis::updateOrCreate(
            ['dossier_id' => $dossier->id],
            [
                'note' => $request->note,
                'commentaire' => $request->commentaire,
                'client_id' => Auth::id(),
            ]
        );

        return back()->with('success', 'Merci pour votre avis !');
    }
}
