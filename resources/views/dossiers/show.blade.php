@extends('layouts.app')
@section('title', 'Gestion détaillée du dossier #' . $dossier->num_dossier)

@push('styles')
<link rel="stylesheet" href="{{ asset('css/ticket_show.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('dossiers.index') }}" class="btn btn-sm btn-outline-secondary me-3" style="border-radius: 10px;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small fw-bold">
                <li class="breadcrumb-item"><a href="#" class="text-muted text-decoration-none">Tickets</a></li>
                <li class="breadcrumb-item active text-primary">Dossier #{{ $dossier->num_dossier }}</li>
            </ol>
        </nav>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-dark fw-bold">Gestion détaillée du dossier</h1>
        <div class="d-flex gap-2">
            {{-- Actions Technicien --}}
            @if(auth()->user()->role === 'Technicien' && $dossier->technicien_id == auth()->id())
                @if($dossier->statut === 'AFFECTE')
                    <form action="{{ route('dossiers.startDiagnostic', $dossier->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-sm shadow-sm px-4 rounded-pill fw-bold">
                            <i class="fas fa-play me-1"></i> Commencer le Diagnostic
                        </button>
                    </form>
                @elseif($dossier->statut === 'EN_DIAGNOSTIC')
                    <a href="{{ route('diagnostics.create', $dossier->id) }}" class="btn btn-primary btn-sm shadow-sm px-4 rounded-pill fw-bold">
                        <i class="fas fa-microscope me-1"></i> Saisir Diagnostic
                    </a>
                @elseif($dossier->statut === 'EN_REPARATION')
                    <a href="{{ route('interventions.create', $dossier->id) }}" class="btn btn-success btn-sm shadow-sm px-4 rounded-pill fw-bold">
                        <i class="fas fa-wrench me-1"></i> Saisir Intervention
                    </a>
                @endif
                
                {{-- Demande de retrait --}}
                @if(!in_array($dossier->statut, ['LIVRE', 'CLOTURE', 'FACTURE']))
                <button type="button" class="btn btn-outline-danger btn-sm shadow-sm px-4 rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#rejetModal">
                    <i class="fas fa-undo me-1"></i> Demander Retrait
                </button>
                @endif
            @endif

            {{-- Actions Admin : Validation Remplacement --}}
            @if(auth()->user()->role === 'Admin' && $dossier->statut === 'ATTENTE_VALIDATION_REMPLACEMENT')
                <form action="{{ route('dossiers.validerRemplacement', $dossier->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm shadow-sm px-4 rounded-pill fw-bold" onclick="return confirm('Valider ce remplacement ?')">
                        <i class="fas fa-check-circle me-1"></i> Valider Remplacement
                    </button>
                </form>
                <form action="{{ route('dossiers.refuserRemplacement', $dossier->id) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm shadow-sm px-4 rounded-pill fw-bold">
                        <i class="fas fa-times-circle me-1"></i> Refuser
                    </button>
                </form>
            @endif

            {{-- Actions Agent/Admin : Préparer Remplacement --}}
            @if(in_array(auth()->user()->role, ['Agent', 'Admin']) && $dossier->statut === 'REMPLACEMENT_VALIDE')
                <a href="{{ route('dossiers.preparerRemplacement', $dossier->id) }}" class="btn btn-primary btn-sm shadow-sm px-4 rounded-pill fw-bold">
                    <i class="fas fa-exchange-alt me-1"></i> Préparer Remplacement
                </a>
            @endif

            <a href="{{ route('dossiers.reception.pdf', $dossier->id) }}" class="btn btn-link text-dark text-decoration-none small fw-bold">
                <i class="fas fa-file-invoice me-1"></i> Bon de réception
            </a>
            <a href="{{ route('dossiers.etiquette', $dossier->id) }}" class="btn btn-dark btn-sm shadow-sm px-4 rounded-pill">
                <i class="fas fa-tag me-1"></i> Étiquette
            </a>
        </div>
    </div>

    {{-- Bandeau Dossier --}}
    <div class="card shadow-sm mb-4 border-0" style="border-radius: 20px;">
        <div class="card-body d-flex align-items-center p-4">
            <div class="bg-primary bg-opacity-10 p-4 rounded-4 me-4 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                <div class="bg-primary rounded-3 p-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="fas fa-folder-open fa-lg text-white"></i>
                </div>
            </div>
            <div class="flex-grow-1">
                <small class="text-muted text-uppercase fw-bold" style="font-size: 0.6rem; letter-spacing: 0.5px; opacity: 0.7;">RÉFÉRENCE DOSSIER</small>
                <h2 class="mb-0 fw-bold text-dark">#{{ $dossier->num_dossier }}</h2>
                <div class="text-muted mt-1" style="letter-spacing: -2px; opacity: 0.3;">|||||||||||||||||||||||||||||||||||||||||||||||||||||</div>
            </div>
            <div class="ms-auto text-end">
                <span class="badge rounded-pill px-4 py-2 bg-primary bg-opacity-10 text-primary fw-bold" style="font-size: 0.8rem;">
                    {{ str_replace('_', ' ', $dossier->statut) }}
                </span>
            </div>
        </div>
    </div>

    {{-- Bandeau Action Recommandée --}}
    @php
        $action = ['icon' => 'fa-info-circle', 'color' => 'primary', 'title' => 'Informations', 'desc' => 'Dossier en cours de traitement.', 'btn' => null, 'btn_icon' => null, 'route' => null, 'method' => 'GET'];
        
        switch($dossier->statut) {
            case 'RECU':
                $action = ['icon' => 'fa-user-check', 'color' => 'primary', 'title' => 'Affectation requise', 'desc' => 'Veuillez assigner un technicien pour débuter le diagnostic.', 'btn' => 'Affecter un technicien', 'btn_icon' => 'fa-user-plus', 'route' => '#details'];
                break;
            case 'AFFECTE':
                $action = [
                    'icon' => 'fa-play-circle', 
                    'color' => 'warning', 
                    'title' => 'En attente de diagnostic', 
                    'desc' => 'Le technicien a été assigné. Le diagnostic peut commencer.',
                    'btn' => 'Lancer le diagnostic',
                    'btn_icon' => 'fa-microscope',
                    'route' => route('diagnostics.create', $dossier->id),
                    'method' => 'GET'
                ];
                break;
            case 'EN_DIAGNOSTIC':
                $action = [
                    'icon' => 'fa-microscope', 
                    'color' => 'info', 
                    'title' => 'Diagnostic en cours', 
                    'desc' => 'Vous avez commencé le diagnostic. Veuillez remplir le compte-rendu.',
                    'btn' => 'Continuer le diagnostic',
                    'btn_icon' => 'fa-edit',
                    'route' => route('diagnostics.create', $dossier->id),
                    'method' => 'GET'
                ];
                break;
            case 'ATTENTE_VALIDATION_REMPLACEMENT':
                $action = ['icon' => 'fa-exclamation-triangle', 'color' => 'danger', 'title' => 'Validation Remplacement', 'desc' => 'L\'appareil est irréparable sous garantie. Une validation admin est requise pour le remplacement.'];
                break;
            case 'REMPLACEMENT_VALIDE':
                $action = ['icon' => 'fa-check-circle', 'color' => 'success', 'title' => 'Remplacement Approuvé', 'desc' => 'Veuillez préparer l\'appareil de remplacement pour le client.'];
                break;
            case 'EN_ATTENTE_DEVIS':
                if ($dossier->devis) {
                    $action = [
                        'icon' => 'fa-clock', 
                        'color' => 'info', 
                        'title' => 'En attente de décision client', 
                        'desc' => 'Le devis #' . $dossier->devis->numero . ' a été envoyé au client. En attente de sa réponse.',
                        'btn' => 'Consulter le devis',
                        'btn_icon' => 'fa-eye',
                        'route' => route('devis.show', $dossier->devis->id),
                        'method' => 'GET'
                    ];
                } else {
                    $action = [
                        'icon' => 'fa-file-invoice-dollar', 
                        'color' => 'warning', 
                        'title' => 'Devis requis', 
                        'desc' => 'Le diagnostic est terminé. Veuillez établir le devis pour le client.',
                        'btn' => 'Établir le devis',
                        'btn_icon' => 'fa-plus-circle',
                        'route' => route('devis.create', $dossier->id),
                        'method' => 'GET'
                    ];
                }
                break;
            case 'DEVIS_REFUSE':
                $action = ['icon' => 'fa-times-circle', 'color' => 'danger', 'title' => 'Devis Refusé', 'desc' => 'Le client a refusé le devis. L\'appareil doit être restitué.'];
                break;
            case 'EN_REPARATION':
                $action = ['icon' => 'fa-tools', 'color' => 'primary', 'title' => 'En cours de réparation', 'desc' => 'La réparation est en cours par le technicien.'];
                break;
            case 'REPARE':
                $action = [
                    'icon' => 'fa-check-double', 
                    'color' => 'success', 
                    'title' => 'Réparation terminée', 
                    'desc' => 'L\'appareil est réparé. Vous pouvez maintenant générer la facture.',
                    'btn' => 'Générer la facture',
                    'btn_icon' => 'fa-file-invoice',
                    'route' => route('facture.create', $dossier->id),
                    'method' => 'GET'
                ];
                break;
        }
    @endphp

    <div class="card border-0 text-white mb-4 shadow-sm overflow-hidden" style="border-radius: 15px; background: {{ $action['color'] == 'primary' ? '#2563eb' : ($action['color'] == 'warning' ? '#ff9800' : ($action['color'] == 'danger' ? '#ef4444' : ($action['color'] == 'success' ? '#10b981' : '#0ea5e9'))) }} !important;">
        <div class="card-body d-flex align-items-center p-4">
            <div class="bg-white bg-opacity-100 p-3 rounded-3 me-4 d-flex align-items-center justify-content-center shadow-sm" style="width: 60px; height: 60px;">
                <i class="fas {{ $action['icon'] }} fa-2x" style="color: {{ $action['color'] == 'primary' ? '#2563eb' : ($action['color'] == 'warning' ? '#ff9800' : ($action['color'] == 'danger' ? '#ef4444' : ($action['color'] == 'success' ? '#10b981' : '#0ea5e9'))) }};"></i>
            </div>
            <div class="flex-grow-1">
                <h5 class="mb-0 fw-bold">{{ $action['title'] }}</h5>
                <p class="mb-0 opacity-90 small">{{ $action['desc'] }}</p>
            </div>
            @if(isset($action['btn']))
                @php
                    $canSeeBtn = true;
                    // Si c'est une action technique (diagnostic), seul le tech assigné ou l'admin peut voir le bouton
                    if (in_array($dossier->statut, ['AFFECTE', 'EN_DIAGNOSTIC']) && auth()->user()->role === 'Agent') {
                        $canSeeBtn = false;
                    }
                    // Pour le technicien, il ne voit le bouton que si c'est son dossier
                    if (auth()->user()->role === 'Technicien' && $dossier->technicien_id !== auth()->id()) {
                        $canSeeBtn = false;
                    }
                @endphp

                @if($canSeeBtn)
                    <div class="ms-auto">
                        @if(isset($action['method']) && $action['method'] === 'POST')
                            <form action="{{ $action['route'] }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-white rounded-pill px-4 fw-bold shadow-sm" style="color: {{ $action['color'] == 'primary' ? '#2563eb' : ($action['color'] == 'warning' ? '#ff9800' : ($action['color'] == 'danger' ? '#ef4444' : ($action['color'] == 'success' ? '#10b981' : '#0ea5e9'))) }}; background: white;">
                                    <i class="fas {{ $action['btn_icon'] }} me-2"></i> {{ $action['btn'] }}
                                </button>
                            </form>
                        @else
                            <a href="{{ $action['route'] }}" class="btn btn-white rounded-pill px-4 fw-bold shadow-sm" style="color: {{ $action['color'] == 'primary' ? '#2563eb' : ($action['color'] == 'warning' ? '#ff9800' : ($action['color'] == 'danger' ? '#ef4444' : ($action['color'] == 'success' ? '#10b981' : '#0ea5e9'))) }}; background: white;">
                                <i class="fas {{ $action['btn_icon'] }} me-2"></i> {{ $action['btn'] }}
                            </a>
                        @endif
                    </div>
                @endif
            @endif
        </div>
    </div>
    </div>

    {{-- Onglets --}}
    <ul class="nav nav-pills mb-4 gap-2" id="dossierTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active d-flex align-items-center gap-2 px-4 py-2" data-bs-toggle="tab" href="#details">
                <i class="fas fa-info-circle"></i> Détails
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center gap-2 px-4 py-2" data-bs-toggle="tab" href="#atelier">
                <i class="fas fa-tools"></i> Atelier
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center gap-2 px-4 py-2" data-bs-toggle="tab" href="#finances">
                <i class="fas fa-wallet"></i> Finances
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center gap-2 px-4 py-2" data-bs-toggle="tab" href="#communication">
                <i class="fas fa-comments"></i> Communication
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link d-flex align-items-center gap-2 px-4 py-2" data-bs-toggle="tab" href="#historique">
                <i class="fas fa-history"></i> Historique
            </a>
        </li>
    </ul>

    <div class="tab-content">
        {{-- ONGLET DÉTAILS --}}
        <div class="tab-pane fade show active" id="details">
            <div class="row">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0 p-4 mb-4" style="border-radius: 20px;">
                        <h6 class="fw-bold text-primary mb-4 d-flex align-items-center gap-2">
                            <i class="fas fa-id-card"></i> Information client et appareil
                        </h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="bg-light bg-opacity-50 p-3 rounded-4">
                                    <label class="small text-muted text-uppercase fw-bold mb-1 d-block" style="font-size: 0.55rem; letter-spacing: 0.5px;">NOM CLIENT</label>
                                    <div class="fw-bold text-dark">{{ $dossier->client->name ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light bg-opacity-50 p-3 rounded-4">
                                    <label class="small text-muted text-uppercase fw-bold mb-1 d-block" style="font-size: 0.55rem; letter-spacing: 0.5px;">ADRESSE EMAIL</label>
                                    <div class="text-dark">{{ $dossier->client->email ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light bg-opacity-50 p-3 rounded-4">
                                    <label class="small text-muted text-uppercase fw-bold mb-1 d-block" style="font-size: 0.55rem; letter-spacing: 0.5px;">NUMÉRO DE TÉLÉPHONE</label>
                                    <div class="text-dark">{{ $dossier->client->telephone ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light bg-opacity-50 p-3 rounded-4">
                                    <label class="small text-muted text-uppercase fw-bold mb-1 d-block" style="font-size: 0.55rem; letter-spacing: 0.5px;">ARTICLE / MODÈLE</label>
                                    <div class="text-dark">{{ $dossier->appareil->modele ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light bg-opacity-50 p-3 rounded-4">
                                    <label class="small text-muted text-uppercase fw-bold mb-1 d-block" style="font-size: 0.55rem; letter-spacing: 0.5px;">RÉFÉRENCE PRODUIT</label>
                                    <div class="text-dark">{{ $dossier->appareil->reference_produit ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="bg-light bg-opacity-50 p-3 rounded-4">
                                    <label class="small text-muted text-uppercase fw-bold mb-1 d-block" style="font-size: 0.55rem; letter-spacing: 0.5px;">NUMÉRO IMEI / SÉRIE</label>
                                    <div class="text-primary fw-bold">{{ $dossier->imei }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow-sm border-0 p-4 mb-4" style="border-radius: 20px;">
                        <h6 class="fw-bold text-primary mb-4 d-flex align-items-center gap-2">
                            <i class="fas fa-clipboard-list"></i> Panne déclarée / Accessoire déposé
                        </h6>
                        <div class="mb-4">
                            <label class="small text-muted text-uppercase fw-bold mb-2 d-block" style="font-size: 0.55rem; letter-spacing: 0.5px;">PANNE DÉCLARÉE PAR LE CLIENT</label>
                            <div class="bg-light bg-opacity-50 p-3 rounded-4 border-start border-primary border-4">
                                <span class="fw-bold text-dark">Logiciel & Système</span> | Détails : {{ $dossier->panne_declaree }}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 mb-4" style="border-radius: 20px; overflow: hidden;">
                        <div class="bg-dark text-white p-3 d-flex align-items-center justify-content-between">
                            <span class="fw-bold small"><i class="fas fa-user-tag me-2"></i> Affectation</span>
                        </div>
                        <div class="card-body text-center py-4">
                            @if(!$dossier->technicien_id || in_array(auth()->user()->role, ['Agent', 'Admin']))
                                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center text-primary mb-3" style="width: 60px; height: 60px; font-size: 1.2rem;">
                                    <i class="fas fa-user-cog"></i>
                                </div>
                                <h6 class="fw-bold mb-3">Technicien</h6>
                                <form action="{{ route('dossiers.assign', $dossier->id) }}" method="POST">
                                    @csrf
                                    <select name="technicien_id" class="form-select mb-3 border-0 bg-light" required>
                                        <option value="">-- Choisir --</option>
                                        @foreach($techniciens as $tech)<option value="{{ $tech->id }}" {{ $dossier->technicien_id == $tech->id ? 'selected' : '' }}>{{ $tech->name }}</option>@endforeach
                                    </select>
                                    <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-bold shadow-sm">Assigner</button>
                                </form>
                            @else
                                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center mb-3 shadow" style="width: 70px; height: 70px; font-size: 1.5rem; font-weight: bold;">
                                    {{ strtoupper(substr($dossier->technicien->name ?? '?', 0, 2)) }}
                                </div>
                                <h6 class="fw-bold mb-1">{{ $dossier->technicien->name ?? '—' }}</h6>
                                <p class="small text-muted mb-0">Assigné à ce dossier</p>
                            @endif
                        </div>
                    </div>

                    {{-- CARTE GARANTIE --}}
                    <div class="card shadow-sm border-0 mb-4" style="border-radius: 20px; overflow: hidden;">
                        <div class="bg-{{ $dossier->garantie_annulee ? 'danger' : ($dossier->sous_garantie ? 'success' : 'secondary') }} text-white p-3 d-flex align-items-center justify-content-between">
                            <span class="fw-bold small"><i class="fas fa-shield-alt me-2"></i> État de la Garantie</span>
                            @if($dossier->garantie_annulee)
                                <span class="badge bg-white text-danger fw-bold rounded-pill">ANNULÉE</span>
                            @elseif($dossier->sous_garantie)
                                <span class="badge bg-white text-success fw-bold rounded-pill">VALIDE</span>
                            @else
                                <span class="badge bg-white text-secondary fw-bold rounded-pill">EXPIRED</span>
                            @endif
                        </div>
                        <div class="card-body py-4">
                            @if($dossier->garantie_annulee)
                                <div class="text-center">
                                    <i class="fas fa-exclamation-triangle fa-2x text-danger mb-3"></i>
                                    <h6 class="fw-bold text-danger">Exclusion appliquée</h6>
                                    <p class="small text-muted mb-0">La garantie a été annulée suite au diagnostic (casse, oxydation, etc.).</p>
                                </div>
                            @else
                                <div class="d-flex justify-content-between mb-2 pb-2 border-bottom border-light">
                                    <span class="small text-muted fw-bold">Date d'achat :</span>
                                    <span class="small fw-bold text-dark">{{ $dossier->date_vente ? $dossier->date_vente->format('d/m/Y') : 'Inconnue' }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-0">
                                    <span class="small text-muted fw-bold">Fin garantie :</span>
                                    <span class="small fw-bold text-{{ $dossier->sous_garantie ? 'success' : 'danger' }}">
                                        {{ $dossier->fin_garantie ? $dossier->fin_garantie->format('d/m/Y') : '—' }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ONGLET ATELIER --}}
        <div class="tab-pane fade" id="atelier">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 mb-4" style="border-radius: 20px;">
                        <div class="card-header bg-white border-0 py-3"><h6 class="fw-bold text-primary mb-0"><i class="fas fa-microscope me-2"></i> Diagnostic</h6></div>
                        <div class="card-body">
                            @if($dossier->diagnostic)
                                <div class="mb-3"><label class="small text-muted fw-bold">CONSTAT</label><div class="p-3 bg-light rounded-4 border-start border-primary border-4">{{ $dossier->diagnostic->constat }}</div></div>
                                <div class="mb-3"><label class="small text-muted fw-bold">RECOMMANDATION</label><div class="p-3 bg-light rounded-4">{{ $dossier->diagnostic->recommandation }}</div></div>
                            @else
                                <div class="text-center py-5 text-muted small"><i class="fas fa-spinner fa-spin fa-2x mb-2 opacity-25"></i><p>En attente de diagnostic</p></div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 mb-4" style="border-radius: 20px;">
                        <div class="card-header bg-white border-0 py-3"><h6 class="fw-bold text-primary mb-0"><i class="fas fa-tools me-2"></i> Intervention</h6></div>
                        <div class="card-body">
                            @if($dossier->intervention)
                                <div class="mb-3"><label class="small text-muted fw-bold">TRAVAUX</label><div class="p-3 bg-light rounded-4 border-start border-success border-4">{{ $dossier->intervention->compte_rendu }}</div></div>
                            @else
                                <div class="text-center py-5 text-muted small"><i class="fas fa-wrench fa-2x mb-2 opacity-25"></i><p>En attente d'intervention</p></div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ONGLET FINANCES --}}
        <div class="tab-pane fade" id="finances">
            <div class="row">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 mb-4" style="border-radius: 20px;">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-primary mb-3">Devis</h6>
                            @if($dossier->devis)
                                <div class="p-3 bg-light rounded-4 mb-3 d-flex justify-content-between align-items-center">
                                    <div><div class="small text-muted">Total TTC</div><div class="h4 fw-bold mb-0">{{ number_format($dossier->devis->montant_ttc, 3, '.', ' ') }} DT</div></div>
                                    <span class="badge bg-{{ $dossier->devis->statut == 'ACCEPTE' ? 'success' : 'warning' }}">{{ $dossier->devis->statut }}</span>
                                </div>
                                <a href="{{ route('devis.pdf', $dossier->devis->id) }}" target="_blank" class="btn btn-outline-primary w-100 rounded-pill btn-sm">Voir le PDF</a>
                            @else
                                <p class="text-muted small">Aucun devis</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 mb-4" style="border-radius: 20px;">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-primary mb-3">Facture</h6>
                            @if($dossier->facture)
                                <div class="p-3 bg-light rounded-4 mb-3 d-flex justify-content-between align-items-center">
                                    <div><div class="small text-muted">Total</div><div class="h4 fw-bold mb-0">{{ number_format($dossier->facture->montant_total, 3, '.', ' ') }} DT</div></div>
                                    <span class="badge bg-success">PAYÉ</span>
                                </div>
                                <a href="{{ route('factures.pdf', $dossier->facture->id) }}" target="_blank" class="btn btn-outline-success w-100 rounded-pill btn-sm">Voir le PDF</a>
                            @else
                                <p class="text-muted small">Aucune facture</p>
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
                    <h6 class="fw-bold text-primary mb-4"><i class="fas fa-comments me-2"></i> Fil de discussion</h6>
                    <div class="chat-box mb-4" style="max-height: 400px; overflow-y: auto;">
                        @forelse($dossier->messages as $msg)
                            <div class="mb-3 {{ $msg->user_id == auth()->id() ? 'text-end' : '' }}">
                                <div class="d-inline-block p-3 rounded-4 {{ $msg->user_id == auth()->id() ? 'bg-primary text-white' : 'bg-light text-dark shadow-sm' }}" style="max-width: 80%;">
                                    <div class="small fw-bold opacity-75 mb-1">{{ $msg->user->name }}</div>
                                    <div class="small">{{ $msg->message }}</div>
                                    <div class="text-end mt-1" style="font-size: 0.6rem; opacity: 0.5;">{{ $msg->created_at ? $msg->created_at->format('H:i') : '' }}</div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted small"><p>Aucun message.</p></div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- ONGLET HISTORIQUE --}}
        <div class="tab-pane fade" id="historique">
            <div class="card shadow-sm border-0" style="border-radius: 20px;">
                <div class="card-header bg-white py-3 border-0"><h6 class="m-0 fw-bold"><i class="far fa-clock text-success me-2"></i> Historique du Workflow</h6></div>
                <div class="card-body p-4">
                    <div class="timeline-v2">
                        @foreach($dossier->suivi()->latest()->get() as $log)
                        <div class="timeline-item mb-4 pb-2 position-relative">
                            <div class="timeline-marker position-absolute start-0"></div>
                            <div class="timeline-content ps-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold text-primary mb-0" style="font-size: 0.85rem;">{{ $log->nouveau_statut }}</h6>
                                    <small class="text-muted" style="font-size: 0.7rem;">{{ $log->created_at ? $log->created_at->format('d M, H:i') : '' }}</small>
                                </div>
                                <div class="comment-box p-3 rounded-4 bg-light mb-2 border-0 shadow-sm" style="background-color: #f8fafc !important;">
                                    <p class="mb-0 small text-dark">{{ $log->commentaire }}</p>
                                </div>
                                <div class="d-flex align-items-center text-muted small" style="font-size: 0.7rem;"><i class="fas fa-user-circle me-2"></i><span>{{ $log->user->name ?? 'Système' }}</span></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recap Garantie (Controle SAV) --}}
    <div class="card shadow-sm border-0 mt-4 mb-5" style="border-radius: 20px; background: #f8fafc;">
        <div class="card-body p-4">
            <h6 class="fw-bold text-dark mb-3"><i class="fas fa-shield-alt text-primary me-2"></i> Contrôle de Garantie</h6>
            <div class="row align-items-center">
                <div class="col-md-3">
                    <div class="p-3 rounded-4 {{ $dossier->sous_garantie && !$dossier->garantie_annulee ? 'bg-success' : 'bg-danger' }} text-white text-center">
                        <div class="small opacity-75 text-uppercase fw-bold">Statut</div>
                        <div class="h5 fw-bold mb-0">{{ $dossier->sous_garantie && !$dossier->garantie_annulee ? 'VALIDE' : 'HORS GARANTIE' }}</div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="row g-3">
                        <div class="col-sm-4">
                            <small class="text-muted d-block">Type</small>
                            <span class="fw-bold">{{ $dossier->sous_garantie ? 'Garantie Constructeur' : 'Sans garantie' }}</span>
                        </div>
                        <div class="col-sm-4">
                            <small class="text-muted d-block">Expiration</small>
                            <span class="fw-bold text-{{ $dossier->sous_garantie ? 'success' : 'muted' }}">{{ $dossier->fin_garantie ? $dossier->fin_garantie->format('d/m/Y') : '—' }}</span>
                        </div>
                        <div class="col-sm-4">
                            <small class="text-muted d-block">Anomalies</small>
                            @if($dossier->garantie_annulee)
                                <span class="badge bg-danger">ANNULÉE PAR TECHNICIEN</span>
                            @else
                                <span class="badge bg-light text-success border border-success">AUCUNE</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

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
                        <label class="form-label fw-bold small text-uppercase">Motif du retrait</label>
                        <textarea name="motif" class="form-control bg-light border-0" rows="4" placeholder="Saisissez votre motif ici..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">ENVOYER LA DEMANDE</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .bg-soft-primary { background-color: rgba(37, 99, 235, 0.1); }
    #dossierTabs.nav-pills .nav-link { color: #8a94ad; background: #fff; border-radius: 12px; transition: all 0.3s; font-weight: 600; font-size: 0.85rem; }
    #dossierTabs.nav-pills .nav-link.active { background: #2563eb !important; color: #fff !important; box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3); }
    .rounded-4 { border-radius: 15px !important; }
    .btn-white { background: #fff; color: #2563eb; border: none; transition: all 0.2s; }
    .btn-white:hover { background: #f8fafc; transform: translateY(-1px); }
    .timeline-v2 { position: relative; padding-left: 20px; border-left: 2px solid #f1f5f9; margin-left: 10px; }
    .timeline-marker { width: 12px; height: 12px; border-radius: 50%; background-color: #2563eb; border: 2px solid #fff; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); left: -7px !important; top: 5px; }
    .comment-box { border-left: 3px solid #cbd5e1; }
</style>
@endsection
