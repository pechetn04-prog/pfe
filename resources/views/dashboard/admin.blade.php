@extends('layouts.app')

@section('title', 'Tableau de Bord Administrateur')

@section('content')
    <div class="container-fluid">

        {{-- En-tête --}}
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 fw-bold mb-0">Tableau de Bord</h1>
                <small class="text-muted">Vue d'ensemble du service SAV — {{ now()->format('d/m/Y') }}</small>
            </div>
           
        </div>



        @if($stats['demandes_rejet_count'] > 0)
            <div class="card border-0 shadow-sm mb-4 overflow-hidden" style="border-radius: 15px; background: linear-gradient(135deg, #fffbeb 0%, #fff7ed 100%); border-left: 5px solid #f59e0b !important;">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="position-relative">
                                <div class="bg-warning bg-opacity-20 p-3 rounded-circle pulse-warning">
                                    <i class="fas fa-exclamation-triangle text-warning fs-3"></i>
                                </div>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm" style="font-size: 0.6rem;">{{ $stats['demandes_rejet_count'] }}</span>
                            </div>
                        </div>
                        <div class="col">
                            <h5 class="fw-bold text-dark mb-1">Demandes de Retrait Prioritaires</h5>
                            <p class="text-muted small mb-0">Plusieurs techniciens ont soumis des demandes de désistement pour des dossiers critiques.</p>
                            <div class="d-flex flex-wrap gap-3 mt-3">
                                @foreach($stats['demandes_rejet_recent'] as $rj)
                                    <div class="bg-white p-2 px-3 rounded-3 shadow-sm border d-flex align-items-center" style="min-width: 200px;">
                                        <div class="me-3 text-center">
                                            <div class="fw-bold text-primary" style="font-size: 0.9rem;">#{{ $rj->dossier->num_dossier ?? '???' }}</div>
                                            <div class="text-muted" style="font-size: 0.65rem;">{{ $rj->user->name ?? 'Tech' }}</div>
                                        </div>
                                        <div class="border-start ps-3">
                                            <div class="text-dark fw-medium small text-truncate" style="max-width: 150px;">{{ $rj->raison }}</div>
                                        </div>
                                    </div>
                                @endforeach
                                @if($stats['demandes_rejet_count'] > 3)
                                    <div class="bg-light px-3 py-2 rounded-pill small text-muted fw-bold">+ {{ $stats['demandes_rejet_count'] - 3 }} autres</div>
                                @endif
                            </div>
                        </div>
                        <div class="col-auto">
                            <a href="{{ route('admin.demandes_rejet.index') }}" class="btn btn-warning rounded-pill px-4 py-2 fw-bold shadow-sm transition-all hover-scale">
                                <i class="fas fa-tasks me-2"></i> EXAMINER TOUT
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <style>
                .pulse-warning { animation: pulse-orange 2s infinite; }
                @keyframes pulse-orange {
                    0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); }
                    70% { box-shadow: 0 0 0 15px rgba(245, 158, 11, 0); }
                    100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
                }
                .hover-scale:hover { transform: scale(1.05); }
            </style>
        @endif

        {{-- 8 KPIs principaux Style Premium --}}
        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3 mb-4">
            @php
                $adminKpis = [
                    ['label' => 'TOTAL TICKETS', 'val' => $stats['total'], 'icon' => 'fa-folder-open', 'class' => 'bg-soft-primary'],
                    ['label' => 'NOUVEAUX REÇUS', 'val' => $stats['recu'], 'icon' => 'fa-inbox', 'class' => 'bg-soft-danger'],
                    ['label' => 'EN DIAGNOSTIC', 'val' => $stats['en_diagnostic'], 'icon' => 'fa-microscope', 'class' => 'bg-soft-warning'],
                    ['label' => 'ATTENTE DEVIS', 'val' => $stats['attente_devis'], 'icon' => 'fa-file-invoice-dollar', 'class' => 'bg-soft-info'],
                    ['label' => 'EN RÉPARATION', 'val' => $stats['en_reparation'], 'icon' => 'fa-tools', 'class' => 'bg-soft-success'],
                    ['label' => 'ATTENTE PIÈCES', 'val' => $stats['attente_pieces'], 'icon' => 'fa-clock', 'class' => 'bg-soft-danger'],
                    ['label' => 'LIVRÉS / CLOS', 'val' => $stats['cloture'], 'icon' => 'fa-check-double', 'class' => 'bg-soft-slate'],
                    ['label' => 'UTILISATEURS', 'val' => $stats['users'], 'icon' => 'fa-users', 'class' => 'bg-soft-purple'],
                ];
            @endphp
            @foreach($adminKpis as $k)
                <div class="col">
                    <div class="card kpi-card">
                        <div class="card-body">
                            <div class="kpi-icon-wrapper {{ $k['class'] }}">
                                <i class="fas {{ $k['icon'] }}"></i>
                            </div>
                            <div class="kpi-content">
                                <div class="kpi-value" style="font-weight: 800;">{{ $k['val'] }}</div>
                                <div class="kpi-label">{{ $k['label'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="row g-4 mb-4">
            {{-- Dossiers récents --}}
            <div class="col-xl-8">
                <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 15px;">
                    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-dark">
                            <span class="p-2 bg-primary bg-opacity-10 rounded-3 me-2"><i
                                    class="fas fa-list text-primary"></i></span>
                            Dossiers Récents
                        </h6>
                        <a href="{{ route('dossiers.index') }}"
                            class="btn btn-sm btn-link text-decoration-none fw-bold small">Voir tout</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="small text-muted text-uppercase">
                                    <th class="ps-4" style="font-size: 0.65rem;">N° Dossier</th>
                                    <th style="font-size: 0.65rem;">Client</th>
                                    <th style="font-size: 0.65rem;">Statut</th>
                                    <th style="font-size: 0.65rem;">Date</th>
                                    <th class="text-end pe-4" style="font-size: 0.65rem;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentDossiers as $d)
                                    @php
                                        $statClasses = [
                                            'RECU' => 'bg-light text-muted',
                                            'AFFECTE' => 'bg-secondary text-white',
                                            'EN_DIAGNOSTIC' => 'bg-info text-dark',
                                            'EN_REPARATION' => 'bg-primary text-white',
                                            'EN_ATTENTE_DEVIS' => 'bg-warning text-dark',
                                            'REPARE' => 'bg-success text-white',
                                            'FACTURE' => 'bg-success text-white',
                                            'LIVRE' => 'bg-success text-white',
                                            'CLOTURE' => 'bg-dark text-white',
                                            'IRREPARABLE' => 'bg-danger text-white',
                                        ];
                                    @endphp
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="fw-bolder text-dark" style="font-weight: 800;">#{{ $d->num_dossier }}</div>
                                            <div class="text-muted" style="font-size: 0.65rem;">
                                                {{ $d->appareil->modele ?? '—' }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-bolder text-dark" style="font-weight: 800;">{{ $d->client->name ?? '—' }}</div>
                                            <div class="text-muted" style="font-size: 0.65rem;">
                                                {{ $d->client->telephone ?? '—' }}</div>
                                        </td>
                                        <td>
                                            <span
                                                class="badge {{ $statClasses[$d->statut] ?? 'bg-secondary' }} rounded-pill px-3 py-1"
                                                style="font-size: 0.65rem;">
                                                {{ str_replace('_', ' ', $d->statut) }}
                                            </span>
                                        </td>
                                        <td class="text-muted small">
                                            {{ $d->created_at ? $d->created_at->format('d/m/Y') : '—' }}</td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('dossiers.show', $d->id) }}"
                                                class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold"
                                                style="font-size: 0.75rem;">
                                                Voir
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Colonne de droite : Alertes et Blocages --}}
            <div class="col-xl-4">
                
                {{-- Dossiers en attente de pièces --}}
                @if($dossiersAttentePieces->count() > 0)
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="fw-bold mb-0 text-dark">
                            <span class="p-2 bg-danger bg-opacity-10 rounded-3 me-2"><i class="fas fa-clock text-danger"></i></span>
                            En Attente Pièces
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($dossiersAttentePieces as $d)
                            <a href="{{ route('dossiers.show', $d->id) }}" class="list-group-item list-group-item-action border-0 border-bottom mx-2 px-2 py-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="small fw-bolder text-dark" style="font-weight: 800;">#{{ $d->num_dossier }}</div>
                                        <div class="text-muted fw-bold" style="font-size: 0.65rem;">{{ $d->appareil->modele ?? '—' }}</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="small text-muted fw-bold" style="font-size: 0.6rem;">{{ $d->updated_at->diffForHumans() }}</div>
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1" style="font-size: 0.6rem; font-weight: 800;">BLOQUÉ</span>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 text-center pb-3 pt-2">
                        <a href="{{ route('dossiers.index', ['statut' => 'ATTENTE_PIECE']) }}" class="btn btn-sm btn-light rounded-pill px-4 fw-bold shadow-sm">
                            Voir tout
                        </a>
                    </div>
                </div>
                @endif

                {{-- Alertes de Stock --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="fw-bold mb-0 text-dark">
                            <span class="p-2 bg-danger bg-opacity-10 rounded-3 me-2"><i class="fas fa-exclamation-circle text-danger"></i></span>
                            Alertes Stock
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse($stockAlerts as $alert)
                                <div class="list-group-item d-flex justify-content-between align-items-center py-3 border-0 border-bottom mx-2 px-2">
                                    <div>
                                        <div class="small fw-bolder text-dark" style="font-weight: 800;">{{ $alert->nom }}</div>
                                        <div class="text-muted fw-bold" style="font-size: 0.65rem;">Réf: {{ $alert->reference }}</div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge {{ $alert->quantite == 0 ? 'bg-danger' : 'bg-warning text-dark' }} rounded-pill fw-bold" style="font-size: 0.65rem;">
                                            {{ $alert->quantite }} restant(s)
                                        </span>
                                        <div class="text-muted fw-bold" style="font-size: 0.6rem;">Seuil: {{ $alert->seuil_alerte }}</div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-4 text-center">
                                    <i class="fas fa-check-circle text-success fa-2x mb-2 opacity-25"></i>
                                    <div class="small text-muted fw-bold">Aucune alerte de stock</div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    @if($stockAlerts->count() > 0)
                        <div class="card-footer bg-white border-0 text-center pb-3 pt-2">
                            <a href="{{ route('stock.index') }}" class="btn btn-sm btn-light rounded-pill px-4 fw-bold shadow-sm">Gérer le stock</a>
                        </div>
                    @endif
                </div>


            </div>
        </div>


        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="m-0 fw-bold text-dark"><i class="fas fa-chart-pie text-primary me-2"></i> Répartition des Statuts (%)</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="statusChart" height="300"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100" style="border-radius: 20px;">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="m-0 fw-bold text-dark"><i class="fas fa-shield-alt text-primary me-2"></i> État des Garanties (%)</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="warrantyChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Passage des données statistiques au JS externe
        window.dashboardStats = {!! json_encode($stats) !!};
    </script>
    <script src="{{ asset('js/admin_dashboard.js') }}"></script>
@endpush
</div>
@endsection