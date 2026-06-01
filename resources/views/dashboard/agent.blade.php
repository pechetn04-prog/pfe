@extends('layouts.app')

@section('title', 'Tableau de Bord Agent SAV')



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
    
    


        <div class="row row-cols-xl-3 row-cols-md-3 row-cols-2 g-3 mb-4">
            {{-- Total Tickets --}}
            <div class="col">
                <div class="card kpi-card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center p-3">
                        <div class="kpi-icon-wrapper bg-soft-sky">
                            <i class="fas fa-folder-open fa-lg"></i>
                        </div>
                        <div>
                            <div class="kpi-value">{{ $stats['total'] }}</div>
                            <div class="kpi-label">Total Tickets</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Affectés --}}
            <div class="col">
                <div class="card kpi-card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center p-3">
                        <div class="kpi-icon-wrapper bg-soft-info">
                            <i class="fas fa-user-check fa-lg"></i>
                        </div>
                        <div>
                            <div class="kpi-value">{{ $stats['affecte'] }}</div>
                            <div class="kpi-label">Affectés</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Attente Devis --}}
            <div class="col">
                <div class="card kpi-card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center p-3">
                        <div class="kpi-icon-wrapper bg-soft-warning">
                            <i class="fas fa-file-invoice-dollar fa-lg"></i>
                        </div>
                        <div>
                            <div class="kpi-value">{{ $stats['attente_devis'] }}</div>
                            <div class="kpi-label">Attente Devis</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Attente Remplacement --}}
            <div class="col">
                <div class="card kpi-card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center p-3">
                        <div class="kpi-icon-wrapper bg-soft-warning">
                            <i class="fas fa-exchange-alt fa-lg"></i>
                        </div>
                        <div>
                            <div class="kpi-value">{{ $stats['attente_remplacement'] }}</div>
                            <div class="kpi-label">Attente Remplacement</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Prêts à livrer --}}
            <div class="col">
                <div class="card kpi-card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center p-3">
                        <div class="kpi-icon-wrapper bg-soft-success">
                            <i class="fas fa-hand-holding-heart fa-lg"></i>
                        </div>
                        <div>
                            <div class="kpi-value">{{ $stats['prets'] }}</div>
                            <div class="kpi-label">Prêts à livrer</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Clôturés --}}
            <div class="col">
                <div class="card kpi-card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center p-3">
                        <div class="kpi-icon-wrapper bg-soft-dark">
                            <i class="fas fa-archive fa-lg"></i>
                        </div>
                        <div>
                            <div class="kpi-value">{{ $stats['cloture'] }}</div>
                            <div class="kpi-label">Clôturés</div>
                        </div>
                    </div>
                </div>
            </div>
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
                                    <span class="status-badge-capsule {{ $d->statut_class }} text-uppercase">
                                        {{ $d->statut_text }}
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