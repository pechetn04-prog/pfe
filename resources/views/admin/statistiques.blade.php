@extends('layouts.app')

@section('title', 'Statistiques et Analyses')

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 fw-bold mb-0">Statistiques et Analyses</h1>
                <small class="text-muted">Analyse de performance du service SAV</small>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-outline-secondary btn-sm rounded-pill" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Imprimer
                </button>
            </div>
        </div>

        {{-- Barre de Filtres --}}
        <div class="card border-0 shadow-sm mb-4 card-stats-rounded">
            <div class="card-body py-3">
                <form action="{{ route('admin.statistiques') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted text-uppercase label-stats-caps">Date de début</label>
                        <input type="date" name="date_debut" class="form-control form-control-sm rounded-3"
                            value="{{ $dateDebut->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-muted text-uppercase label-stats-caps">Date de fin</label>
                        <input type="date" name="date_fin" class="form-control form-control-sm rounded-3"
                            value="{{ $dateFin->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm flex-grow-1 rounded-3 fw-bold">
                            <i class="fas fa-filter me-2"></i> Filtrer
                        </button>
                        <a href="{{ route('admin.statistiques') }}" class="btn btn-light btn-sm rounded-3">
                            <i class="fas fa-sync-alt"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Cartes KPI --}}
        <div class="row g-3 mb-4">
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm text-white h-100 overflow-hidden kpi-gradient-blue">
                    <div class="card-body p-4 position-relative">
                        <div class="text-uppercase fw-bold mb-1 kpi-caps-title">Volume Total Tickets</div>
                        <div class="h2 fw-bold mb-0">{{ $totalDossiers }}</div>
                        <div class="small mt-2 kpi-subtitle">Dossiers créés à ce jour</div>
                        <i class="fas fa-ticket-alt position-absolute kpi-bg-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm text-white h-100 overflow-hidden kpi-gradient-green">
                    <div class="card-body p-4 position-relative">
                        <div class="text-uppercase fw-bold mb-1 kpi-caps-title">Chiffre d'Affaires</div>
                        <div class="h2 fw-bold mb-0">{{ number_format($chiffreAffaires, 2, ',', ' ') }} <small
                                class="small-currency">TND</small></div>
                        <div class="small mt-2 kpi-subtitle">Total des factures générées</div>
                        <i class="fas fa-wallet position-absolute kpi-bg-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm text-white h-100 overflow-hidden kpi-gradient-orange">
                    <div class="card-body p-4 position-relative">
                        <div class="text-uppercase fw-bold mb-1 kpi-caps-title">Taux d'acceptation Devis</div>
                        <div class="h2 fw-bold mb-0">{{ $tauxAcceptation }}%</div>
                        <div class="small mt-2 kpi-subtitle">Proportion des devis validés</div>
                        <i class="fas fa-handshake position-absolute kpi-bg-icon"></i>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card border-0 shadow-sm text-white h-100 overflow-hidden kpi-gradient-red">
                    <div class="card-body p-4 position-relative">
                        <div class="text-uppercase fw-bold mb-1 kpi-caps-title">Tickets en Retard</div>
                        <div class="h2 fw-bold mb-0">{{ $tauxRetard }}%</div>
                        <div class="small mt-2 kpi-subtitle">
                            {{ $retards['24-48h'] + $retards['48-72h'] + $retards['>72h'] }} dossiers en retard
                        </div>
                        <i class="fas fa-exclamation-triangle position-absolute kpi-bg-icon"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- Graphiques de répartition opérationnelle et contractuelle --}}
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100 card-stats-rounded">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="m-0 fw-bold text-dark"><i class="fas fa-chart-pie text-primary me-2"></i> Répartition des
                            Statuts (%)</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-height-wrapper">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100 card-stats-rounded">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="m-0 fw-bold text-dark"><i class="fas fa-shield-alt text-primary me-2"></i> État des
                            Garanties (%)</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-height-wrapper">
                            <canvas id="warrantyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-xl-8">
                <div class="card border-0 shadow-sm card-stats-rounded">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-user-friends me-2 text-primary"></i> Dossiers
                            traités par Technicien</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-height-wrapper">
                            <canvas id="techChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card border-0 shadow-sm card-stats-rounded">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-clock me-2 text-danger"></i> Analyse des Retards
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-height-wrapper">
                            <canvas id="retardChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Tableaux de Top --}}
        <div class="row g-4">
            <div class="col-xl-4">
                <div class="card border-0 shadow-sm h-100 card-stats-rounded">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-tools me-2 text-info"></i> Top Pannes Fréquentes
                        </h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="small text-muted text-uppercase">
                                    <th class="ps-3 table-header-micro">Description</th>
                                    <th class="text-end pe-3 table-header-micro">Occurrences</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topPannes as $p)
                                    <tr>
                                        <td class="ps-3 small fw-bold">{{ $p->panne_declaree }}</td>
                                        <td class="text-end pe-3 small text-muted">{{ $p->total }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card border-0 shadow-sm h-100 card-stats-rounded">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-boxes me-2 text-success"></i> Consommation
                            Pièces</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="small text-muted text-uppercase">
                                    <th class="ps-3 table-header-micro">Pièce</th>
                                    <th class="text-end pe-3 table-header-micro">Quantité</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topPieces as $piece)
                                    <tr>
                                        <td class="ps-3 small fw-bold">{{ $piece->nom }}</td>
                                        <td class="text-end pe-3 small text-muted">{{ $piece->total_qty }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card border-0 shadow-sm h-100 card-stats-rounded">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-mobile-alt me-2 text-warning"></i> Top 10
                            Modèles Fréquents</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr class="small text-muted text-uppercase">
                                    <th class="ps-3 table-header-micro">Modèle / Article</th>
                                    <th class="text-end pe-3 table-header-micro">Nombre</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topModeles as $m)
                                    <tr>
                                        <td class="ps-3 small fw-bold">{{ $m->modele }}</td>
                                        <td class="text-end pe-3 small text-muted">{{ $m->total }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            // Passage des données Laravel au JS externe pour les graphiques
            window.techChartData = {
                labels: {!! json_encode($dossiersParTech->pluck('name')) !!},
                values: {!! json_encode($dossiersParTech->pluck('dossiers_count')) !!}
            };
            window.retardChartData = [{{ $retards['0-24h'] }}, {{ $retards['24-48h'] }}, {{ $retards['48-72h'] }}, {{ $retards['>72h'] }}];

            window.dashboardStats = {
                status_distribution: {!! json_encode($statusDistribution) !!},
                warranty_distribution: {!! json_encode($warrantyDistribution) !!}
            };
        </script>
        <script src="{{ asset('js/statistiques.js') }}"></script>
    @endpush
@endsection
