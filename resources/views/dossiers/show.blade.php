@extends('layouts.app')
@section('title', 'Gestion détaillée du dossier #' . $dossier->num_dossier)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/ticket_show.css') }}">
@endpush


@section('content')
    <div class="container-fluid px-4 py-3">
        {{-- Header / Breadcrumb --}}
        <div class="d-flex align-items-center mb-1">
            <a href="{{ route('dossiers.index') }}"
                class="btn btn-sm bg-white shadow-sm me-3 border-0 d-flex align-items-center justify-content-center"
                style="width: 32px; height: 32px; border-radius: 8px; color: #1e69ff;">
                <i class="fas fa-arrow-left"></i>
            </a>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="font-size: 0.75rem; font-weight: 500;">
                    <li class="breadcrumb-item"><a href="{{ route('dossiers.index') }}"
                            class="text-primary text-decoration-none">Tickets</a></li>
                    <li class="breadcrumb-item active text-muted" aria-current="page">Dossier #{{ $dossier->num_dossier }}
                    </li>
                </ol>
            </nav>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h5 fw-bold mb-0" style="color: #1a2332; letter-spacing: -0.5px;">Gestion détaillée du dossier</h1>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('dossiers.reception.pdf', $dossier->id) }}" class="btn btn-light btn-sm rounded-pill px-3 fw-bold text-dark border shadow-sm">
                    <i class="fas fa-file-invoice me-1"></i> Bon
                </a>
                <a href="{{ route('dossiers.etiquette', $dossier->id) }}" class="btn btn-dark btn-sm rounded-pill px-3 fw-bold shadow-sm">
                    <i class="fas fa-tag me-1"></i> Étiquette
                </a>
            </div>
        </div>

        {{-- Recommendation Banner (Only one, without white part) --}}
        @php
            $action = ['color' => '#1e69ff', 'title' => 'Informations', 'desc' => 'Dossier en cours de traitement.'];
            switch($dossier->statut) {
                case 'RECU': $action = ['color' => '#1e69ff', 'title' => 'Affectation requise', 'desc' => 'Veuillez assigner un technicien.']; break;
                case 'AFFECTE': $action = ['color' => '#f59e0b', 'title' => 'Diagnostic prêt', 'desc' => 'L\'expertise peut commencer.']; break;
                case 'EN_DIAGNOSTIC': $action = ['color' => '#3b82f6', 'title' => 'Expertise en cours', 'desc' => 'Le diagnostic est en cours.']; break;
                case 'EN_ATTENTE_DEVIS': $action = ['color' => '#ef4444', 'title' => 'Devis à établir', 'desc' => 'Veuillez créer le devis.']; break;
                case 'EN_REPARATION': $action = ['color' => '#10b981', 'title' => 'Réparation en cours', 'desc' => 'L\'appareil est en atelier.']; break;
                case 'REPARE': $action = ['color' => '#059669', 'title' => 'Prêt pour facturation', 'desc' => 'La réparation est terminée.']; break;
                case 'ATTENTE_PIECE': $action = ['color' => '#d97706', 'title' => 'En attente de pièces', 'desc' => 'Le dossier est bloqué.']; break;
                case 'IRREPARABLE': $action = ['color' => '#ef4444', 'title' => 'Appareil Irréparable', 'desc' => 'Retour au client recommandé.']; break;
                case 'LIVRE': $action = ['color' => '#6366f1', 'title' => 'Appareil Livré', 'desc' => 'Prêt pour clôture.']; break;
                case 'CLOTURE': $action = ['color' => '#64748b', 'title' => 'Dossier Clôturé', 'desc' => 'Dossier archivé.']; break;
                case 'DEVIS_REFUSE': $action = ['color' => '#64748b', 'title' => 'Devis Refusé', 'desc' => 'Prêt pour restitution.']; break;
            }
        @endphp

        {{-- Info Card --}}
        <div class="card border-0 shadow-sm mb-3" style="border-radius: 15px;">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3 d-flex align-items-center justify-content-center"
                    style="width: 50px; height: 50px;">
                    <div class="bg-primary rounded-2 p-2 d-flex align-items-center justify-content-center shadow-sm"
                        style="width: 35px; height: 35px;">
                        <i class="fas fa-folder-open fa-sm text-white"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <div class="small text-muted text-uppercase fw-bold mb-1"
                        style="font-size: 0.6rem; letter-spacing: 0.8px; opacity: 0.6;">RÉFÉRENCE DOSSIER</div>
                    <h4 class="mb-0 fw-bold" style="color: #1a2332; letter-spacing: -0.5px;">#{{ $dossier->num_dossier }}
                    </h4>
                </div>
                <div class="text-end">
                    <div class="small text-muted text-uppercase fw-bold mb-2"
                        style="font-size: 0.6rem; letter-spacing: 0.8px; opacity: 0.6;">STATUT ACTUEL</div>
                    <span class="badge rounded-pill px-4 py-2"
                        style="background-color: #eef4ff; color: #1e69ff; font-weight: 700; font-size: 0.7rem; border: 1px solid #e0eaff;">
                        {{ str_replace('_', ' ', $dossier->statut) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Recommendation Banner --}}
        @php
            $action = ['icon' => 'fa-info-circle', 'color' => '#1e69ff', 'title' => 'Informations', 'desc' => 'Dossier en cours de traitement.'];

            switch ($dossier->statut) {
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
                case 'IRREPARABLE':
                    $action = ['icon' => 'fa-times-circle', 'color' => '#ef4444', 'title' => 'Appareil Irréparable', 'desc' => 'Le diagnostic a conclu que l\'appareil ne peut pas être réparé. Prêt pour retour au client.'];
                    break;
                case 'LIVRE':
                    $action = ['icon' => 'fa-truck', 'color' => '#6366f1', 'title' => 'Appareil Livré', 'desc' => 'L\'appareil a été remis au client. Vous pouvez clôturer le dossier définitivement.'];
                    break;
                case 'CLOTURE':
                    $action = ['icon' => 'fa-lock', 'color' => '#64748b', 'title' => 'Dossier Clôturé', 'desc' => 'Ce dossier est terminé et archivé. Aucune modification n\'est plus possible.'];
                    break;
                case 'ATTENTE_VALIDATION_REMPLACEMENT':
                    $action = ['icon' => 'fa-exchange-alt', 'color' => '#1e69ff', 'title' => 'Validation de Remplacement', 'desc' => 'L\'appareil est irréparable mais sous garantie. Veuillez valider ou refuser le remplacement.'];
                    break;
                case 'REMPLACEMENT_VALIDE':
                    $action = ['icon' => 'fa-check-circle', 'color' => '#10b981', 'title' => 'Remplacement Validé', 'desc' => 'L\'administration a validé le remplacement. En attente de préparation de l\'appareil neuf par un agent.'];
                    break;
                case 'REMPLACEMENT_REFUSE':
                    $action = ['icon' => 'fa-times-circle', 'color' => '#ef4444', 'title' => 'Remplacement Refusé', 'desc' => 'Le remplacement a été refusé. Prêt pour restitution au client.'];
                    break;
                case 'DEVIS_REFUSE':
                    $action = ['icon' => 'fa-times-circle', 'color' => '#64748b', 'title' => 'Devis Refusé', 'desc' => 'Le client a refusé le devis. Prêt pour restitution de l\'appareil.'];
                    break;
            }
        @endphp
        <div class="card border-0 text-white mb-3 shadow-sm"
            style="background-color: {{ $action['color'] }}; border-radius: 10px;">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="mb-0 fw-bold small">{{ $action['title'] }}</h6>
                    <p class="mb-0" style="font-size: 0.75rem; opacity: 0.8;">{{ $action['desc'] }}</p>
                </div>

                @if(in_array(auth()->user()->role, ['Admin', 'Agent']))
                    <div class="d-flex gap-2 flex-wrap">
                        {{-- Devis --}}
                        @if($dossier->statut === 'EN_ATTENTE_DEVIS' && !$dossier->devis)
                            <a href="{{ route('devis.create', $dossier->id) }}" class="btn btn-light text-danger btn-sm rounded-pill px-3 fw-bold shadow-sm">
                                <i class="fas fa-file-invoice-dollar me-1"></i> Établir le Devis
                            </a>
                        @endif

                        {{-- Facture --}}
                        @if($dossier->statut === 'REPARE' && !$dossier->facture)
                            <a href="{{ route('factures.create', $dossier->id) }}" class="btn btn-light text-success btn-sm rounded-pill px-3 fw-bold shadow-sm">
                                <i class="fas fa-file-invoice me-1"></i> Générer Facture
                            </a>
                        @endif

                        {{-- Remplacement --}}
                        @if($dossier->statut === 'ATTENTE_VALIDATION_REMPLACEMENT' && auth()->user()->role === 'Admin')
                            <form action="{{ route('dossiers.validerRemplacement', $dossier->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-light text-success btn-sm rounded-pill px-3 fw-bold shadow-sm" onclick="return confirm('Valider le remplacement ?')">
                                    <i class="fas fa-check-circle me-1"></i> Valider Remplacement
                                </button>
                            </form>
                            <button type="button" class="btn btn-light text-danger btn-sm rounded-pill px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#refusRemplacementModal">
                                <i class="fas fa-times-circle me-1"></i> Refuser
                            </button>
                        @endif

                        @if($dossier->statut === 'REMPLACEMENT_VALIDE' && auth()->user()->role === 'Agent')
                            <button type="button" class="btn btn-light text-primary btn-sm rounded-pill px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#preparerRemplacementModal">
                                <i class="fas fa-box-open me-1"></i> Préparer Appareil Neuf
                            </button>
                        @endif

                        {{-- Livraison / Restitution --}}
                        @if(in_array($dossier->statut, ['FACTURE', 'IRREPARABLE', 'REMPLACEMENT_PRET', 'REMPLACEMENT_REFUSE', 'DEVIS_REFUSE']))
                            <form action="{{ route('dossiers.livrer', $dossier->id) }}" method="POST" onsubmit="return confirm('Confirmer la remise de l\'appareil au client ?')">
                                @csrf
                                <button type="submit" class="btn btn-light text-primary btn-sm rounded-pill px-3 fw-bold shadow-sm text-uppercase">
                                    <i class="fas {{ $dossier->statut == 'DEVIS_REFUSE' ? 'fa-undo' : 'fa-truck' }} me-1"></i>
                                    {{ $dossier->statut == 'DEVIS_REFUSE' ? 'Restituer' : 'Livrer' }}
                                </button>
                            </form>
                        @endif

                        {{-- Clôture --}}
                        @if($dossier->statut === 'LIVRE')
                            <form action="{{ route('dossiers.cloturer', $dossier->id) }}" method="POST" onsubmit="return confirm('Clôturer définitivement le dossier ?')">
                                @csrf
                                <button type="submit" class="btn btn-light text-success btn-sm rounded-pill px-3 fw-bold shadow-sm">
                                    <i class="fas fa-lock me-1"></i> Clôturer Dossier
                                </button>
                            </form>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        {{-- Tabs --}}
        <ul class="nav nav-tabs border-0 mb-4 gap-2" id="dossierTabs" role="tablist"
            style="border-bottom: 1px solid #edf2f7 !important;">
            <li class="nav-item" role="presentation">
                <button class="nav-link active d-flex align-items-center" data-bs-toggle="tab" data-bs-target="#details"
                    type="button" role="tab">
                    <i class="fas fa-info-circle"></i> Détails
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link d-flex align-items-center" data-bs-toggle="tab" data-bs-target="#atelier"
                    type="button" role="tab">
                    <i class="fas fa-tools"></i> Atelier
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link d-flex align-items-center" data-bs-toggle="tab" data-bs-target="#finances"
                    type="button" role="tab">
                    <i class="fas fa-wallet"></i> Finances
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link d-flex align-items-center" data-bs-toggle="tab" data-bs-target="#communication"
                    type="button" role="tab">
                    <i class="fas fa-comments"></i> Communication
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link d-flex align-items-center" data-bs-toggle="tab" data-bs-target="#historique"
                    type="button" role="tab">
                    <i class="fas fa-history"></i> Historique
                </button>
            </li>
        </ul>

        <div class="tab-content">
            {{-- ONGLET DÉTAILS --}}
            <div class="tab-pane fade show active" id="details">
                <div class="row g-3">
                    <div class="col-lg-5">
                        <div class="card shadow-sm border-0 h-100" style="border-radius: 15px; background-color: #ffffff;">
                            <div class="card-body p-3">
                                <h6 class="fw-bold mb-3 d-flex align-items-center"
                                    style="color: #1a2332; font-size: 0.85rem;">
                                    <i class="fas fa-id-card me-2 text-primary"></i> Information client et appareil
                                </h6>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="p-3 rounded-3"
                                            style="background-color: #f8fafc; border: 1px solid #edf2f7;">
                                            <label class="small text-muted text-uppercase fw-bold mb-1 d-block"
                                                style="font-size: 0.65rem; letter-spacing: 0.5px; color: #64748b !important;">NOM
                                                CLIENT</label>
                                            <div class="fw-bolder"
                                                style="font-size: 1rem; color: #1a2332; font-weight: 800;">
                                                {{ $dossier->client->name ?? '—' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-3"
                                            style="background-color: #f8fafc; border: 1px solid #edf2f7;">
                                            <label class="small text-muted text-uppercase fw-bold mb-1 d-block"
                                                style="font-size: 0.65rem; letter-spacing: 0.5px; color: #64748b !important;">ADRESSE
                                                EMAIL</label>
                                            <div class="fw-bold" style="font-size: 0.85rem; color: #1a2332;">
                                                {{ $dossier->client->email ?? '—' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-3"
                                            style="background-color: #f8fafc; border: 1px solid #edf2f7;">
                                            <label class="small text-muted text-uppercase fw-bold mb-1 d-block"
                                                style="font-size: 0.65rem; letter-spacing: 0.5px; color: #64748b !important;">NUMÉRO
                                                DE TÉLÉPHONE</label>
                                            <div class="fw-bold" style="font-size: 0.9rem; color: #1a2332;">
                                                {{ $dossier->client->telephone ?? '—' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="p-3 rounded-3"
                                            style="background-color: #f8fafc; border: 1px solid #edf2f7;">
                                            <label class="small text-muted text-uppercase fw-bold mb-1 d-block"
                                                style="font-size: 0.65rem; letter-spacing: 0.5px; color: #64748b !important;">ARTICLE
                                                / MODÈLE</label>
                                            <div class="fw-bold" style="font-size: 0.9rem; color: #1a2332;">
                                                {{ $dossier->appareil->modele ?? '—' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="p-3 rounded-3"
                                            style="background-color: #f8fafc; border: 1px solid #edf2f7;">
                                            <label class="small text-muted text-uppercase fw-bold mb-1 d-block"
                                                style="font-size: 0.65rem; letter-spacing: 0.5px; color: #64748b !important;">RÉFÉRENCE
                                                PRODUIT</label>
                                            <div class="fw-bold" style="font-size: 0.9rem; color: #1a2332;">
                                                {{ $dossier->appareil->reference_produit ?? '—' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="p-3 rounded-3"
                                            style="background-color: #f8fafc; border: 1px solid #edf2f7;">
                                            <label class="small text-muted text-uppercase fw-bold mb-1 d-block"
                                                style="font-size: 0.65rem; letter-spacing: 0.5px; color: #64748b !important;">NUMÉRO
                                                IMEI / SÉRIE</label>
                                            <div class="fw-bold" style="font-size: 1rem; color: #1e69ff;">
                                                {{ $dossier->imei }}</div>
                                        </div>
                                    </div>

                                    @if($dossier->imei_remplacement)
                                        <div class="col-12 mt-2">
                                            <div class="p-3 rounded-3"
                                                style="background-color: #f0fdf4; border: 1px solid #bcf0da;">
                                                <label class="small text-success text-uppercase fw-bold mb-1 d-block"
                                                    style="font-size: 0.65rem; letter-spacing: 0.5px;">APPAREIL DE
                                                    REMPLACEMENT</label>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <div class="small text-muted" style="font-size: 0.7rem;">Modèle</div>
                                                        <div class="fw-bold" style="color: #065f46; font-size: 0.9rem;">
                                                            {{ $dossier->modele_remplacement }}</div>
                                                    </div>
                                                    <div class="text-end">
                                                        <div class="small text-muted" style="font-size: 0.7rem;">Nouvel IMEI
                                                        </div>
                                                        <div class="fw-bold" style="color: #10b981; font-size: 0.9rem;">
                                                            {{ $dossier->imei_remplacement }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card shadow-sm border-0 h-100" style="border-radius: 15px; background-color: #ffffff;">
                            <div class="card-body p-3">
                                <h6 class="fw-bold mb-3 d-flex align-items-center"
                                    style="color: #1a2332; font-size: 0.85rem;">
                                    <i class="fas fa-clipboard-list me-2 text-primary"></i> Panne et Accessoires
                                </h6>

                                <div class="mb-4">
                                    <label class="small text-muted text-uppercase fw-bold mb-1 d-block"
                                        style="font-size: 0.6rem; letter-spacing: 0.5px;">PANNE DÉCLARÉE PAR LE
                                        CLIENT</label>
                                    <div class="fw-bold" style="color: #1a2332; font-size: 0.9rem;">
                                        <span
                                            style="color: #1e69ff;">{{ $dossier->type_panne ?? 'Logiciel & Système' }}</span>
                                        <span class="mx-1 text-muted">|</span>
                                        {{ $dossier->panne_declaree }}
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="small text-muted text-uppercase fw-bold mb-2 d-block"
                                        style="font-size: 0.65rem; letter-spacing: 0.5px; color: #64748b !important;">ÉTAT
                                        EXTÉRIEUR / OBSERVATIONS</label>
                                    <div class="p-3 rounded-3"
                                        style="background-color: #f8fafc; border: 1px solid #edf2f7;">
                                        <span class="small"
                                            style="color: #64748b;">{{ $dossier->etat_appareil ?: '-' }}</span>
                                    </div>
                                </div>

                                <div>
                                    <label class="small text-muted text-uppercase fw-bold mb-2 d-block"
                                        style="font-size: 0.65rem; letter-spacing: 0.5px; color: #64748b !important;">ACCESSOIRES
                                        DÉPOSÉS</label>
                                    <p class="small italic mb-0" style="color: #64748b;">
                                        {{ $dossier->accessoires_remis ?: 'Aucun accessoire' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="card shadow-sm border-0 mb-4 overflow-hidden"
                            style="border-radius: 15px; background-color: #ffffff;">
                            <div class="p-2 px-3 d-flex align-items-center justify-content-between"
                                style="background-color: #1a2332; color: white;">
                                <span class="fw-bold" style="font-size: 0.75rem;">
                                    <i class="fas fa-user-friends me-1"></i> Affectation
                                </span>
                            </div>
                            <div class="card-body text-center py-4">
                                @php
                                    $initials = $dossier->technicien ? strtoupper(substr($dossier->technicien->name, 0, 2)) : '??';
                                @endphp
                                <div class="rounded-circle text-white d-inline-flex align-items-center justify-content-center mb-2 shadow-sm fw-bold"
                                    style="width: 50px; height: 50px; font-size: 1.1rem; background-color: #1e69ff;">
                                    {{ $initials }}
                                </div>
                                <h6 class="fw-bold mb-0" style="color: #1a2332; font-size: 0.9rem;">
                                    {{ $dossier->technicien->name ?? 'Non assigné' }}</h6>
                                <p class="small text-muted mb-3" style="font-size: 0.7rem;">Technicien en charge</p>

                                @if(auth()->user()->role === 'Admin')
                                    <form action="{{ route('dossiers.assign', $dossier->id) }}" method="POST">
                                        @csrf
                                        <select name="technicien_id"
                                            class="form-select form-select-sm mb-3 rounded-pill border-0 bg-light text-center"
                                            style="font-size: 0.75rem;">
                                            @foreach($techniciens as $tech)
                                                <option value="{{ $tech->id }}" {{ $dossier->technicien_id == $tech->id ? 'selected' : '' }}>{{ $tech->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit"
                                            class="btn btn-primary btn-sm rounded-pill w-100 fw-bold shadow-sm">Modifier
                                            l'assignation</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ONGLET ATELIER --}}
            <div class="tab-pane fade" id="atelier">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <div class="card shadow-sm border-0 mb-3 h-100" style="border-radius: 15px;">
                            <div
                                class="card-header bg-white border-0 py-2 px-3 d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold text-primary mb-0" style="font-size: 0.85rem;"><i
                                        class="fas fa-microscope me-1"></i> Diagnostic Technique</h6>
                                @if($dossier->diagnostic)
                                    <a href="{{ route('dossiers.diagnostic.pdf', $dossier->id) }}" target="_blank"
                                        class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold">
                                        <i class="fas fa-print me-1"></i> Imprimer
                                    </a>
                                @endif
                            </div>
                            <div class="card-body">
                                @if($dossier->diagnostic)
                                    <div class="mb-4">
                                        <label class="small text-muted text-uppercase fw-bold mb-2 d-block"
                                            style="font-size: 0.6rem;">CONSTAT TECHNIQUE</label>
                                        <div class="p-3 bg-light rounded-3 border-start border-primary border-4">
                                            {{ $dossier->diagnostic->constat }}</div>
                                    </div>
                                    <div class="mb-0">
                                        <label class="small text-muted text-uppercase fw-bold mb-2 d-block"
                                            style="font-size: 0.6rem;">RECOMMANDATION</label>
                                        <div class="p-3 bg-light rounded-3">{{ $dossier->diagnostic->recommandation }}</div>
                                    </div>
                                @else
                                    <div class="text-center py-5 text-muted small"><i
                                            class="fas fa-clock fa-2x mb-2 opacity-25"></i>
                                        <p>En attente de diagnostic</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card shadow-sm border-0 mb-3 h-100" style="border-radius: 15px;">
                            <div
                                class="card-header bg-white border-0 py-2 px-3 d-flex justify-content-between align-items-center">
                                <h6 class="fw-bold text-success mb-0" style="font-size: 0.85rem;"><i
                                        class="fas fa-wrench me-1"></i> Intervention réalisée</h6>
                                @if($dossier->intervention)
                                    <a href="{{ route('dossiers.intervention.pdf', $dossier->id) }}" target="_blank"
                                        class="btn btn-outline-success btn-sm rounded-pill px-3 fw-bold">
                                        <i class="fas fa-print me-1"></i> Imprimer
                                    </a>
                                @endif
                            </div>
                            <div class="card-body">
                                @if($dossier->intervention)
                                    <div class="mb-4">
                                        <label class="small text-muted text-uppercase fw-bold mb-2 d-block"
                                            style="font-size: 0.6rem;">TRAVAUX EFFECTUÉS</label>
                                        <div class="p-3 bg-light rounded-3 border-start border-success border-4">
                                            {{ $dossier->intervention->compte_rendu }}</div>
                                    </div>
                                @else
                                    <div class="text-center py-5 text-muted small"><i
                                            class="fas fa-tools fa-2x mb-2 opacity-25"></i>
                                        <p>En attente d'intervention</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ONGLET FINANCES --}}
            <div class="tab-pane fade" id="finances">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0 mb-3" style="border-radius: 15px;">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold text-primary mb-0" style="font-size: 0.85rem;">Devis Estimatif</h6>
                                    @if($dossier->devis)
                                        <a href="{{ route('devis.pdf', $dossier->devis->id) }}" target="_blank"
                                            class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold"
                                            style="font-size: 0.75rem;">
                                            <i class="fas fa-file-pdf me-1"></i> PDF
                                        </a>
                                    @endif
                                </div>
                                @if($dossier->devis)
                                    <div class="p-3 bg-light rounded-4 mb-3 d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="small text-muted">Total TTC</div>
                                            <div class="h4 fw-bold mb-0">
                                                {{ number_format($dossier->devis->montant_total, 3, '.', ' ') }} DT</div>
                                        </div>
                                        <div class="text-end">
                                            <span
                                                class="badge rounded-pill px-3 bg-{{ $dossier->devis->statut == 'ACCEPTE' ? 'success' : ($dossier->devis->statut == 'REFUSE' ? 'danger' : 'warning') }}">{{ $dossier->devis->statut }}</span>
                                            @if($dossier->devis->statut == 'REFUSE' && $dossier->commentaire_refus)
                                                <div class="mt-1 small text-danger italic" style="font-size: 0.65rem;">Motif:
                                                    {{ $dossier->commentaire_refus }}</div>
                                            @endif
                                        </div>
                                    </div>

                                    @if($dossier->devis->statut === 'EN_ATTENTE' && in_array(auth()->user()->role, ['Admin', 'Agent']))
                                        <div class="d-flex gap-2 mt-3">
                                            <form action="{{ route('devis.accepter', $dossier->devis->id) }}" method="POST"
                                                class="flex-grow-1">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm w-100 rounded-pill fw-bold"
                                                    onclick="return confirm('Confirmer l\'acceptation du devis ?')">
                                                    <i class="fas fa-check me-1"></i> ACCEPTER
                                                </button>
                                            </form>
                                            <button type="button"
                                                class="btn btn-danger btn-sm flex-grow-1 rounded-pill fw-bold shadow-sm"
                                                data-bs-toggle="modal" data-bs-target="#refusDevisModal">
                                                <i class="fas fa-times me-1"></i> REFUSER
                                            </button>
                                        </div>
                                        <div class="text-center mt-2">
                                            <small class="text-muted italic" style="font-size: 0.7rem;">Enregistrez la décision du
                                                client après contact.</small>
                                        </div>
                                    @endif
                                @elseif(in_array(auth()->user()->role, ['Admin', 'Agent']) && $dossier->statut === 'EN_ATTENTE_DEVIS')
                                    <div class="text-center py-4">
                                        <p class="text-muted small mb-3">Prêt pour établissement du devis.</p>
                                        <a href="{{ route('devis.create', $dossier->id) }}"
                                            class="btn btn-primary rounded-pill px-4 fw-bold">
                                            <i class="fas fa-plus me-1"></i> Établir le Devis
                                        </a>
                                    </div>
                                @else
                                    <div class="text-center py-4 text-muted small">Aucun devis généré pour ce dossier.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card shadow-sm border-0 mb-4" style="border-radius: 20px;">
                            <div class="card-body p-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="fw-bold text-success mb-0">Facturation</h6>
                                    @if($dossier->facture)
                                        <a href="{{ route('factures.pdf', $dossier->facture->id) }}" target="_blank"
                                            class="btn btn-outline-success btn-sm rounded-pill px-3 fw-bold"
                                            style="font-size: 0.75rem;">
                                            <i class="fas fa-file-pdf me-1"></i> PDF
                                        </a>
                                    @endif
                                </div>
                                @if($dossier->facture)
                                    <div class="p-3 bg-light rounded-4 mb-3 d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="small text-muted">Montant Net TTC</div>
                                            <div class="h4 fw-bold mb-0">
                                                {{ number_format($dossier->facture->montant_total, 3, '.', ' ') }} DT</div>
                                        </div>
                                        <span class="badge rounded-pill px-3 bg-success">PAYÉ / FACTURÉ</span>
                                    </div>
                                @elseif(in_array(auth()->user()->role, ['Admin', 'Agent']) && $dossier->statut === 'REPARE')
                                    <div class="text-center py-4">
                                        <p class="text-muted small mb-3">Réparation terminée. Vous pouvez facturer le client.
                                        </p>
                                        <a href="{{ route('factures.create', $dossier->id) }}"
                                            class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                                            <i class="fas fa-file-invoice me-1"></i> Générer la Facture
                                        </a>
                                    </div>
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
                <div class="row g-3">
                    <div class="col-lg-8">
                        <div class="card shadow-sm border-0" style="border-radius: 15px;">
                            <div class="card-body p-3">
                                <h6 class="fw-bold text-primary mb-3" style="font-size: 0.85rem;"><i
                                        class="fas fa-comments me-2"></i> Flux de communication</h6>

                                <div id="section-messages" class="chat-box mb-3 p-2 bg-light rounded-3"
                                    style="height: 300px; overflow-y: auto; border: 1px solid #edf2f7;">
                                    @forelse($dossier->messages->sortBy('created_at') as $msg)
                                        @php
                                            $isInternal = str_starts_with($msg->message, '[INT] ');
                                            $cleanMessage = $isInternal ? substr($msg->message, 6) : $msg->message;
                                            $isMe = $msg->user_id == auth()->id();
                                        @endphp
                                        <div class="mb-3 d-flex {{ $isMe ? 'justify-content-end' : 'justify-content-start' }}">
                                            <div class="message-bubble p-3 rounded-4 shadow-sm {{ $isInternal ? 'bg-warning bg-opacity-10 border border-warning border-opacity-25' : ($isMe ? 'bg-primary text-white' : 'bg-white text-dark') }}"
                                                style="max-width: 80%; min-width: 150px;">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="fw-bold" style="font-size: 0.75rem;">
                                                        {{ $msg->user->name }}
                                                        @if($isInternal)
                                                            <span class="badge bg-warning text-dark ms-2"
                                                                style="font-size: 0.55rem;"><i class="fas fa-lock me-1"></i>
                                                                INTERNE</span>
                                                        @endif
                                                    </span>
                                                    <span class="opacity-50 ms-3"
                                                        style="font-size: 0.6rem;">{{ $msg->created_at ? $msg->created_at->format('d/m H:i') : '' }}</span>
                                                </div>
                                                <div style="font-size: 0.85rem; line-height: 1.4;">{{ $cleanMessage }}</div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-5 text-muted small">
                                            <i class="fas fa-comments fa-3x mb-3 opacity-25"></i>
                                            <p>Aucun message pour le moment.</p>
                                        </div>
                                    @endforelse
                                </div>

                                {{-- Formulaire d'envoi --}}
                                <form action="{{ route('dossiers.messages.store', $dossier->id) }}#section-messages"
                                    method="POST">
                                    @csrf
                                    <div class="bg-white border rounded-4 p-3 shadow-sm">
                                        <textarea name="message" class="form-control border-0 bg-transparent mb-2" rows="3"
                                            placeholder="Tapez votre message ici..." required
                                            style="box-shadow: none; resize: none;"></textarea>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="d-flex gap-3">
                                                @if(auth()->user()->role !== 'Client')
                                                    <div class="form-check form-check-inline m-0">
                                                        <input class="form-check-input" type="radio" name="type" id="typePublic"
                                                            value="public" checked>
                                                        <label class="form-check-label small fw-bold text-muted"
                                                            for="typePublic">Communication Client</label>
                                                    </div>
                                                    <div class="form-check form-check-inline m-0">
                                                        <input class="form-check-input" type="radio" name="type"
                                                            id="typeInternal" value="internal">
                                                        <label class="form-check-label small fw-bold text-warning"
                                                            for="typeInternal">Note Interne <i
                                                                class="fas fa-lock small"></i></label>
                                                    </div>
                                                @endif
                                            </div>
                                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                                                Envoyer <i class="fas fa-paper-plane ms-2"></i>
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 bg-light">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-dark mb-3">Guide de communication</h6>
                                <ul class="small text-muted ps-3 mb-0">
                                    <li class="mb-2"><strong>Communication Client</strong> : Visible par le client sur son
                                        interface de suivi. Utilisez ce mode pour les demandes d'informations ou mises à
                                        jour.</li>
                                    <li><strong>Note Interne</strong> : Visible uniquement par l'équipe administrative et
                                        les techniciens. Utilisez ce mode pour les détails techniques ou observations
                                        d'atelier.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ONGLET HISTORIQUE --}}
            <div class="tab-pane fade" id="historique">
                <div class="card shadow-sm border-0" style="border-radius: 20px;">
                    <div class="card-header bg-white py-3 border-0">
                        <h6 class="m-0 fw-bold text-dark"><i class="far fa-clock text-primary me-2"></i> Journal des
                            évènements</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="timeline-v2">
                            @foreach($dossier->suivi()->latest()->get() as $log)
                                <div class="timeline-item mb-4 pb-2 position-relative ps-4 border-start border-light"
                                    style="border-width: 2px !important;">
                                    <div class="timeline-marker position-absolute bg-primary rounded-circle"
                                        style="width: 10px; height: 10px; left: -6px; top: 5px;"></div>
                                    <div class="timeline-content">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <h6 class="fw-bold text-primary mb-0" style="font-size: 0.8rem;">
                                                {{ str_replace('_', ' ', $log->nouveau_statut) }}</h6>
                                            <small class="text-muted"
                                                style="font-size: 0.65rem;">{{ $log->created_at ? $log->created_at->format('d/m/Y H:i') : '' }}</small>
                                        </div>
                                        <div class="p-2 rounded bg-light mb-1 small text-dark">{{ $log->commentaire }}</div>
                                        <div class="small text-muted" style="font-size: 0.65rem;"><i
                                                class="fas fa-user-circle me-1"></i> {{ $log->user->name ?? 'Système' }}</div>
                                    </div>
                                </div>
                            @endforeach
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
                        <p class="text-muted small">Expliquez pourquoi vous ne pouvez pas traiter ce dossier (pièce
                            indisponible à long terme, expertise manquante, etc.).</p>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">Motif du
                                retrait</label>
                            <textarea name="raison" class="form-control bg-light border-0" rows="4"
                                placeholder="Saisissez votre motif ici..." required style="border-radius: 10px;"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                            data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">ENVOYER LA DEMANDE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Refus Devis --}}
    <div class="modal fade" id="refusDevisModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 15px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-danger">Motif du refus de devis</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('devis.refuser', $dossier->devis->id ?? 0) }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <p class="text-muted small">Veuillez indiquer la raison pour laquelle le client a refusé ce devis.
                            Cela nous aidera à mieux comprendre ses attentes.</p>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase"
                                style="color: #64748b; font-size: 0.65rem;">Commentaire / Motif</label>
                            <textarea name="commentaire_refus" class="form-control bg-light border-0" rows="4"
                                placeholder="Ex: Prix trop élevé, Client préfère acheter un neuf..." required
                                style="border-radius: 10px; font-size: 0.85rem;"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0 px-4 pb-4">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                            data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">Confirmer le refus</button>
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
                        <p class="text-muted small">Vous allez marquer ce dossier comme <strong>IRRÉPARABLE</strong> car les
                            pièces nécessaires ne sont plus disponibles sur le marché.</p>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">Commentaire
                                administratif</label>
                            <textarea name="commentaire" class="form-control bg-light border-0" rows="3"
                                placeholder="Ex: Pièce obsolète, fournisseur en rupture définitive..." required
                                style="border-radius: 10px;"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                            data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">CONFIRMER COMME
                            IRRÉPARABLE</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Modal Refus Remplacement (Admin) --}}
    <div class="modal fade" id="refusRemplacementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 15px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-danger">Refus de remplacement</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('dossiers.refuserRemplacement', $dossier->id) }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <p class="text-muted small">Veuillez obligatoirement justifier le refus de remplacement pour ce
                            dossier sous garantie.</p>
                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">Motif du
                                refus</label>
                            <textarea name="raison" class="form-control bg-light border-0" rows="3"
                                placeholder="Ex: Mauvaise utilisation non détectée, trace de choc interne..." required
                                style="border-radius: 10px;"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                            data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">CONFIRMER LE REFUS</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Modal Préparer Remplacement (Agent) --}}
    <div class="modal fade" id="preparerRemplacementModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 15px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-success"><i class="fas fa-box-open me-2"></i> Nouvel Appareil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('dossiers.storeRemplacement', $dossier->id) }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <p class="text-muted small">Saisissez les informations de l'unité de remplacement remise au client.
                        </p>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">Nouvel IMEI <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="imei_remplacement" class="form-control bg-light border-0"
                                placeholder="Ex: 356938035643809" required style="border-radius: 10px;">
                        </div>

                        <div class="mb-0">
                            <label class="form-label fw-bold small text-uppercase" style="color: #64748b;">Modèle (si
                                différent)</label>
                            <input type="text" name="modele_remplacement" class="form-control bg-light border-0"
                                placeholder="Ex: {{ $dossier->appareil->modele }}" value="{{ $dossier->appareil->modele }}"
                                style="border-radius: 10px;">
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold"
                            data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">ENREGISTRER LE
                            REMPLACEMENT</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Activer l'onglet selon le paramètre ?tab= dans l'URL
        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab');
            if (tab) {
                const tabBtn = document.querySelector('[data-bs-target="#' + tab + '"]');
                if (tabBtn) {
                    tabBtn.click();
                    // Scroll vers la zone de messages si on revient de l'envoi
                    if (tab === 'communication') {
                        setTimeout(() => {
                            const msgBox = document.getElementById('section-messages');
                            if (msgBox) {
                                msgBox.scrollTop = msgBox.scrollHeight;
                            }
                        }, 300);
                    }
                }
            }
        });
    </script>
@endpush