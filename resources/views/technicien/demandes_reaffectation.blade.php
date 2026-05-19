@extends('layouts.app')

@section('title', 'Demandes de Réaffectation — Espace Technique')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/reject-demands.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 fw-bold mb-0">Demandes de Réaffectation</h1>
            <small class="text-muted">Consultez vos demandes de retrait de dossiers et leur historique</small>
        </div>
        <div class="text-end">
            <span class="badge bg-soft-danger py-2 px-3 rounded-pill fw-bold badge-header-total">
                <i class="fas fa-undo-alt me-2"></i>{{ $demandesEnCours->count() + $historique->total() }} Demandes au total
            </span>
        </div>
    </div>

    {{-- 1. Demandes en cours (affichées uniquement s'il y en a) --}}
    @if($demandesEnCours->isNotEmpty())
        <div class="card border-0 shadow-sm overflow-hidden mb-4 card-reaffectation-pending">
            <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-warning d-flex align-items-center">
                    <span class="p-2 bg-warning bg-opacity-10 rounded-3 me-2 icon-reaffectation-box">
                        <i class="fas fa-hourglass-half text-warning"></i>
                    </span>
                    Demandes en cours d'examen
                </h6>
                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 fw-bold">{{ $demandesEnCours->count() }} en attente</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted text-uppercase table-head-uppercase">
                        <tr>
                            <th class="ps-4">Dossier</th>
                            <th>Appareil</th>
                            <th>Date demande</th>
                            <th>Motif de désaffectation</th>
                            <th class="text-end pe-4">Statut</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($demandesEnCours as $dr)
                        <tr>
                            <td class="ps-4 fw-bold text-primary">
                                #{{ $dr->dossier->num_dossier ?? '—' }}
                            </td>
                            <td>
                                <div class="small fw-bold text-dark">{{ $dr->dossier->appareil->modele ?? '—' }}</div>
                                <div class="extra-small text-muted imei-text-sub">IMEI: {{ $dr->dossier->imei ?? '—' }}</div>
                            </td>
                            <td class="small">
                                <div class="fw-bold text-dark">{{ $dr->created_at->format('d/m/Y') }}</div>
                                <div class="text-muted extra-small time-text-sub">{{ $dr->created_at->diffForHumans() }}</div>
                            </td>
                            <td>
                                <div class="small text-dark text-wrap raison-text-wrap">
                                    {{ $dr->raison }}
                                </div>
                            </td>
                            <td class="text-end pe-4">
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1 fw-bold badge-reaffectation-status">
                                    <i class="fas fa-circle me-1"></i> En Attente
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- 2. Historique des demandes traitées --}}
    <div class="card border-0 shadow-sm overflow-hidden card-reaffectation-history">
        <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark d-flex align-items-center">
                <span class="p-2 bg-secondary bg-opacity-10 rounded-3 me-2 icon-reaffectation-box">
                    <i class="fas fa-history text-secondary"></i>
                </span>
                Historique des demandes
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted text-uppercase table-head-uppercase">
                    <tr>
                        <th class="ps-4">Dossier</th>
                        <th>Appareil</th>
                        <th>Date demande</th>
                        <th>Motif</th>
                        <th>Statut</th>
                        <th class="text-end pe-4">Commentaire Administration</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($historique as $dr)
                    @php
                        $statusBadge = 'secondary';
                        $statusLabel = 'Traité';
                        if ($dr->statut === 'ACCEPTE') {
                            $statusBadge = 'success';
                            $statusLabel = 'Approuvée';
                        } elseif ($dr->statut === 'REFUSE') {
                            $statusBadge = 'danger';
                            $statusLabel = 'Refusée';
                        }
                    @endphp
                    <tr>
                        <td class="ps-4 fw-bold text-primary">
                            #{{ $dr->dossier->num_dossier ?? '—' }}
                        </td>
                        <td>
                            <div class="small fw-bold text-dark">{{ $dr->dossier->appareil->modele ?? '—' }}</div>
                            <div class="extra-small text-muted imei-text-sub">IMEI: {{ $dr->dossier->imei ?? '—' }}</div>
                        </td>
                        <td class="small">
                            <div class="fw-bold text-dark">{{ $dr->created_at->format('d/m/Y') }}</div>
                            <div class="text-muted extra-small time-text-sub">{{ $dr->created_at->diffForHumans() }}</div>
                        </td>
                        <td>
                            <div class="small text-dark text-wrap raison-text-wrap-history">
                                {{ $dr->raison }}
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-{{ $statusBadge }} bg-opacity-10 text-{{ $statusBadge }} border border-{{ $statusBadge }} border-opacity-25 rounded-pill px-3 py-1 fw-bold badge-reaffectation-status">
                                <i class="fas fa-circle me-1"></i> {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <div class="small text-muted text-wrap ms-auto commentaire-admin-text">
                                {{ $dr->commentaire_admin ?? '—' }}
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-folder-open fa-2x mb-3 d-block opacity-50"></i>
                            Aucune demande archivée dans votre historique.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($historique->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-center">
                {{ $historique->links() }}
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
