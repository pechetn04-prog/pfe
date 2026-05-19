@extends('layouts.app')

@section('title', "Suivi Dossier #{$dossier->num_dossier}")

@section('content')
    <div class="main-container">

        <div class="d-flex align-items-center justify-content-between mb-4">
            <a href="{{ route('client.suivi') }}"
                class="btn btn-white shadow-sm border rounded-pill px-4 py-2 fw-bold text-muted text-decoration-none bg-white">
                <i class="fas fa-arrow-left me-2"></i> Retour
            </a>
            <div class="text-end">
                <span class="small text-muted d-block mb-1">Dossier de réparation</span>
                <span
                    class="badge bg-soft-primary text-primary px-3 py-2 rounded-pill fw-bold border">#{{ $dossier->num_dossier }}</span>
            </div>
        </div>

        <div class="row g-4">
            {{-- GAUCHE : Statut Actuel et Infos --}}
            <div class="col-lg-5">
                <div class="premium-card">
                    <div class="status-hero">
                        <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3"
                            style="width: 70px; height: 70px; background-color: {{ $statusConfig['color'] }}15;">
                            <i class="fas {{ $statusConfig['icon'] }} fs-3" style="color: {{ $statusConfig['color'] }};"></i>
                        </div>
                        <h2 class="fw-800 mb-1" style="color: {{ $statusConfig['color'] }}; font-size: 22px;">
                            {{ $statusConfig['label'] }}</h2>
                        <p class="text-muted small mb-0">{{ $statusConfig['desc'] }}</p>
                    </div>

                    <div class="info-list">
                        <h6 class="section-title mt-4" style="font-size: 15px;">
                            <i class="fas fa-info-circle text-muted"></i>
                            Détails de l'appareil
                        </h6>
                        <div class="info-row">
                            <span class="info-label">Modèle</span>
                            <span class="info-value">{{ $dossier->appareil->modele ?? '—' }}</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">IMEI (Masqué)</span>
                            <span class="info-value">{{ substr($dossier->imei, 0, 6) }}***</span>
                        </div>
                        <div class="info-row">
                            <span class="info-label">Garantie</span>
                            @if($dossier->garantie_annulee)
                                <span class="info-value text-warning">Garantie Exclue</span>
                            @elseif($dossier->sous_garantie)
                                <span class="info-value text-success">Sous Garantie</span>
                            @else
                                <span class="info-value text-danger">Hors Garantie</span>
                            @endif
                        </div>
                        <div class="info-row">
                            <span class="info-label">Date Dépôt</span>
                            <span class="info-value">{{ $dossier->date_reception->format('d/m/Y') }}</span>
                        </div>
                    </div>

                    @if($dossier->statut === 'EN_ATTENTE_DEVIS')
                        <div class="mt-4 p-3 bg-soft-primary rounded-4 border-start border-primary border-4">
                            <p class="small fw-bold text-primary mb-1">Un devis vous attend !</p>
                            <p class="extra-small text-muted mb-3" style="font-size: 11px;">Connectez-vous pour valider le
                                devis.</p>
                            <a href="{{ route('login') }}" class="btn btn-primary w-100 rounded-pill fw-bold">Espace
                                Client</a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- DROITE : Historique de suivi --}}
            <div class="col-lg-7">
                <div class="premium-card">
                    <h5 class="section-title">
                        <i class="fas fa-history text-primary"></i>
                        Historique de suivi
                    </h5>
                    <div class="timeline-container">
                        @forelse($dossier->suivi->sortByDesc('created_at') as $suivi)
                            <div class="timeline-item">
                                <div class="timeline-marker"></div>
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="text-muted extra-small"
                                        style="font-size: 11px;">{{ $suivi->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="text-dark small lh-base">{{ $suivi->commentaire }}</div>
                            </div>
                        @empty
                            <div class="text-center py-5 text-muted">Aucun historique disponible.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection