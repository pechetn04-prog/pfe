<?php

namespace App\Http\Controllers;

use App\Models\Dossier;
use App\Models\User;
use App\Models\Vente;
use App\Models\Appareil;
use App\Models\SuiviDossier;
use App\Models\ParametreSociete;
use App\Http\Requests\StoreDossierRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Notifications\TicketCreatedNotification;
use App\Notifications\GenericNotification;
use App\Notifications\PieceRecueNotification;

class DossierController extends Controller
{
    /**
     * Liste de tous les dossiers (Admin / Agent).
     */
    public function index(Request $request)
    {
        $query = Dossier::with(['client', 'technicien', 'appareil'])->latest();

        // Filtre par statut (CLOTURE exclu par défaut)
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        } else {
            $query->where('statut', '!=', 'CLOTURE');
        }

        // Recherche globale
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('num_dossier', 'like', "%{$search}%")
                    ->orWhereHas('appareil', function ($q2) use ($search) {
                        $q2->where('imei', 'like', "%{$search}%");
                    })
                    ->orWhereHas('client', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%")
                            ->orWhere('telephone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('technicien', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filtre par Technicien
        if ($request->filled('technicien_id')) {
            $query->where('technicien_id', $request->technicien_id);
        }

        // Filtre par Garantie
        if ($request->filled('garantie')) {
            $query->where('sous_garantie', $request->garantie);
        }

        // Filtre par dates de réception
        if ($request->filled('from')) {
            $query->whereDate('date_reception', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->whereDate('date_reception', '<=', $request->to);
        }

        $dossiers = $query->paginate(20);

        $stats_kpis = [
            ['label' => 'TOTAL TICKETS', 'val' => $dossiers->total(), 'icon' => 'fa-folder', 'color' => '#2563eb', 'bg' => '#eff6ff'],
            ['label' => 'AFFECTÉS', 'val' => Dossier::where('statut', 'AFFECTE')->count(), 'icon' => 'fa-user-check', 'color' => '#0ea5e9', 'bg' => '#f0f9ff'],
            ['label' => 'EN DIAGNOSTIC', 'val' => Dossier::where('statut', 'EN_DIAGNOSTIC')->count(), 'icon' => 'fa-microscope', 'color' => '#f59e0b', 'bg' => '#fff7ed'],
            ['label' => 'EN RÉPARATION', 'val' => Dossier::where('statut', 'EN_REPARATION')->count(), 'icon' => 'fa-tools', 'color' => '#6366f1', 'bg' => '#e0e7ff'],
        ];

        return view('dossiers.index', compact('dossiers', 'stats_kpis'));
    }



    /**
     * Formulaire de création de dossier.
     */
    public function create()
    {
        $techniciens = User::where('role', 'Technicien')
            ->where('actif', true)
            ->withCount([
                'dossiers as dossiers_en_cours' => function ($q) {
                    $q->whereNotIn('statut', ['LIVRE', 'CLOTURE']);
                }
            ])
            ->get();

        $pannes = array_merge(User::SPECIALITES, ['Autre']);

        return view('dossiers.create', compact('techniciens', 'pannes'));
    }

    /**
     *Enregistrer un nouveau dossier SAV.
     */
    public function store(StoreDossierRequest $request)
    {

        // Vérifier si un dossier actif existe déjà pour cet IMEI
        $dossierExistant = Dossier::whereHas('appareil', function ($q) use ($request) {
            $q->where('imei', $request->imei);
        })->whereNotIn('statut', ['LIVRE', 'CLOTURE'])->first();

        if ($dossierExistant) {
            return back()->withInput()->with('error', "Un dossier (#{$dossierExistant->num_dossier}) est déjà ouvert pour cet IMEI.");
        }

        // Récupérer ou créer l'appareil
        $appareil = Appareil::where('imei', $request->imei)->first();
        if (!$appareil) {
            $appareil = Appareil::create([
                'imei' => $request->imei,
                'modele' => $request->modele,
                'reference_produit' => $request->reference_produit,
            ]);
        }

        // Gérer ou créer le client (Recherche par email ou téléphone)
        $clientExistant = true;
        $client = User::where('role', 'Client')
            ->where(function ($q) use ($request) {
                if ($request->client_email) {
                    $q->where('email', $request->client_email);
                }
                if ($request->client_telephone) {
                    $q->orWhere('telephone', $request->client_telephone);
                }
            })->first();

        if (!$client) {
            $clientExistant = false;
            $email = $request->client_email ?: 'client_' . str_replace('.', '', microtime(true)) . '@maisontel.tn';
            $defaultPassword = $request->client_telephone ?: 'sav12345';

            $client = User::create([
                'name' => $request->client_nom,
                'email' => $email,
                'password' => Hash::make($defaultPassword),
                'role' => 'Client',
                'telephone' => $request->client_telephone,
                'actif' => true
            ]);

            $client->_plainPassword = $defaultPassword;
        } else {
            // Mettre à jour l'email si nécessaire
            if (!$client->email || str_contains($client->email, '@maisontel')) {
                if ($request->client_email) {
                    $client->update(['email' => $request->client_email]);
                }
            }
        }

        // Lier l'appareil au client
        $appareil->update(['client_id' => $client->id]);

        // Vérification de la garantie
        $vente = Vente::where('imei', $request->imei)->first();
        $sousGarantie = false;
        if ($vente) {
            $sousGarantie = VenteController::estSousGarantie($vente);
        }

        // Création du dossier
        $nextId = (Dossier::max('id') ?? 0) + 1;
        $numDossier = 'D' . now()->format('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

        $pannesSelectees = is_array($request->type_pannes) ? implode(', ', $request->type_pannes) : '';
        $panneComplete = $pannesSelectees ? '[' . $pannesSelectees . '] ' . $request->panne_declaree : $request->panne_declaree;

        $dossier = Dossier::create([
            'num_dossier' => $numDossier,
            'appareil_id' => $appareil->id,
            'client_id' => $client->id,
            'agent_id' => Auth::id(),
            'technicien_id' => $request->technicien_id,
            'date_reception' => now(),
            'date_vente' => $vente ? $vente->date_vente : null,
            'fin_garantie' => $vente ? VenteController::dateExpiration($vente) : null,
            'statut' => 'AFFECTE',
            'sous_garantie' => $sousGarantie,
            'panne_declaree' => $panneComplete,
            'etat_appareil' => $request->etat_appareil,
            'accessoires_remis' => (function () use ($request) {
                $accs = is_array($request->accessoires) ? $request->accessoires : [];
                if ($request->filled('accessoires_autre')) {
                    if (($key = array_search('Autre...', $accs)) !== false) {
                        unset($accs[$key]);
                    }
                    $accs[] = 'Autre: ' . $request->accessoires_autre;
                }
                return implode(', ', $accs);
            })(),
        ]);

        // Tracing historique
        SuiviDossier::create([
            'dossier_id' => $dossier->id,
            'user_id' => Auth::id(),
            'ancien_statut' => null,
            'nouveau_statut' => $dossier->statut,
            'commentaire' => 'Dossier de réparation créé et enregistré.',
        ]);

        // Notification client
        if ($client && $client->email && !str_contains($client->email, '@maisontel.tn')) {
            $client->notify(new TicketCreatedNotification($dossier, $client->_plainPassword ?? null));
        }

        // Notification technicien
        if ($dossier->technicien) {
            $dossier->technicien->notify(new GenericNotification(
                "Nouveau dossier assigné (#{$dossier->num_dossier})",
                "Vous avez été assigné au dossier #{$dossier->num_dossier} pour l'appareil {$appareil->modele}.",
                route('dossiers.show', $dossier->id)
            ));
        }

        $clientMsg = $clientExistant
            ? "Compte client existant associé"
            : "Nouveau compte client créé";

        return redirect()->route('dossiers.show', $dossier->id)
            ->with('success', "Le dossier #{$numDossier} a été créé avec succès ({$clientMsg}).");
    }

    /**
     * Détail d'un dossier.
     */
    public function show(Dossier $dossier)
    {
        // Sécurité : Un technicien ne peut voir que ses propres dossiers
        if (Auth::user()->role === 'Technicien' && $dossier->technicien_id !== Auth::id()) {
            abort(403, "Vous n'êtes pas autorisé à consulter ce dossier.");
        }

        $dossier->load([
            'client',
            'technicien',
            'appareil',
            'suivi.user',
            'diagnostic.pieces',
            'diagnostic.tarifsMo',
            'intervention.pieces',
            'intervention.tarifsMo',
            'devis',
            'facture',
            'messages.user',
        ]);

        $techniciens = User::where('role', 'Technicien')->where('actif', true)->get();

        $action = ['icon' => 'fa-list-check', 'color' => '#1e69ff', 'title' => 'Action suivante recommandée', 'desc' => 'Suivez l\'avancement du dossier via les onglets ci-dessous.'];

        switch ($dossier->statut) {
            case 'RECU':
                $action['desc'] = 'Affectation d\'un technicien requise.';
                break;
            case 'AFFECTE':
                $action['desc'] = 'Le diagnostic est prêt à être effectué.';
                break;
            case 'EN_DIAGNOSTIC':
                $action['desc'] = 'L\'expertise technique est en cours.';
                break;
            case 'EN_ATTENTE_DEVIS':
                $action['desc'] = 'Établissement du devis en attente.';
                break;
            case 'EN_REPARATION':
                $action['desc'] = 'Réparation en cours en atelier.';
                break;
            case 'REPARE':
                $action['desc'] = 'Appareil réparé, prêt pour facturation.';
                break;
            case 'ATTENTE_PIECE':
                $action['desc'] = 'Dossier en attente de pièces détachées.';
                break;
            case 'IRREPARABLE':
                $action['desc'] = 'Appareil déclaré irréparable.';
                break;
            case 'LIVRE':
                $action['desc'] = 'Appareil restitué, prêt pour clôture.';
                break;
            case 'CLOTURE':
                $action['desc'] = 'Dossier clôturé et archivé.';
                break;
            case 'ATTENTE_VALIDATION_REMPLACEMENT':
                $action['desc'] = 'Validation du remplacement requise.';
                break;
            case 'REMPLACEMENT_VALIDE':
                $action['desc'] = 'Remplacement validé, préparation en cours.';
                break;
            case 'REMPLACEMENT_REFUSE':
                $action['desc'] = 'Remplacement refusé, prêt pour restitution.';
                break;
            case 'DEVIS_REFUSE':
                $action['desc'] = 'Devis refusé, prêt pour restitution.';
                break;
        }

        $messages = $dossier->messages->sortBy('created_at')->map(function ($msg) {
            $msg->isInternal = str_starts_with($msg->message, '[INT] ');
            $msg->cleanMessage = $msg->isInternal ? substr($msg->message, 6) : $msg->message;
            $msg->isMe = $msg->user_id == Auth::id();
            return $msg;
        });

        return view('dossiers.show', compact('dossier', 'techniciens', 'action', 'messages'));
    }

    /**
     * Vérification IMEI via AJAX .
     */
    public function checkImei(Request $request)
    {
        $imei = trim($request->query('imei'));

        $appareil = Appareil::where('imei', $imei)->first();
        $vente = Vente::where('imei', $imei)->first();

        $venteInfo = null;
        if ($vente) {
            $dateVente = Carbon::parse($vente->date_vente);
            $finGarantie = VenteController::dateExpiration($vente);
            $sousGarantie = VenteController::estSousGarantie($vente);

            $venteInfo = [
                'date_vente' => $dateVente->format('d/m/Y'),
                'fin_garantie' => $finGarantie ? $finGarantie->format('d/m/Y') : '—',
                'duree' => $vente->duree_garantie_mois . ' mois',
                'sous_garantie' => $sousGarantie,
                'facture' => $vente->numero_facture_vente ?? '—',
            ];
        }

        if ($appareil) {
            return response()->json([
                'found' => true,
                'source' => 'appareil',
                'device' => [
                    'modele' => $appareil->modele,
                    'reference_produit' => $appareil->reference_produit,
                    'client_nom' => $appareil->client->name ?? '',
                    'client_email' => $appareil->client->email ?? '',
                    'client_telephone' => $appareil->client->telephone ?? '',
                ],
                'vente' => $venteInfo
            ]);
        }

        if ($vente) {
            return response()->json([
                'found' => true,
                'source' => 'vente',
                'device' => [
                    'modele' => $vente->modele,
                    'reference_produit' => $vente->reference_produit,
                    'client_nom' => $vente->client_nom,
                    'client_email' => $vente->client_email,
                    'client_telephone' => '',
                ],
                'vente' => $venteInfo
            ]);
        }

        return response()->json([
            'found' => false,
            'message' => 'Appareil non répertorié dans nos ventes (Hors Vente).',
            'sous_garantie' => false
        ]);
    }

    /**
     * Suggérer des techniciens disponibles via AJAX.
     */
    public function suggestTechnicians(Request $request)
    {
        $techniciens = User::where('role', 'Technicien')
            ->where('actif', true)
            ->withCount([
                'dossiers as dossiers_en_cours' => function ($q) {
                    $q->whereNotIn('statut', ['LIVRE', 'CLOTURE']);
                }
            ])
            ->orderBy('dossiers_en_cours')
            ->get(['id', 'name', 'specialite']);

        return response()->json($techniciens);
    }



    /**
     * Marquer le dossier comme livré.
     */
    public function livrer(Dossier $dossier)
    {
        // Bloquer si l'appareil est réparé mais pas encore facturé (le passage par l'état FACTURE est obligatoire)
        if ($dossier->statut === 'REPARE') {
            return back()->with('error', 'Veuillez générer la facture avant de livrer l\'appareil.');
        }

        // Sécurité supplémentaire pour les autres cas non clôturés
        if (!in_array($dossier->statut, ['FACTURE', 'IRREPARABLE', 'REMPLACEMENT_PRET', 'REMPLACEMENT_REFUSE', 'DEVIS_REFUSE'])) {
            return back()->with('error', 'Le dossier n\'est pas dans un état permettant la livraison.');
        }

        $ancienStatut = $dossier->statut;
        $dossier->update([
            'statut' => 'LIVRE',
            'date_livraison' => now(),
        ]);

        SuiviDossier::create([
            'dossier_id' => $dossier->id,
            'user_id' => Auth::id(),
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => 'LIVRE',
            'commentaire' => 'Appareil restitué au client. Livraison confirmée.',
        ]);

        return back()->with('success', 'Dossier marqué comme livré.');
    }

    /**
     * Clôturer définitivement le dossier.
     */
    public function cloturer(Dossier $dossier)
    {
        // Bloquer si pas encore livré
        if ($dossier->statut !== 'LIVRE') {
            return back()->with('error', 'Impossible de clôturer un dossier non livré.');
        }

        $dossier->update([
            'statut' => 'CLOTURE',
            'date_cloture' => now(),
        ]);

        SuiviDossier::create([
            'dossier_id' => $dossier->id,
            'user_id' => Auth::id(),
            'ancien_statut' => 'LIVRE',
            'nouveau_statut' => 'CLOTURE',
            'commentaire' => 'Dossier archivé et clôturé.',
        ]);

        return back()->with('success', 'Dossier clôturé avec succès.');
    }


    /**
     * Étiquette d'identification du dossier (impression).
     */
    public function etiquette(Dossier $dossier)
    {
        return view('dossiers.etiquette', compact('dossier'));
    }


    /**
     * Marquer une pièce comme introuvable.
     */
    public function pieceIntrouvable(Request $request, Dossier $dossier)
    {
        $ancienStatut = $dossier->statut;
        $dossier->update(['statut' => 'ATTENTE_PIECE']);

        SuiviDossier::create([
            'dossier_id' => $dossier->id,
            'user_id' => Auth::id(),
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => 'ATTENTE_PIECE',
            'commentaire' => 'Composant introuvable — dossier suspendu en attente de réapprovisionnement.',
        ]);

        return back()->with('warning', 'Dossier mis en attente pièce.');
    }

    /**
     * Marquer la pièce comme reçue / Réapprovisionnée.
     */
    public function marquerPieceRecue(Dossier $dossier)
    {
        $ancienStatut = $dossier->statut;
        $dossier->update(['statut' => 'EN_REPARATION']);

        SuiviDossier::create([
            'dossier_id' => $dossier->id,
            'user_id' => Auth::id(),
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => 'EN_REPARATION',
            'commentaire' => 'Pièce réceptionnée. Reprise de l\'intervention en cours.',
        ]);

        // Notifier le technicien
        if ($dossier->technicien) {
            $dossier->technicien->notify(new PieceRecueNotification($dossier));
        }

        return back()->with('success', 'Dossier réapprovisionné, prêt pour la réparation.');
    }

    /**
     * Marquer un dossier comme irréparable (Admin).
     */
    public function marquerIrreparable(Dossier $dossier)
    {
        $ancienStatut = $dossier->statut;
        $dossier->update(['statut' => 'IRREPARABLE']);

        SuiviDossier::create([
            'dossier_id' => $dossier->id,
            'user_id' => Auth::id(),
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => 'IRREPARABLE',
            'commentaire' => 'Classé irréparable par la direction technique.',
        ]);

        return back()->with('warning', 'Dossier marqué comme irréparable.');
    }

    // ─── PDFs ──────────────────────────────────────────────────────────────

    /**
     * PDF du bon de réception.
     */
    public function receptionPdf(Dossier $dossier)
    {
        $dossier->load('client', 'technicien', 'appareil');
        $company = ParametreSociete::first();
        $pdf = Pdf::loadView('dossiers.reception-pdf', compact('dossier', 'company'))
            ->setPaper('a4', 'portrait');
        return $pdf->stream("bon-reception-{$dossier->num_dossier}.pdf");
    }
}
