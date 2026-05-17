@extends('layouts.app')

@section('title', 'Tableau de Bord Agent SAV')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-premium.css') }}">
@endpush

@section('content')
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 dash-title mb-0">Tableau de bord</h1>
                <small class="text-muted fw-bold">Agent SAV — {{ now()->translatedFormat('d F Y') }}</small>
            </div>
            <a href="{{ route('dossiers.create') }}" class="btn btn-primary px-4 shadow-sm fw-bold">
                <i class="fas fa-plus me-2"></i> Nouveau Dossier
            </a>
        </div>



        <div class="row row-cols-xl-5 row-cols-md-3 row-cols-2 g-3 mb-4">
            @foreach($all_kpis as $k)
                <div class="col">
                    <div class="card kpi-card shadow-sm h-100">
                        <div class="card-body d-flex align-items-center p-3">
                            <div class="kpi-icon-wrapper {{ $k['class'] }}">
                                <i class="fas {{ $k['icon'] }} fa-lg"></i>
                            </div>
                            <div>
                                <div class="kpi-value">{{ $k['val'] }}</div>
                                <div class="kpi-label">{{ $k['label'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Dossiers récents --}}
        <div class="card dash-card-table overflow-hidden">
            <div class="card-header bg-white border-0 py-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="fas fa-bolt me-2 text-warning"></i>Dernières Activités
                </h5>
                <a href="{{ route('dossiers.index') }}" class="btn btn-sm btn-light border rounded-pill px-4 fw-bold">Voir tout</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 dash-table">
                    <thead>
                        <tr>
                            <th class="ps-4">Réception</th>
                            <th>N° Dossier</th>
                            <th>Client</th>
                            <th>Appareil</th>
                            <th>Statut</th>
                            <th class="text-end pe-4">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentDossiers as $d)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark small">{{ $d->created_at->format('d/m/Y') }}</div>
                                    <div class="text-muted extra-small">{{ $d->created_at->format('H:i') }}</div>
                                </td>
                                <td>
                                    <span class="fw-bold text-primary">#{{ $d->num_dossier }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark small">{{ $d->client->name ?? '—' }}</div>
                                    <div class="text-muted extra-small">{{ $d->client->telephone ?? '' }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold small text-dark">{{ $d->appareil->modele ?? '—' }}</div>
                                    <div class="text-muted extra-small">IMEI: {{ $d->imei ?? '—' }}</div>
                                </td>
                                <td>
                                    <span class="badge-status-pill bg-{{ $d->badge_color }} bg-opacity-10 text-{{ $d->badge_color }}">
                                        <i class="fas fa-circle"></i> {{ $d->badge_label }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('dossiers.show', $d->id) }}" class="btn btn-sm btn-white border rounded-pill px-3 fw-bold text-primary">
                                        Explorer
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted fw-bold">Aucun dossier récent.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection