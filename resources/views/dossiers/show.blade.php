@extends('layouts.app')
@section('title', 'Gestion détaillée du dossier #' . $dossier->num_dossier)

@section('content')
<div class="container-fluid px-4 py-3">
    {{-- Header / Breadcrumb --}}
    <div class="d-flex align-items-center mb-1">
        <a href="{{ route('dossiers.index') }}" class="btn btn-sm bg-white shadow-sm me-3 border-0 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; border-radius: 8px; color: #1e69ff;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="font-size: 0.75rem; font-weight: 500;">
                <li class="breadcrumb-item"><a href="{{ route('dossiers.index') }}" class="text-primary text-decoration-none">Tickets</a></li>
                <li class="breadcrumb-item active text-muted" aria-current="page">Dossier #{{ $dossier->num_dossier }}</li>
            </ol>
        </nav>
    </div>
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold mb-0" style="color: #1a2332; letter-spacing: -0.5px;">Gestion détaillée du dossier</h1>
        <div class="d-flex align-items-center gap-2">
            {{-- Actions Technicien --}}
            @if(auth()->user()->role === 'Technicien' && $dossier->technicien_id == auth()->id())
                @if($dossier->statut === 'AFFECTE')
                    <form action="{{ route('dossiers.startDiagnostic', $dossier->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-sm rounded-pill px-3 fw-bold shadow-sm">
                            <i class="fas fa-play me-1"></i> Diagnostic
                        </button>
                    </form>
                @elseif($dossier->statut === 'EN_DIAGNOSTIC')
                    <a href="{{ route('diagnostics.create', $dossier->id) }}" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold shadow-sm text-white">
                        <i class="fas fa-microscope me-1"></i> Saisir Diagnostic
                    </a>
                @elseif($dossier->statut === 'EN_REPARATION')
                    <a href="{{ route('interventions.create', $dossier->id) }}" class="btn btn-success btn-sm rounded-pill px-3 fw-bold shadow-sm text-white">
                        <i class="fas fa-wrench me-1"></i> Saisir Intervention
                    </a>
                @endif
                
                @if(!in_array($dossier->statut, ['LIVRE', 'CLOTURE', 'FACTURE', 'ANNULE']))
                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#rejetModal">
                        <i class="fas fa-undo me-1"></i> Retrait
                    </button>
                @endif
            @endif

            {{-- Actions Admin : Validation Remplacement --}}
            @if(auth()->user()->role === 'Admin' && $dossier->statut === 'ATTENTE_VALIDATION_REMPLACEMENT')
                <form action="{{ route('dossiers.validerRemplacement', $dossier->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm rounded-pill px-3 fw-bold shadow-sm text-white" onclick="return confirm('Valider ce remplacement ?')">
                        <i class="fas fa-check me-1"></i> Valider Remplacement
                    </button>
                </form>
                <form action="{{ route('dossiers.refuserRemplacement', $dossier->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold shadow-sm">
                        <i class="fas fa-times me-1"></i> Refuser
                    </button>
                </form>
            @endif

            {{-- Actions Admin : Pièce introuvable --}}
            @if(auth()->user()->role === 'Admin' && $dossier->statut === 'ATTENTE_PIECE')
                <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold shadow-sm text-white" data-bs-toggle="modal" data-bs-target="#modalImpossibleReappro">
                    <i class="fas fa-ban me-1"></i> Impossible Réappro
                </button>
            @endif

            {{-- Actions Agent/Admin : Préparer Remplacement --}}
            @if(in_array(auth()->user()->role, ['Agent', 'Admin']) && $dossier->statut === 'REMPLACEMENT_VALIDE')
                <a href="{{ route('dossiers.preparerRemplacement', $dossier->id) }}" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold shadow-sm text-white">
                    <i class="fas fa-exchange-alt me-1"></i> Préparer Remplacement
                </a>
            @endif

            <a href="{{ route('dossiers.reception.pdf', $dossier->id) }}" class="btn btn-light btn-sm rounded-pill px-3 fw-bold text-dark border shadow-sm">
                <i class="fas fa-file-invoice me-1"></i> Bon
            </a>
            <a href="{{ route('dossiers.etiquette', $dossier->id) }}" class="btn btn-dark btn-sm rounded-pill px-3 fw-bold shadow-sm">
                <i class="fas fa-tag me-1"></i> Étiquette
            </a>
        </div>
    </div>

    {{-- Info Card --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
        <div class="card-body p-4 d-flex align-items-center">
            <div class="bg-primary bg-opacity-10 p-3 rounded-4 me-4 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                <div class="bg-primary rounded-3 p-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px;">
                    <i class="fas fa-folder-open fa-lg text-white"></i>
                </div>
            </div>
            <div class="flex-grow-1">
                <div class="small text-muted text-uppercase fw-bold mb-1" style="font-size: 0.65rem; letter-spacing: 0.8px; opacity: 0.6;">RÉFÉRENCE DOSSIER</div>
                <h2 class="mb-1 fw-bold" style="color: #1a2332; letter-spacing: -1px;">#{{ $dossier->num_dossier }}</h2>
                <div class="text-muted opacity-25" style="letter-spacing: -2px; font-size: 0.8rem;">||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||||</div>
            </div>
            <div class="text-end">
                <div class="small text-muted text-uppercase fw-bold mb-2" style="font-size: 0.6rem; letter-spacing: 0.8px; opacity: 0.6;">STATUT ACTUEL</div>
                <span class="badge rounded-pill px-4 py-2" style="background-color: #eef4ff; color: #1e69ff; font-weight: 700; font-size: 0.7rem; border: 1px solid #e0eaff;">
                    {{ str_replace('_', ' ', $dossier->statut) }}
                </span>
            </div>
        </div>
    </div>

    {{-- Recommendation Banner --}}
    @php
        $action = ['icon' => 'fa-info-circle', 'color' => '#1e69ff', 'title' => 'Informations', 'desc' => 'Dossier en cours de traitement.'];
        
        switch($dossier->statut) {
            case 'RECU':
                $action = ['icon' => 'fa-user-check', 'color' => '#1e69ff', 'title' => 'Affectation requise', 'desc' => 'Veuillez assigner un technicien pour débuter le diagnostic.'];
                break;
            case 'AFFECTE':
                $action = ['icon' => 'fa-play-circle', 'color' => '#f59e0b', 'title' => 'Diagnostic prêt', 'desc' => 'Le technicien peut maintenant commencer l\'expertise technique.'];
                break;
            case 'EN_DIAGNOSTIC':
                $action = ['icon' => 'fa-microscope', 'color' => '#3b82f6', 'title' => 'Expertise en cours', 'desc' => 'Le diagnostic est en cours de saisie par le technicien.'];
                break;
            case 'EN_ATTENTE_DEVIS':
                $action = ['icon' => 'fa-file-invoice-dollar', 'color' => '#ef4444', 'title' => 'Devis à établir', 'desc' => 'Le diagnostic est terminé. Veuillez créer le devis pour le client.'];
                break;
            case 'EN_REPARATION':
                $action = ['icon' => 'fa-tools', 'color' => '#10b981', 'title' => 'Réparation en cours', 'desc' => 'L\'appareil est actuellement en cours de réparation en atelier.'];
                break;
            case 'REPARE':
                $action = ['icon' => 'fa-check-double', 'color' => '#059669', 'title' => 'Prêt pour facturation', 'desc' => 'La réparation est terminée. Vous pouvez générer la facture finale.'];
                break;
            case 'ATTENTE_PIECE':
                $action = ['icon' => 'fa-hourglass-half', 'color' => '#d97706', 'title' => 'En attente de pièces', 'desc' => 'Le dossier est bloqué en attendant la réception des composants nécessaires.'];
                break;
        }
    @endphp
    <div class="card border-0 text-white mb-4 shadow-sm" style="background-color: {{ $action['color'] }}; border-radius: 12px;">
        <div class="card-body p-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <div class="bg-white bg-opacity-20 p-2 rounded-2 me-3">
                    <i class="fas {{ $action['icon'] }} text-white"></i>
                </div>
                <div>
                    <h6 class="mb-1 fw-bold">{{ $action['title'] }}</h6>
                    <p class="mb-0 small opacity-75">{{ $action['desc'] }}</p>
                </div>
            </div>
            
            @if($dossier->statut === 'ATTENTE_PIECE' && auth()->user()->role === 'Admin')
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-danger btn-sm rounded-pill px-4 fw-bold shadow-sm text-white" data-bs-toggle="modal" data-bs-target="#modalImpossibleReappro">
                    <i class="fas fa-ban me-1"></i> Impossible de réapprovisionner
                </button>
            </div>
            @endif
        </div>
    </div>

    {{-- Tabs --}}
    <ul class="nav nav-tabs border-0 mb-4 gap-4" id="dossierTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active p-0 pb-2 border-0 bg-transparent fw-bold d-flex align-items-center" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab" style="color: #1e69ff; border-bottom: 3px solid #1e69ff !important; border-radius: 0;">
                <i class="fas fa-info-circle me-2"></i> Détails
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link p-0 pb-2 border-0 bg-transparent fw-bold text-muted d-flex align-items-center" data-bs-toggle="tab" data-bs-target="#atelier" type="button" role="tab">
                <i class="fas fa-tools me-2"></i> Atelier
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link p-0 pb-2 border-0 bg-transparent fw-bold text-muted d-flex align-items-center" data-bs-toggle="tab" data-bs-target="#finances" type="button" role="tab">
                <i class="fas fa-wallet me-2"></i> Finances
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link p-0 pb-2 border-0 bg-transparent fw-bold text-muted d-flex align-items-center" data-bs-toggle="tab" data-bs-target="#communication" type="button" role="tab">
                <i class="fas fa-comments me-2"></i> Communication
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link p-0 pb-2 border-0 bg-transparent fw-bold text-muted d-flex align-items-center" data-bs-toggle="tab" data-bs-target="#historique" type="button" role="tab">
                <i class="fas fa-history me-2"></i> Historique
            </button>
        </li>
    </ul>

    <div class="tab-content">
        {{-- ONGLET DÉTAILS --}}
        <div class="tab-pane fade show active" id="details">
            <div class="row g-4">
                <div class="col-lg-5">
                    <div class="card shadow-sm border-0 h-100" style="border-radius: 20px; background-color: #ffffff;">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-4 d-flex align-items-center" style="color: #1a2332;">
                                <i class="fas fa-id-card me-2" style="color: #1e69ff;"></i> Information client et appareil
                            </h6>
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="p-3 rounded-3" style="background-color: #f8fafc; border: 1px solid #edf2f7;">
                                        <label class="small text-muted text-uppercase fw-bold mb-1 d-block" style="font-size: 0.65rem; letter-spacing: 0.5px; color: #64748b !important;">NOM CLIENT</label>
                                        <div class="fw-bolder" style="font-size: 1rem; color: #1a2332; font-weight: 800;">{{ $dossier->client->name ?? '—' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 rounded-3" style="background-color: #f8fafc; border: 1px solid #edf2f7;">
                                        <label class="small text-muted text-uppercase fw-bold mb-1 d-block" style="font-size: 0.65rem; letter-spacing: 0.5px; color: #64748b !important;">ADRESSE EMAIL</label>
                                        <div class="fw-bold" style="font-size: 0.85rem; color: #1a2332;">{{ $dossier->client->email ?? '—' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 rounded-3" style="background-color: #f8fafc; border: 1px solid #edf2f7;">
                                        <label class="small text-muted text-uppercase fw-bold mb-1 d-block" style="font-size: 0.65rem; letter-spacing: 0.5px; color: #64748b !important;">NUMÉRO DE TÉLÉPHONE</label>
                                        <div class="fw-bold" style="font-size: 0.9rem; color: #1a2332;">{{ $dossier->client->telephone ?? '—' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-7">
                                    <div class="p-3 rounded-3" style="background-color: #f8fafc; border: 1px solid #edf2f7;">
                                        <label class="small text-muted text-uppercase fw-bold mb-1 d-block" style="font-size: 0.65rem; letter-spacing: 0.5px; color: #64748b !important;">ARTICLE / MODÈLE</label>
                                        <div class="fw-bold" style="font-size: 0.9rem; color: #1a2332;">{{ $dossier->appareil->modele ?? '—' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="p-3 rounded-3" style="background-color: #f8fafc; border: 1px solid #edf2f7;">
                                        <label class="small text-muted text-uppercase fw-bold mb-1 d-block" style="font-size: 0.65rem; letter-spacing: 0.5px; color: #64748b !important;">RÉFÉRENCE PRODUIT</label>
                                        <div class="fw-bold" style="font-size: 0.9rem; color: #1a2332;">{{ $dossier->appareil->reference_produit ?? '—' }}</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="p-3 rounded-3" style="background-color: #f8fafc; border: 1px solid #edf2f7;">
                                        <label class="small text-muted text-uppercase fw-bold mb-1 d-block" style="font-size: 0.65rem; letter-spacing: 0.5px; color: #64748b !important;">NUMÉRO IMEI / SÉRIE</label>
                                        <div class="fw-bold" style="font-size: 1rem; color: #1e69ff;">{{ $dossier->imei }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 h-100" style="border-radius: 20px; background-color: #ffffff;">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-4 d-flex align-items-center" style="color: #1a2332;">
                                <i class="fas fa-clipboard-list me-2" style="color: #1e69ff;"></i> Panne et Accessoires
                            </h6>
                            
                            <div class="mb-4">
                                <label class="small text-muted text-uppercase fw-bold mb-2 d-block" style="font-size: 0.65rem; letter-spacing: 0.5px; color: #64748b !important;">PANNE DÉCLARÉE PAR LE CLIENT</label>
                                <div class="p-3 rounded-3 border-start border-primary border-4" style="background-color: #f8fafc; border: 1px solid #edf2f7;">
                                    <span class="small fw-bold" style="color: #1a2332;">
                                        <span style="color: #1e69ff;">{{ $dossier->type_panne ?? 'Logiciel & Système' }}</span> 
                                        | Détails : {{ $dossier->panne_declaree }}
                                    </span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="small text-muted text-uppercase fw-bold mb-2 d-block" style="font-size: 0.65rem; letter-spacing: 0.5px; color: #64748b !important;">ÉTAT EXTÉRIEUR / OBSERVATIONS</label>
                                <div class="p-3 rounded-3" style="background-color: #f8fafc; border: 1px solid #edf2f7;">
                                    <span class="small" style="color: #64748b;">{{ $dossier->etat_appareil ?: '-' }}</span>
                                </div>
                            </div>

                            <div>
                                <label class="small text-muted text-uppercase fw-bold mb-2 d-block" style="font-size: 0.65rem; letter-spacing: 0.5px; color: #64748b !important;">ACCESSOIRES DÉPOSÉS</label>
                                <p class="small italic mb-0" style="color: #64748b;">{{ $dossier->accessoires_remis ?: 'Aucun accessoire' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-3">
                    <div class="card shadow-sm border-0 mb-4 overflow-hidden" style="border-radius: 20px; background-color: #ffffff;">
                        <div class="p-3 d-flex align-items-center justify-content-between" style="background-color: #1a2332; color: white;">
                            <span class="fw-bold small d-flex align-items-center">
                                <i class="fas fa-user-friends me-2"></i> Affectation
                            </span>
                        </div>
                        <div class="card-body text-center py-5">
                            @php
                                $initials = $dossier->technicien ? strtoupper(substr($dossier->technicien->name, 0, 2)) : '??';
                            @endphp
                            <div class="rounded-circle text-white d-inline-flex align-items-center justify-content-center mb-3 shadow-sm fw-bold" style="width: 70px; height: 70px; font-size: 1.5rem; background-color: #1e69ff;">
                                {{ $initials }}
                            </div>
                            <h6 class="fw-bold mb-1" style="color: #1a2332;">{{ $dossier->technicien->name ?? 'Non assigné' }}</h6>
                            <p class="small text-muted mb-4">Technicien en charge</p>
                            
                            @if(in_array(auth()->user()->role, ['Agent', 'Admin']))
                            <form action="{{ route('dossiers.assign', $dossier->id) }}" method="POST">
                                @csrf
                                <select name="technicien_id" class="form-select form-select-sm mb-3 rounded-pill border-0 bg-light text-center" style="font-size: 0.75rem;">
                                    @foreach($techniciens as $tech)
                                        <option value="{{ $tech->id }}" {{ $dossier->technicien_id == $tech->id ? 'selected' : '' }}>{{ $tech->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm rounded-pill w-100 fw-bold shadow-sm">Modifier l'assignation</button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ONGLET ATELIER --}}
        <div class="tab-pane fade" id="atelier">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 mb-4 h-100" style="border-radius: 20px;">
                        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold text-primary mb-0"><i class="fas fa-microscope me-2"></i> Diagnostic Technique</h6>
                            @if($dossier->diagnostic)
                                <a href="{{ route('dossiers.diagnostic.pdf', $dossier->id) }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold">
                                    <i class="fas fa-print me-1"></i> Imprimer
                                </a>
                            @endif
                        </div>
                        <div class="card-body">
                            @if($dossier->diagnostic)
                                <div class="mb-4">
                                    <label class="small text-muted text-uppercase fw-bold mb-2 d-block" style="font-size: 0.6rem;">CONSTAT TECHNIQUE</label>
                                    <div class="p-3 bg-light rounded-3 border-start border-primary border-4">{{ $dossier->diagnostic->constat }}</div>
                                </div>
                                <div class="mb-0">
                                    <label class="small text-muted text-uppercase fw-bold mb-2 d-block" style="font-size: 0.6rem;">RECOMMANDATION</label>
                                    <div class="p-3 bg-light rounded-3">{{ $dossier->diagnostic->recommandation }}</div>
                                </div>
                            @else
                                <div class="text-center py-5 text-muted small"><i class="fas fa-spinner fa-spin fa-2x mb-2 opacity-25"></i><p>En attente de diagnostic</p></div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 mb-4 h-100" style="border-radius: 20px;">
                        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold text-success mb-0"><i class="fas fa-wrench me-2"></i> Intervention réalisée</h6>
                            @if($dossier->intervention)
                                <a href="{{ route('dossiers.intervention.pdf', $dossier->id) }}" target="_blank" class="btn btn-outline-success btn-sm rounded-pill px-3 fw-bold">
                                    <i class="fas fa-print me-1"></i> Imprimer
                                </a>
                            @endif
                        </div>
                        <div class="card-body">
                            @if($dossier->intervention)
                                <div class="mb-4">
                                    <label class="small text-muted text-uppercase fw-bold mb-2 d-block" style="font-size: 0.6rem;">TRAVAUX EFFECTUÉS</label>
                                    <div class="p-3 bg-light rounded-3 border-start border-success border-4">{{ $dossier->intervention->compte_rendu }}</div>
                                </div>
                            @else
                                <div class="text-center py-5 text-muted small"><i class="fas fa-tools fa-2x mb-2 opacity-25"></i><p>En attente d'intervention</p></div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ONGLET FINANCES --}}
        <div class="tab-pane fade" id="finances">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 mb-4" style="border-radius: 20px;">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-primary mb-3">Devis Estimatif</h6>
                            @if($dossier->devis)
                                <div class="p-3 bg-light rounded-4 mb-3 d-flex justify-content-between align-items-center">
                                    <div><div class="small text-muted">Total TTC</div><div class="h4 fw-bold mb-0">{{ number_format($dossier->devis->montant_ttc, 3, '.', ' ') }} DT</div></div>
                                    <span class="badge rounded-pill px-3 bg-{{ $dossier->devis->statut == 'ACCEPTE' ? 'success' : 'warning' }}">{{ $dossier->devis->statut }}</span>
                                </div>
                                <a href="{{ route('devis.pdf', $dossier->devis->id) }}" target="_blank" class="btn btn-outline-primary w-100 rounded-pill btn-sm fw-bold">Télécharger le PDF</a>
                            @else
                                <div class="text-center py-4 text-muted small">Aucun devis généré pour ce dossier.</div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 mb-4" style="border-radius: 20px;">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-success mb-3">Facturation</h6>
                            @if($dossier->facture)
                                <div class="p-3 bg-light rounded-4 mb-3 d-flex justify-content-between align-items-center">
                                    <div><div class="small text-muted">Montant Net TTC</div><div class="h4 fw-bold mb-0">{{ number_format($dossier->facture->montant_total, 3, '.', ' ') }} DT</div></div>
                                    <span class="badge rounded-pill px-3 bg-success">PAYÉ / FACTURÉ</span>
                                </div>
                                <a href="{{ route('factures.pdf', $dossier->facture->id) }}" target="_blank" class="btn btn-outline-success w-100 rounded-pill btn-sm fw-bold">Télécharger la facture PDF</a>
                            @else
                                <div class="text-center py-4 text-muted small">Aucune facture disponible.</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ONGLET COMMUNICATION --}}
        <div class="tab-pane fade" id="communication">
            <div class="card shadow-sm border-0" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-primary mb-4"><i class="fas fa-comments me-2"></i> Messages interne / Client</h6>
                    <div class="chat-box mb-4" style="max-height: 400px; overflow-y: auto;">
                        @forelse($dossier->messages as $msg)
                            <div class="mb-3 {{ $msg->user_id == auth()->id() ? 'text-end' : '' }}">
                                <div class="d-inline-block p-3 rounded-4 {{ $msg->user_id == auth()->id() ? 'bg-primary text-white shadow-sm' : 'bg-light text-dark shadow-sm' }}" style="max-width: 80%;">
                                    <div class="small fw-bold opacity-75 mb-1">{{ $msg->user->name }}</div>
                                    <div class="small">{{ $msg->message }}</div>
                                    <div class="text-end mt-1" style="font-size: 0.6rem; opacity: 0.5;">{{ $msg->created_at ? $msg->created_at->format('H:i') : '' }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted small"><p>Aucun message échangé.</p></div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- ONGLET HISTORIQUE --}}
        <div class="tab-pane fade" id="historique">
            <div class="card shadow-sm border-0" style="border-radius: 20px;">
                <div class="card-header bg-white py-3 border-0"><h6 class="m-0 fw-bold text-dark"><i class="far fa-clock text-primary me-2"></i> Journal des évènements</h6></div>
                <div class="card-body p-4">
                    <div class="timeline-v2">
                        @foreach($dossier->suivi()->latest()->get() as $log)
                        <div class="timeline-item mb-4 pb-2 position-relative ps-4 border-start border-light" style="border-width: 2px !important;">
                            <div class="timeline-marker position-absolute bg-primary rounded-circle" style="width: 10px; height: 10px; left: -6px; top: 5px;"></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="fw-bold text-primary mb-0" style="font-size: 0.8rem;">{{ str_replace('_', ' ', $log->nouveau_statut) }}</h6>
                                    <small class="text-muted" style="font-size: 0.65rem;">{{ $log->created_at ? $log->created_at->format('d/m/Y H:i') : '' }}</small>
                                </div>
                                <div class="p-2 rounded bg-light mb-1 small text-dark">{{ $log->commentaire }}</div>
                                <div class="small text-muted" style="font-size: 0.65rem;"><i class="fas fa-user-circle me-1"></i> {{ $log->user->name ?? 'Système' }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* Correction du fond bleu forcé par le layout global */
    .nav-tabs .nav-link {
        color: #64748b !important;
        background-color: transparent !important;
        border: none !important;
        font-size: 0.85rem;
        transition: all 0.2s;
    }
    
    .nav-tabs .nav-link.active {
        color: #1e69ff !important;
        background-color: transparent !important;
        border-bottom: 3px solid #1e69ff !important;
        border-radius: 0 !important;
    }

    .breadcrumb-item + .breadcrumb-item::before { content: "/"; color: #cbd5e1; }
    .bg-opacity-20 { background-color: rgba(255,255,255, 0.2) !important; }
    .italic { font-style: italic; }
</style>
{{-- Modal de demande de retrait --}}
<div class="modal fade" id="rejetModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger">Demande de retrait du dossier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dossiers.rejeter', $dossier->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted small">Expliquez pourquoi vous ne pouvez pas traiter ce dossier (pièce indisponible à long terme, expertise manquante, etc.).</p>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">Motif du retrait</label>
                        <textarea name="raison" class="form-control bg-light border-0" rows="4" placeholder="Saisissez votre motif ici..." required style="border-radius: 10px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">ENVOYER LA DEMANDE</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Impossible de Réapprovisionner --}}
<div class="modal fade" id="modalImpossibleReappro" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 15px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-danger">Impossible de réapprovisionner</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dossiers.marquerIrreparable', $dossier->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted small">Vous allez marquer ce dossier comme <strong>IRRÉPARABLE</strong> car les pièces nécessaires ne sont plus disponibles sur le marché.</p>
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">Commentaire administratif</label>
                        <textarea name="commentaire" class="form-control bg-light border-0" rows="3" placeholder="Ex: Pièce obsolète, fournisseur en rupture définitive..." required style="border-radius: 10px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">CONFIRMER COMME IRRÉPARABLE</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
