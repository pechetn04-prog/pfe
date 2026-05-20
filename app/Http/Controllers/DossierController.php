<?php

namespace App\Http\Controllers;

use App\Models\Dossier;
use App\Models\User;
use App\Models\Vente;
use App\Models\Appareil;
use App\Models\SuiviDossier;
use App\Models\ParametreSociete;
use App\Http\Requests\StoreDossierRequest;
use App\Http\Requests\AssignDossierRequest;
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

        // 1. Filtrer par statut (Exclure CLOTURE par défaut si aucun statut n'est choisi)
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        } else {
            $query->where('statut', '!=', 'CLOTURE');
        }

        // 2. Recherche globale
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

        // 3. Filtre par Technicien
        if ($request->filled('technicien_id')) {
            $query->where('technicien_id', $request->technicien_id);
        }

        // 4. Filtre par Garantie
        if ($request->filled('garantie')) {
            $query->where('sous_garantie', $request->garantie);
        }

        // 5. Filtre par plage de dates (Réception)
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

        $pannes = [
            'Écran & Affichage',
            'Batterie & Alimentation',
            'Connectique & Ports',
            'Caméra',
            'Audio',
            'Connectivité',
            'Logiciel & Système',
            'Dommages Physiques',
            'Sécurité & Accès',
            'Autre'
        ];

        return view('dossiers.create', compact('techniciens', 'pannes'));
    }

    /**
     * UC03 (Réception) — Enregistrer un nouveau dossier SAV.
     */
    public function store(StoreDossierRequest $request)
    {

        // 1. Vérifier si un dossier actif existe déjà pour cet IMEI
        $dossierExistant = Dossier::whereHas('appareil', function ($q) use ($request) {
            $q->where('imei', $request->imei);
        })->whereNotIn('statut', ['LIVRE', 'CLOTURE'])->first();

        if ($dossierExistant) {
            return back()->withInput()->with('error', "Un dossier (#{$dossierExistant->num_dossier}) est déjà ouvert pour cet IMEI.");
        }

        // 2. Récupérer ou créer l'appareil
        $appareil = Appareil::where('imei', $request->imei)->first();
        if (!$appareil) {
            $appareil = Appareil::create([
                'imei' => $request->imei,
                'modele' => $request->modele,
                'reference_produit' => $request->reference_produit,
            ]);
        }

        // 3. Gérer le client (Recherche par email OU téléphone pour éviter les doublons)
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
            $email = $request->client_email ?: 'client_' . str_replace('.', '', microtime(true)) . '@maisontel.dz';
            // Mot de passe par défaut = numéro de téléphone (sinon sav12345)
            $defaultPassword = $request->client_telephone ?: 'sav12345';

            $client = User::create([
                'name' => $request->client_nom,
                'email' => $email,
                'password' => Hash::make($defaultPassword),
                'role' => 'Client',
                'telephone' => $request->client_telephone,
                'actif' => true
            ]);

            // Stocker le mot de passe en clair pour le passer à la notification
            $client->_plainPassword = $defaultPassword;
        } else {
            // Optionnel : Mettre à jour l'email si le client n'en avait pas
            if (!$client->email || str_contains($client->email, '@maisontel')) {
                if ($request->client_email) {
                    $client->update(['email' => $request->client_email]);
                }
            }
        }

        // Assurer la liaison de l'appareil avec son propriétaire (client)
        $appareil->update(['client_id' => $client->id]);

        // 4. Vérification de la garantie via les ventes
        $vente = Vente::where('imei', $request->imei)->first();
        $sousGarantie = false;
        if ($vente) {
            $finGarantie = Carbon::parse($vente->date_vente)->addMonths($vente->duree_garantie_mois);
            $sousGarantie = now()->lessThanOrEqualTo($finGarantie);
        }

        // 5. Création du dossier (Utilisation du max ID pour plus de sécurité)
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
            'fin_garantie' => $vente ? Carbon::parse($vente->date_vente)->addMonths($vente->duree_garantie_mois) : null,
            'statut' => 'AFFECTE',
            'sous_garantie' => $sousGarantie,
            'panne_declaree' => $panneComplete,
            'etat_appareil' => $request->etat_appareil,
            'accessoires_remis' => (function () use ($request) {
                $accs = is_array($request->accessoires) ? $request->accessoires : [];
                if ($request->filled('accessoires_autre')) {
                    // Supprimer "Autre..." de la liste pour le remplacer par la valeur précise
                    if (($key = array_search('Autre...', $accs)) !== false) {
                        unset($accs[$key]);
                    }
                    $accs[] = 'Autre: ' . $request->accessoires_autre;
                }
                return implode(', ', $accs);
            })(),
        ]);

        SuiviDossier::create([
            'dossier_id' => $dossier->id,
            'user_id' => Auth::id(),
            'ancien_statut' => null,
            'nouveau_statut' => $dossier->statut,
            'commentaire' => 'Dossier de réparation créé et enregistré.',
        ]);

        // Notification au client par email
        if ($client && $client->email && !str_contains($client->email, '@maisontel.dz')) {
            $client->notify(new TicketCreatedNotification($dossier, $client->_plainPassword ?? null));
        }

        // Notification au technicien assigné
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
     * UC15 — Affecter / réaffecter un technicien à un dossier.
     */
    public function assign(AssignDossierRequest $request, Dossier $dossier)
    {

        // UC15 : Bloquer si dossier clôturé ou livré
        if (in_array($dossier->statut, ['LIVRE', 'CLOTURE', 'FACTURE'])) {
            return back()->with('error', 'Impossible de réaffecter un dossier clôturé ou livré.');
        }

        // UC15 : Bloquer si même technicien
        if ($dossier->technicien_id == $request->technicien_id) {
            return back()->with('error', 'Ce technicien est déjà affecté à ce dossier.');
        }

        $ancienStatut = $dossier->statut;

        // Si le dossier est en attente (RECU) ou déjà affecté, on s'assure qu'il passe/reste en AFFECTE.
        // Sinon (en diagnostic, en réparation, etc.), on garde le statut actuel pour ne pas casser le flux.
        $nouveauStatut = in_array($ancienStatut, ['RECU', 'AFFECTE']) ? 'AFFECTE' : $ancienStatut;

        $dossier->update([
            'technicien_id' => $request->technicien_id,
            'statut' => $nouveauStatut,
        ]);

        SuiviDossier::create([
            'dossier_id' => $dossier->id,
            'user_id' => Auth::id(),
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => $nouveauStatut,
            'commentaire' => $request->commentaire ?? "Dossier réaffecté à {$dossier->technicien->name}. Statut conservé : {$nouveauStatut}.",
        ]);

        return back()->with('success', 'Technicien affecté avec succès.');
    }

    /**
     * Vérification IMEI via AJAX (UC03).
     */
    public function checkImei(Request $request)
    {
        $imei = trim($request->query('imei'));

        $appareil = Appareil::where('imei', $imei)->first();
        $vente = Vente::where('imei', $imei)->first();

        $venteInfo = null;
        if ($vente) {
            $dateVente = Carbon::parse($vente->date_vente);
            $finGarantie = $dateVente->copy()->addMonths($vente->duree_garantie_mois);
            $sousGarantie = now()->lessThanOrEqualTo($finGarantie);

            $venteInfo = [
                'date_vente' => $dateVente->format('d/m/Y'),
                'fin_garantie' => $finGarantie->format('d/m/Y'),
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
     * UC09 — Marquer le dossier comme livré.
     */
    public function livrer(Dossier $dossier)
    {
        // UC09 : Bloquer si l'appareil est réparé mais pas encore facturé (le passage par l'état FACTURE est obligatoire)
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
     * UC09 — Clôturer définitivement le dossier.
     */
    public function cloturer(Dossier $dossier)
    {
        // UC09 : Bloquer si pas encore livré
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
     * UC12 — Formulaire de préparation du remplacement (irréparable sous garantie).
     */
    public function preparerRemplacement(Dossier $dossier)
    {
        return view('remplacements.preparer', compact('dossier'));
    }

    /**
     * UC12 — Enregistrer le remplacement et basculer statut.
     */
    public function storeRemplacement(Request $request, Dossier $dossier)
    {
        $request->validate([
            'imei_remplacement' => 'required|string',
            'modele_remplacement' => 'nullable|string',
        ]);

        $ancienStatut = $dossier->statut;
        $modele = $request->modele_remplacement ?: $dossier->appareil->modele;

        // Enregistrer le nouvel appareil dans la table ventes avec type REMPLACEMENT
        Vente::create([
            'type' => 'REMPLACEMENT',
            'imei' => $request->imei_remplacement,
            'modele' => $modele,
            'client_nom' => $dossier->client->name,
            'date_vente' => now()->toDateString(),
            'duree_garantie_mois' => 12,
            'reference_produit' => $dossier->appareil->reference_produit ?? null,
            'numero_facture_vente' => 'SAV-REMP-' . $dossier->num_dossier,
        ]);

        // Mettre à jour le dossier
        $dossier->update([
            'statut' => 'REMPLACEMENT_PRET',
            'imei_remplacement' => $request->imei_remplacement,
            'modele_remplacement' => $modele,
        ]);

        SuiviDossier::create([
            'dossier_id' => $dossier->id,
            'user_id' => Auth::id(),
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => 'REMPLACEMENT_PRET',
            'commentaire' => "Appareil de substitution préparé — Modèle : {$modele} / IMEI : {$request->imei_remplacement}.",
        ]);

        return redirect()->route('dossiers.show', $dossier->id)
            ->with('success', "Remplacement enregistré. Appareil {$modele} (IMEI : {$request->imei_remplacement}) prêt pour livraison.");
    }

    /**
     * Étiquette d'identification du dossier (impression).
     */
    public function etiquette(Dossier $dossier)
    {
        return view('dossiers.etiquette', compact('dossier'));
    }

    /**
     * Valider la demande de remplacement (Admin) - UC12
     */
    public function validateReplacement(Dossier $dossier)
    {
        $dossier->update(['statut' => 'REMPLACEMENT_VALIDE']);

        SuiviDossier::create([
            'dossier_id' => $dossier->id,
            'user_id' => Auth::id(),
            'ancien_statut' => 'ATTENTE_VALIDATION_REMPLACEMENT',
            'nouveau_statut' => 'REMPLACEMENT_VALIDE',
            'commentaire' => 'Remplacement de l\'appareil approuvé par l\'administration.',
        ]);

        // Notification aux agents SAV
        $agents = User::where('role', 'Agent')->where('actif', true)->get();
        foreach ($agents as $agent) {
            $agent->notify(new GenericNotification(
                "Remplacement validé (#{$dossier->num_dossier})",
                "L'administration a validé le remplacement. Veuillez préparer un appareil neuf.",
                route('dossiers.show', $dossier->id)
            ));
        }

        return back()->with('success', 'Remplacement validé. L\'agent SAV a été notifié pour préparer l\'appareil.');
    }

    /**
     * Refuser la demande de remplacement (Admin) - UC12
     */
    public function refuseReplacement(Request $request, Dossier $dossier)
    {
        $request->validate([
            'raison' => 'required|string|min:5'
        ], [
            'raison.required' => 'Le motif du refus est obligatoire.'
        ]);

        $dossier->update(['statut' => 'REMPLACEMENT_REFUSE']);

        SuiviDossier::create([
            'dossier_id' => $dossier->id,
            'user_id' => Auth::id(),
            'ancien_statut' => 'ATTENTE_VALIDATION_REMPLACEMENT',
            'nouveau_statut' => 'REMPLACEMENT_REFUSE',
            'commentaire' => 'Remplacement refusé par l\'administration. Motif : ' . $request->raison,
        ]);

        // Notification aux agents SAV
        $agents = User::where('role', 'Agent')->where('actif', true)->get();
        foreach ($agents as $agent) {
            $agent->notify(new GenericNotification(
                "Remplacement refusé (#{$dossier->num_dossier})",
                "L'administration a refusé le remplacement. Motif : {$request->raison}. Veuillez informer le client.",
                route('dossiers.show', $dossier->id)
            ));
        }

        return back()->with('warning', 'Remplacement refusé. Le dossier est passé en statut Remplacement Refusé.');
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
