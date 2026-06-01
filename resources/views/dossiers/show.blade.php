@extends('layouts.app')
@section('title', 'Gestion détaillée du dossier #' . $dossier->num_dossier)


@section('content')
    <div class="container-fluid px-4 py-3">
        {{-- Header / Breadcrumb --}}
        <div class="d-flex align-items-center mb-1">
            <a href="{{ route('dossiers.index') }}"
                class="btn btn-sm bg-white shadow-sm me-3 border-0 d-flex align-items-center justify-content-center btn-back-dossier">
                <i class="fas fa-arrow-left"></i>
            </a>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 font-size-075 fw-medium">
                    <li class="breadcrumb-item"><a href="{{ route('dossiers.index') }}"
                            class="text-primary text-decoration-none">Tickets</a></li>
                    <li class="breadcrumb-item active text-muted" aria-current="page">Dossier #{{ $dossier->num_dossier }}
                    </li>
                </ol>
            </nav>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h5 fw-bold mb-0 text-dark">Gestion détaillée du dossier</h1>
            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('dossiers.reception.pdf', $dossier->id) }}" target="_blank" class="btn btn-light btn-sm rounded-pill px-3 fw-bold text-dark border shadow-sm">
                    <i class="fas fa-file-invoice me-1"></i> Bon de réception
                </a>
                <a href="{{ route('dossiers.etiquette', $dossier->id) }}" target="_blank" class="btn btn-dark btn-sm rounded-pill px-3 fw-bold shadow-sm">
                    <i class="fas fa-tag me-1"></i> Étiquette
                </a>


                
            </div>
        </div>


        {{-- Info Card --}}
        <div class="card border-0 shadow-sm mb-3 card-dossier-box-15">
            <div class="card-body p-3 d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3 d-flex align-items-center justify-content-center icon-circle-wrapper-50">
                    <div class="bg-primary rounded-2 p-2 d-flex align-items-center justify-content-center shadow-sm icon-circle-wrapper-35">
                        <i class="fas fa-folder-open fa-sm text-white"></i>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <div class="small text-muted text-uppercase fw-bold mb-1 font-size-06 letter-spacing-08 opacity-06">RÉFÉRENCE DOSSIER</div>
                    <h4 class="mb-0 fw-bold text-dark">#{{ $dossier->num_dossier }}
                    </h4>
                </div>
                <div class="text-end">
                    <div class="small text-muted text-uppercase fw-bold mb-2 font-size-06 letter-spacing-08 opacity-06">STATUT ACTUEL</div>
                    <span class="status-badge-capsule {{ $dossier->statut_class }} text-uppercase">
                        {{ $dossier->statut_text }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Recommendation Banner --}}

        <div class="card border-0 text-white mb-3 shadow-sm action-banner-card"
            style="background-color: {{ $action['color'] }};">
            <div class="card-body p-3 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="me-3 fs-4">
                        <i class="fas {{ $action['icon'] }}"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-bold">{{ $action['title'] }}</h6>
                        <p class="mb-0 action-banner-text">{{ $action['desc'] }}</p>
                    </div>
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
                                    <i class="fas {{ $dossier->statut == 'DEVIS_REFUSE' ? 'fa-undo' : 'fa-hand-holding-heart' }} me-1"></i>
                                    {{ $dossier->statut == 'DEVIS_REFUSE' ? 'Restituer' : 'Restituer Appareil' }}
                                </button>
                            </form>
                        @endif

                        {{-- Attente Pièce --}}
                        @if($dossier->statut === 'ATTENTE_PIECE' && auth()->user()->role === 'Admin')
                            <form action="{{ route('dossiers.marquerPieceRecue', $dossier->id) }}" method="POST" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-light text-primary btn-sm rounded-pill px-3 fw-bold shadow-sm">
                                    <i class="fas fa-box me-1"></i> Marquer pièce reçue
                                </button>
                            </form>
                            <button type="button" class="btn btn-light text-danger btn-sm rounded-pill px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalImpossibleReappro">
                                <i class="fas fa-times-circle me-1"></i> Rupture définitive
                            </button>
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
                        <div class="card shadow-sm border-0 h-100 card-dossier-box-15 bg-white-important">
                            <div class="card-body p-3">
                                <h6 class="fw-bold mb-3 d-flex align-items-center info-section-title">
                                    <i class="fas fa-id-card me-2 text-primary"></i> Information client et appareil
                                </h6>
                                <div class="row g-3">
                                    <div class="col-12">
                                        <div class="p-3 rounded-3 info-block-wrapper">
                                            <label class="small text-muted text-uppercase fw-bold mb-1 d-block info-block-label">NOM CLIENT</label>
                                            <div class="fw-bolder info-block-value-large">{{ $dossier->client->name ?? '—' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-3 info-block-wrapper">
                                            <label class="small text-muted text-uppercase fw-bold mb-1 d-block info-block-label">ADRESSE EMAIL</label>
                                            <div class="fw-bold info-block-value-email">{{ $dossier->client->email ?? '—' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-3 info-block-wrapper">
                                            <label class="small text-muted text-uppercase fw-bold mb-1 d-block info-block-label">NUMÉRO DE TÉLÉPHONE</label>
                                            <div class="fw-bold info-block-value-standard">{{ $dossier->client->telephone ?? '—' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="p-3 rounded-3 info-block-wrapper">
                                            <label class="small text-muted text-uppercase fw-bold mb-1 d-block info-block-label">ARTICLE / MODÈLE</label>
                                            <div class="fw-bold info-block-value-standard">{{ $dossier->appareil->modele ?? '—' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="p-3 rounded-3 info-block-wrapper">
                                            <label class="small text-muted text-uppercase fw-bold mb-1 d-block info-block-label">RÉFÉRENCE PRODUIT</label>
                                            <div class="fw-bold info-block-value-standard">{{ $dossier->appareil->reference_produit ?? '—' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="p-3 rounded-3 info-block-wrapper">
                                            <label class="small text-muted text-uppercase fw-bold mb-1 d-block info-block-label">NUMÉRO IMEI / SÉRIE</label>
                                            <div class="fw-bold info-block-value-imei">{{ $dossier->imei }}</div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="p-3 rounded-3 info-block-wrapper">
                                            <label class="small text-muted text-uppercase fw-bold mb-1 d-block info-block-label">STATUT GARANTIE</label>
                                            <div class="mt-1">
                                                @if($dossier->garantie_annulee)
                                                    <span class="badge rounded-pill bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-2 fw-bold font-size-075">GARANTIE EXCLUE</span>
                                                @elseif($dossier->sous_garantie)
                                                    <span class="badge rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-2 fw-bold font-size-075">SOUS GARANTIE</span>
                                                @else
                                                    <span class="badge rounded-pill bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 fw-bold font-size-075">HORS GARANTIE</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    @if($dossier->imei_remplacement)
                                        <div class="col-12 mt-2">
                                            <div class="p-3 rounded-3 info-block-replacement">
                                                <label class="small text-success text-uppercase fw-bold mb-1 d-block info-block-label">APPAREIL DE REMPLACEMENT</label>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <div class="small text-muted font-size-07">Modèle</div>
                                                        <div class="fw-bold text-success-dark font-size-09">{{ $dossier->modele_remplacement }}</div>
                                                    </div>
                                                    <div class="text-end">
                                                        <div class="small text-muted font-size-07">Nouvel IMEI</div>
                                                        <div class="fw-bold text-success font-size-09">{{ $dossier->imei_remplacement }}</div>
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
                        <div class="card shadow-sm border-0 h-100 card-dossier-box-15 bg-white-important">
                            <div class="card-body p-3">
                                <h6 class="fw-bold mb-3 d-flex align-items-center info-section-title">
                                    <i class="fas fa-clipboard-list me-2 text-primary"></i> Panne et Accessoires
                                </h6>

                                <div class="mb-4">
                                    <label class="small text-muted text-uppercase fw-bold mb-1 d-block font-size-06 letter-spacing-05">PANNE DÉCLARÉE PAR LE CLIENT</label>
                                    <div class="fw-bold text-dark font-size-09">
                                        <span class="text-primary">{{ $dossier->type_panne ?? 'Logiciel & Système' }}</span>
                                        <span class="mx-1 text-muted">|</span>
                                        {{ $dossier->panne_declaree }}
                                    </div>
                                </div>

                        

                                <div>
                                    <label class="small text-muted text-uppercase fw-bold mb-2 d-block info-block-label">ACCESSOIRES DÉPOSÉS</label>
                                    <div class="p-3 rounded-3 info-block-wrapper">
                                        <span class="small fw-bold text-dark">{{ $dossier->accessoires_remis ?: 'Aucun accessoire' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3">
                        <div class="card shadow-sm border-0 mb-4 overflow-hidden card-dossier-box-15 bg-white-important">
                            <div class="p-2 px-3 d-flex align-items-center justify-content-between assignment-header">
                                <span class="fw-bold font-size-075">
                                    <i class="fas fa-user-friends me-1"></i> Affectation
                                </span>
                            </div>
                            <div class="card-body text-center py-4">

                                <h6 class="fw-bold mb-1 info-section-title font-size-095">
                                    {{ $dossier->technicien->name ?? 'Non assigné' }}
                                </h6>
                                <div class="text-primary fw-bold small mb-2">
                                    <i class="fas fa-phone-alt me-1"></i> {{ $dossier->technicien->telephone ?? 'Aucun numéro' }}
                                </div>
                                <p class="small text-muted mb-0 font-size-07">Technicien en charge du dossier</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ONGLET ATELIER --}}
            <div class="tab-pane fade" id="atelier">
                <div class="row g-3">
                    <div class="col-lg-6">
                        <div class="card shadow-sm border-0 mb-3 h-100 card-dossier-box-15">
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
                            <div class="card-body">Aucun message pour le moment.


                                @if($dossier->diagnostic)
                                    <div class="mb-4">
                                        <label class="small text-muted text-uppercase fw-bold mb-2 d-block"
                                            style="font-size: 0.6rem;">CONSTAT TECHNIQUE</label>
                                        <div class="p-3 bg-light rounded-3 border-start border-primary border-4">
                                            {{ $dossier->diagnostic->constat }}</div>
                                    </div>
                                    <div class="mb-4">
                                        <label class="small text-muted text-uppercase fw-bold mb-2 d-block"
                                            style="font-size: 0.6rem;">RECOMMANDATION</label>
                                        <div class="p-3 bg-light rounded-3">{{ $dossier->diagnostic->recommandation }}</div>
                                    </div>

                                    {{-- Photo de la Panne --}}
                                    @if($dossier->diagnostic->photo_panne)
                                        <div class="mt-3 pt-3 border-top">
                                            <label class="small text-muted text-uppercase fw-bold mb-2 d-block"
                                                style="font-size: 0.6rem; letter-spacing: 0.5px;">📸 PHOTO CONSTAT PANNE</label>
                                            <a href="{{ asset('storage/' . $dossier->diagnostic->photo_panne) }}" target="_blank" class="d-inline-block">
                                                <img src="{{ asset('storage/' . $dossier->diagnostic->photo_panne) }}" class="img-thumbnail shadow-sm hover-zoom" style="max-height: 180px; border-radius: 12px; cursor: pointer;">
                                            </a>
                                        </div>
                                    @endif

                                    {{-- Pièces demandées / requises --}}
                                    @if($dossier->diagnostic->pieces && $dossier->diagnostic->pieces->count())
                                        <div class="mt-4 pt-3 border-top">
                                            <label class="small text-muted text-uppercase fw-bold mb-2 d-block text-primary"
                                                style="font-size: 0.65rem;"><i class="fas fa-cubes me-1"></i> Pièces détachées demandées</label>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.8rem;">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Désignation</th>
                                                            <th class="text-center" style="width: 80px;">Quantité</th>
                                                            <th class="text-end" style="width: 120px;">Disponibilité</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($dossier->diagnostic->pieces as $piece)
                                                            <tr>
                                                                <td class="fw-semibold">{{ $piece->nom }}</td>
                                                                <td class="text-center">{{ $piece->pivot->quantite ?? 1 }}</td>
                                                                <td class="text-end">
                                                                    @if($piece->quantite >= ($piece->pivot->quantite ?? 1))
                                                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 fw-bold" style="font-size: 0.65rem;">En Stock ({{ $piece->quantite }})</span>
                                                                    @else
                                                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2 py-1 fw-bold" style="font-size: 0.65rem;">Rupture ({{ $piece->quantite }})</span>
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Prestations / Main d'œuvre --}}
                                    @if($dossier->diagnostic->tarifsMo && $dossier->diagnostic->tarifsMo->count())
                                        <div class="mt-3 pt-3 border-top">
                                            <label class="small text-muted text-uppercase fw-bold mb-2 d-block text-info"
                                                style="font-size: 0.65rem;"><i class="fas fa-tools me-1"></i> Prestations requises</label>
                                            <ul class="list-group list-group-flush mb-0" style="font-size: 0.8rem;">
                                                @foreach($dossier->diagnostic->tarifsMo as $mo)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 bg-transparent">
                                                        <span><i class="fas fa-check text-success me-2"></i>{{ $mo->type_intervention }}</span>
                                                        <span class="fw-bold text-dark">{{ number_format($mo->pivot->montant ?? $mo->montant, 3, '.', ' ') }} DT</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
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
                        <div class="card shadow-sm border-0 mb-3 h-100 card-dossier-box-15">
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

                                    {{-- Photo de l'Intervention --}}
                                    @if($dossier->intervention->photo_intervention)
                                        <div class="mt-3 pt-3 border-top">
                                            <label class="small text-muted text-uppercase fw-bold mb-2 d-block"
                                                style="font-size: 0.6rem; letter-spacing: 0.5px;">📸 PREUVE DE RÉPARATION (PHOTO)</label>
                                            <a href="{{ asset('storage/' . $dossier->intervention->photo_intervention) }}" target="_blank" class="d-inline-block">
                                                <img src="{{ asset('storage/' . $dossier->intervention->photo_intervention) }}" class="img-thumbnail shadow-sm hover-zoom" style="max-height: 180px; border-radius: 12px; cursor: pointer;">
                                            </a>
                                        </div>
                                    @endif

                                    {{-- Pièces consommées --}}
                                    @if($dossier->intervention->pieces && $dossier->intervention->pieces->count())
                                        <div class="mt-4 pt-3 border-top">
                                            <label class="small text-muted text-uppercase fw-bold mb-2 d-block text-success"
                                                style="font-size: 0.65rem;"><i class="fas fa-cubes me-1"></i> Pièces détachées consommées</label>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.8rem;">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>Désignation</th>
                                                            <th class="text-center" style="width: 80px;">Quantité</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($dossier->intervention->pieces as $piece)
                                                            <tr>
                                                                <td class="fw-semibold text-success">{{ $piece->nom }}</td>
                                                                <td class="text-center fw-bold">{{ $piece->pivot->quantite ?? 1 }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Prestations appliquées --}}
                                    @if($dossier->intervention->tarifsMo && $dossier->intervention->tarifsMo->count())
                                        <div class="mt-3 pt-3 border-top">
                                            <label class="small text-muted text-uppercase fw-bold mb-2 d-block text-info"
                                                style="font-size: 0.65rem;"><i class="fas fa-tools me-1"></i> Prestations appliquées</label>
                                            <ul class="list-group list-group-flush mb-0" style="font-size: 0.8rem;">
                                                @foreach($dossier->intervention->tarifsMo as $mo)
                                                    <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-2 bg-transparent">
                                                        <span><i class="fas fa-check text-success me-2"></i>{{ $mo->type_intervention }}</span>
                                                        <span class="fw-bold text-dark">{{ number_format($mo->pivot->montant ?? $mo->montant, 3, '.', ' ') }} DT</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
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
                        <div class="card shadow-sm border-0 mb-3 card-dossier-box-15">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold text-primary mb-0" style="font-size: 0.85rem;">Devis Estimatif</h6>
                                    @if($dossier->devis)
                                        <div class="d-flex gap-1">
                                            @if($dossier->devis->statut === 'EN_ATTENTE' && in_array(auth()->user()->role, ['Agent', 'Admin']))
                                                <a href="{{ route('devis.edit', $dossier->devis->id) }}"
                                                    class="btn btn-warning btn-sm text-white rounded-pill px-3 fw-bold shadow-sm"
                                                    style="font-size: 0.75rem;">
                                                    <i class="fas fa-edit me-1"></i> Modifier
                                                </a>
                                            @endif
                                            <a href="{{ route('devis.show', $dossier->devis->id) }}"
                                                class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold"
                                                style="font-size: 0.75rem;">
                                                <i class="fas fa-eye me-1"></i> Voir
                                            </a>
                                            <a href="{{ route('devis.pdf', $dossier->devis->id) }}" target="_blank"
                                                class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold"
                                                style="font-size: 0.75rem;">
                                                <i class="fas fa-file-pdf me-1"></i> PDF
                                            </a>
                                        </div>
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

                                    @if($dossier->devis->statut === 'EN_ATTENTE' && auth()->user()->role === 'Agent')
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
                                        <span class="badge rounded-pill px-3 bg-success">FACTURÉ</span>
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
                        <div class="card shadow-sm border-0 card-dossier-box-15">
                            <div class="card-body p-3">
                                <h6 class="fw-bold text-primary mb-3" style="font-size: 0.85rem;"><i
                                        class="fas fa-comments me-2"></i> Flux de communication</h6>

                                <div id="section-messages" class="chat-box mb-3 p-2 bg-light rounded-3"
                                    style="height: 300px; overflow-y: auto; border: 1px solid #edf2f7;">
                                    @forelse($messages as $msg)
                                        <div class="mb-3 d-flex {{ $msg->isMe ? 'justify-content-end' : 'justify-content-start' }}">
                                            <div class="message-bubble p-3 rounded-4 shadow-sm {{ $msg->isInternal ? 'bg-warning bg-opacity-10 border border-warning border-opacity-25' : ($msg->isMe ? 'bg-primary text-white' : 'bg-white text-dark') }}"
                                                style="max-width: 80%; min-width: 150px;">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="fw-bold" style="font-size: 0.75rem;">
                                                        {{ $msg->user->name }}
                                                        @if($msg->isInternal)
                                                            <span class="badge bg-warning text-dark ms-2"
                                                                style="font-size: 0.55rem;"><i class="fas fa-lock me-1"></i>
                                                                INTERNE</span>
                                                        @endif
                                                    </span>
                                                    <span class="opacity-50 ms-3"
                                                        style="font-size: 0.6rem;">{{ $msg->created_at ? $msg->created_at->format('d/m H:i') : '' }}</span>
                                                </div>
                                                <div style="font-size: 0.85rem; line-height: 1.4;">{{ $msg->cleanMessage }}</div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-5 text-muted small">
                                            <i class="fas fa-comments fa-3x mb-3 opacity-25"></i>
                                            <p>Aucun message pour le moment.</p>
                                        </div>
                                    @endforelse
                                </div>

                                @if($dossier->statut === 'CLOTURE')
                                    <div class="alert alert-secondary border-0 rounded-pill p-3 text-center mb-0 small fw-bold">
                                        <i class="fas fa-lock me-2 text-secondary"></i> Ce dossier est clôturé. L'espace de discussion est fermé.
                                    </div>
                                @else
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
                                @endif
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
                                    <li><strong>Note Interne</strong> : Visible uniquement par l'équipe administrative.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ONGLET HISTORIQUE --}}
            <div class="tab-pane fade" id="historique">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="card shadow-sm border-0" style="border-radius: 20px;">
                            <div class="card-header bg-white py-3 border-0">
                                <h6 class="m-0 fw-bold text-dark"><i class="far fa-clock text-primary me-2"></i> Journal des évènements</h6>
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
        </div>
    </div>


    {{-- Modal de demande de retrait --}}
    <div class="modal fade" id="rejetModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow modal-content-box-15">
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
            <div class="modal-content border-0 shadow modal-content-box-15">
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
            <div class="modal-content border-0 shadow modal-content-box-15">
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
            <div class="modal-content border-0 shadow modal-content-box-15">
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
            <div class="modal-content border-0 shadow modal-content-box-15">
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
    <script src="{{ asset('js/dossier_show.js') }}"></script>
@endpush