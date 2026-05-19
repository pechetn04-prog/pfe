@extends('layouts.app')

@section('title', 'Suivi Dossier #' . $dossier->num_dossier)

@section('content')
    <div class="container-fluid px-4 py-5 ticket-wrapper">
        <div class="row">
            <div class="col-12">

                {{-- En-tête avec navigation --}}
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="d-flex align-items-center">
                        <a href="{{ route('client.dashboard') }}"
                            class="btn btn-white border shadow-sm rounded-pill p-2 d-flex align-items-center justify-content-center me-3 btn-back-ticket">
                            <i class="fas fa-arrow-left text-primary"></i>
                        </a>
                        <div>
                            <h1 class="h3 fw-bold mb-0 text-dark">Suivi de réparation</h1>
                            <span
                                class="badge bg-soft-primary text-primary px-3 py-1 rounded-pill small">#{{ $dossier->num_dossier }}</span>
                        </div>
                    </div>
                    <div class="text-end d-none d-md-block">
                        <div class="text-muted small mb-1">Reçu le</div>
                        <div class="fw-bold text-dark">
                            {{ \Carbon\Carbon::parse($dossier->date_reception)->format('d/m/Y') }}</div>
                    </div>
                </div>

                {{-- Action Suivante Recommandée (Bannière Premium) --}}
                @if($dossier->devis && $dossier->devis->statut === 'EN_ATTENTE')
                    <div class="card border-0 shadow-lg mb-5 overflow-hidden card-action-banner">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-lg-5 mb-3 mb-lg-0">
                                    <div class="d-flex align-items-center gap-3">
                                        <div
                                            class="bg-white bg-opacity-25 rounded-3 p-2 d-flex align-items-center justify-content-center action-icon-box">
                                            <i class="fas fa-list-ul fs-5"></i>
                                        </div>
                                        <div>
                                            <h5 class="fw-bold mb-1 text-white">Action suivante recommandée</h5>
                                            <p class="mb-0 text-white opacity-75 small">Un devis de
                                                {{ number_format($dossier->devis->montant_total, 3, ',', ' ') }} DT is prêt.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-7">
                                    <div class="d-flex flex-wrap justify-content-lg-end align-items-center gap-3">
                                        {{-- Voir PDF --}}
                                        <a href="{{ route('devis.pdf', $dossier->devis->id) }}"
                                            class="text-white text-decoration-none small fw-bold me-3" target="_blank">
                                            <i class="fas fa-file-pdf me-1"></i> Voir le détail
                                        </a>

                                        {{-- Bouton Accepter --}}
                                        <form action="{{ route('client.devis.accepter', $dossier->id) }}" method="POST"
                                            class="m-0">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-success rounded-pill fw-bold px-4 py-2 shadow-sm border-2 border-white"
                                                onclick="return confirm('Voulez-vous accepter ce devis et lancer la réparation ?')">
                                                Accepter le Devis
                                            </button>
                                        </form>

                                        {{-- Bouton Refuser --}}
                                        <form action="{{ route('client.devis.refuser', $dossier->id) }}" method="POST"
                                            class="m-0">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-danger rounded-pill fw-bold px-4 py-2 shadow-sm border-2 border-white"
                                                onclick="return confirm('Voulez-vous vraiment refuser ce devis ?')">
                                                Refuser le Devis
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row g-4">

                    {{-- Panneau GAUCHE (7/12) : Détails et Journal --}}
                    <div class="col-lg-7 order-2 order-lg-1">

                        {{-- Carte Infos Appareil --}}
                        <div class="card border-0 shadow-sm mb-4 overflow-hidden card-info-appareil">
                            <div
                                class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold mb-0 text-dark">Informations Appareil</h5>
                                <i class="fas fa-mobile-alt text-muted fs-4"></i>
                            </div>
                            <div class="card-body p-4 pt-0">
                                <div class="row g-4">
                                    <div class="col-sm-4">
                                        <label class="small text-muted fw-bold text-uppercase mb-1 d-block">Modèle</label>
                                        <div class="fw-bold text-dark fs-5 text-truncate">
                                            {{ $dossier->appareil->modele ?? '—' }}</div>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="small text-muted fw-bold text-uppercase mb-1 d-block">IMEI</label>
                                        <div class="fw-bold text-dark fs-5 text-truncate">{{ $dossier->imei ?? '—' }}</div>
                                    </div>
                                    <div class="col-sm-4">
                                        <label class="small text-muted fw-bold text-uppercase mb-1 d-block">Garantie</label>
                                        <div class="mt-1">
                                            @if($dossier->sous_garantie)
                                                <span class="badge bg-soft-success text-success px-3 py-1 rounded-pill fw-bold">
                                                    <i class="fas fa-shield-alt me-1"></i> Sous Garantie
                                                </span>
                                            @else
                                                <span class="badge bg-soft-danger text-danger px-3 py-1 rounded-pill fw-bold">
                                                    <i class="fas fa-exclamation-circle me-1"></i> Hors Garantie
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="small text-muted fw-bold text-uppercase mb-1 d-block">Symptôme(s)
                                            déclaré(s)</label>
                                        <div class="text-dark bg-light p-3 rounded-4 bg-symptome-text">
                                            {{ $dossier->panne_declaree }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Carte Communication / Messages --}}
                        <div class="card border-0 shadow-sm mb-4 card-chat-sav">
                            <div
                                class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                                <h5 class="fw-bold mb-0 text-dark">Communication SAV</h5>
                                <i class="fas fa-comments text-primary fs-4"></i>
                            </div>
                            <div class="card-body p-4 pt-0">
                                <div class="chat-box mb-4 p-3 bg-light rounded-4 chat-box-scroll">
                                    @php
                                        $publicMessages = $dossier->messages->filter(function ($m) {
                                            return !str_starts_with($m->message, '[INT] ');
                                        })->sortBy('created_at');
                                    @endphp

                                    @forelse($publicMessages as $msg)
                                        @php $isMe = $msg->user_id == auth()->id(); @endphp
                                        <div class="mb-3 d-flex {{ $isMe ? 'justify-content-end' : 'justify-content-start' }}">
                                            <div
                                                class="p-3 rounded-4 {{ $isMe ? 'bg-primary text-white shadow-sm' : 'bg-white text-dark shadow-sm border' }} msg-box-max">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span class="fw-bold small me-3">{{ $msg->user->name }}</span>
                                                    <span
                                                        class="opacity-50 msg-micro-time">{{ $msg->created_at->format('H:i') }}</span>
                                                </div>
                                                <div class="msg-text-body">{{ $msg->message }}</div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-4 text-muted small">Aucun message échangé.</div>
                                    @endforelse
                                </div>

                                @if($dossier->statut === 'CLOTURE')
                                    <div class="alert alert-secondary border-0 rounded-pill p-3 text-center mb-0 small fw-bold">
                                        <i class="fas fa-lock me-2 text-secondary"></i> Ce dossier est clôturé. L'espace de
                                        discussion est fermé.
                                    </div>
                                @else
                                    <form action="{{ route('dossiers.messages.store', $dossier->id) }}" method="POST">
                                        @csrf
                                        <div class="input-group bg-light rounded-pill p-1 shadow-sm">
                                            <input type="text" name="message"
                                                class="form-control border-0 bg-transparent px-3 chat-input-field"
                                                placeholder="Votre message au SAV..." required>
                                            <button class="btn btn-primary rounded-pill px-4 fw-bold"
                                                type="submit">Envoyer</button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </div>

                        {{-- Journal de Suivi (Timeline stylée) --}}
                        <div class="card border-0 shadow-sm card-timeline-history">
                            <div class="card-header bg-white border-0 py-4 px-4">
                                <h5 class="fw-bold mb-0 text-dark">Historique des étapes</h5>
                            </div>
                            <div class="card-body p-4 pt-0">
                                <div class="custom-timeline">
                                    @forelse($dossier->suivi->sortByDesc('created_at') as $suivi)
                                        <div class="timeline-item">
                                            <div class="timeline-marker"></div>
                                            <div class="timeline-content pb-4">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <span
                                                        class="small fw-bold text-primary">{{ \Carbon\Carbon::parse($suivi->created_at)->diffForHumans() }}</span>
                                                    <span
                                                        class="text-muted timeline-micro-time">{{ \Carbon\Carbon::parse($suivi->created_at)->format('H:i') }}</span>
                                                </div>
                                                <div class="text-dark small">{{ $suivi->commentaire }}</div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="text-center py-4 text-muted italic">Aucun historique disponible pour le
                                            moment.</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>


                    </div>

                    {{-- Panneau DROIT (5/12) : État --}}
                    <div class="col-lg-5 order-1 order-lg-2">

                        {{-- Carte d'État Héro --}}
                        <div class="card border-0 shadow-lg p-5 mb-4 overflow-hidden card-status-hero">
                            <div class="mb-4 position-relative">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mb-3 shadow-sm status-icon-circle"
                                    style="background-color: {{ $statusConfig['color'] }}15;">
                                    <i class="fas {{ $statusConfig['icon'] }} fs-2"
                                        style="color: {{ $statusConfig['color'] }};"></i>
                                </div>
                            </div>

                            <div class="small text-muted text-uppercase fw-bold mb-1">Statut actuel</div>
                            <h2 class="fw-bold mb-2" style="color: {{ $statusConfig['color'] }};">
                                {{ $statusConfig['label'] }}</h2>
                            <p class="text-muted mb-0">{{ $statusConfig['desc'] }}</p>
                        </div>

                        {{-- Section Documents PDF --}}
                        <div class="card border-0 shadow-sm overflow-hidden card-docs-pdf">
                            <div class="card-header bg-white border-0 py-4 px-4 d-flex align-items-center">
                                <i class="fas fa-folder-open text-primary me-2 fs-5"></i>
                                <h5 class="fw-bold mb-0 text-dark">Documents PDF</h5>
                            </div>
                            <div class="card-body p-4 pt-0">
                                <div class="d-grid gap-3">

                                    {{-- Bon de Réception --}}
                                    <a href="{{ route('dossiers.reception.pdf', $dossier->id) }}"
                                        class="btn btn-white border d-flex align-items-center justify-content-between p-3 rounded-4 transition-all"
                                        target="_blank">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-soft-primary text-primary rounded-3 p-2 me-3">
                                                <i class="fas fa-file-invoice"></i>
                                            </div>
                                            <div class="text-start">
                                                <div class="fw-bold text-dark small">Bon de Réception</div>
                                                <div class="text-muted doc-sub-text">Document de prise en charge</div>
                                            </div>
                                        </div>
                                        <i class="fas fa-download text-muted small"></i>
                                    </a>

                                    {{-- Rapport de Diagnostic --}}
                                    @if(in_array($dossier->statut, ['EN_ATTENTE_DEVIS', 'EN_REPARATION', 'REPARE', 'LIVRE', 'ATTENTE_PIECE', 'IRREPARABLE']))
                                        <a href="{{ route('dossiers.diagnostic.pdf', $dossier->id) }}"
                                            class="btn btn-white border d-flex align-items-center justify-content-between p-3 rounded-4 transition-all"
                                            target="_blank">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-soft-warning text-warning rounded-3 p-2 me-3 diagnostic-pdf-box">
                                                    <i class="fas fa-microscope"></i>
                                                </div>
                                                <div class="text-start">
                                                    <div class="fw-bold text-dark small">Rapport Diagnostic</div>
                                                    <div class="text-muted doc-sub-text">Détails de la panne</div>
                                                </div>
                                            </div>
                                            <i class="fas fa-download text-muted small"></i>
                                        </a>
                                    @endif

                                    {{-- Devis --}}
                                    @if($dossier->devis)
                                        <a href="{{ route('devis.pdf', $dossier->devis->id) }}"
                                            class="btn btn-white border d-flex align-items-center justify-content-between p-3 rounded-4 transition-all"
                                            target="_blank">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-soft-success text-success rounded-3 p-2 me-3">
                                                    <i class="fas fa-file-invoice-dollar"></i>
                                                </div>
                                                <div class="text-start">
                                                    <div class="fw-bold text-dark small">Devis de Réparation</div>
                                                    <div class="text-muted doc-sub-text">Offre tarifaire</div>
                                                </div>
                                            </div>
                                            <i class="fas fa-download text-muted small"></i>
                                        </a>
                                    @endif

                                    {{-- Facture --}}
                                    @if($dossier->facture)
                                        <a href="{{ route('factures.pdf', $dossier->facture->id) }}"
                                            class="btn btn-white border d-flex align-items-center justify-content-between p-3 rounded-4 transition-all"
                                            target="_blank">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-soft-danger text-danger rounded-3 p-2 me-3">
                                                    <i class="fas fa-file-pdf"></i>
                                                </div>
                                                <div class="text-start">
                                                    <div class="fw-bold text-dark small">Facture Finale</div>
                                                    <div class="text-muted doc-sub-text">Justificatif de paiement</div>
                                                </div>
                                            </div>
                                            <i class="fas fa-download text-muted small"></i>
                                        </a>
                                    @endif

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection