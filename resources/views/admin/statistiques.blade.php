@extends('layouts.app')

@section('title', 'Statistiques et Analyses')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin_statistiques.css') }}">
@endpush

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
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-body py-3">
            <form action="{{ route('admin.statistiques') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.65rem;">Date de début</label>
                    <input type="date" name="date_debut" class="form-control form-control-sm rounded-3" value="{{ $dateDebut->format('Y-m-d') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted text-uppercase" style="font-size: 0.65rem;">Date de fin</label>
                    <input type="date" name="date_fin" class="form-control form-control-sm rounded-3" value="{{ $dateFin->format('Y-m-d') }}">
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
            <div class="card border-0 shadow-sm text-white h-100 overflow-hidden" style="border-radius: 15px; background: linear-gradient(135deg, #1e40af, #3b82f6);">
                <div class="card-body p-4 position-relative">
                    <div class="text-uppercase fw-bold mb-1" style="font-size: 0.65rem; opacity: 0.8; letter-spacing: 0.5px;">Volume Total Tickets</div>
                    <div class="h2 fw-bold mb-0">{{ $totalDossiers }}</div>
                    <div class="small mt-2" style="opacity: 0.7;">Dossiers créés à ce jour</div>
                    <i class="fas fa-ticket-alt position-absolute" style="right: -10px; bottom: -10px; font-size: 5rem; opacity: 0.1;"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm text-white h-100 overflow-hidden" style="border-radius: 15px; background: linear-gradient(135deg, #065f46, #10b981);">
                <div class="card-body p-4 position-relative">
                    <div class="text-uppercase fw-bold mb-1" style="font-size: 0.65rem; opacity: 0.8; letter-spacing: 0.5px;">Chiffre d'Affaires</div>
                    <div class="h2 fw-bold mb-0">{{ number_format($chiffreAffaires, 2, ',', ' ') }} <small style="font-size: 1rem;">TND</small></div>
                    <div class="small mt-2" style="opacity: 0.7;">Total des factures générées</div>
                    <i class="fas fa-wallet position-absolute" style="right: -10px; bottom: -10px; font-size: 5rem; opacity: 0.1;"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm text-white h-100 overflow-hidden" style="border-radius: 15px; background: linear-gradient(135deg, #92400e, #f59e0b);">
                <div class="card-body p-4 position-relative">
                    <div class="text-uppercase fw-bold mb-1" style="font-size: 0.65rem; opacity: 0.8; letter-spacing: 0.5px;">Taux d'acceptation Devis</div>
                    <div class="h2 fw-bold mb-0">{{ $tauxAcceptation }}%</div>
                    <div class="small mt-2" style="opacity: 0.7;">Proportion des devis validés</div>
                    <i class="fas fa-handshake position-absolute" style="right: -10px; bottom: -10px; font-size: 5rem; opacity: 0.1;"></i>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm text-white h-100 overflow-hidden" style="border-radius: 15px; background: linear-gradient(135deg, #991b1b, #ef4444);">
                <div class="card-body p-4 position-relative">
                    <div class="text-uppercase fw-bold mb-1" style="font-size: 0.65rem; opacity: 0.8; letter-spacing: 0.5px;">Tickets en Retard</div>
                    <div class="h2 fw-bold mb-0">{{ $tauxRetard }}%</div>
                    <div class="small mt-2" style="opacity: 0.7;">{{ array_sum($retards) }} dossiers > 72h</div>
                    <i class="fas fa-exclamation-triangle position-absolute" style="right: -10px; bottom: -10px; font-size: 5rem; opacity: 0.1;"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Graphiques --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-8">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-user-friends me-2 text-primary"></i> Dossiers traités par Technicien</h6>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="techChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-clock me-2 text-danger"></i> Analyse des Retards</h6>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="retardChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tableaux de Top --}}
    <div class="row g-4">
        <div class="col-xl-4">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-tools me-2 text-info"></i> Top Pannes Fréquentes</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="small text-muted text-uppercase">
                                <th class="ps-3" style="font-size: 0.6rem;">Description</th>
                                <th class="text-end pe-3" style="font-size: 0.6rem;">Occurrences</th>
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
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-boxes me-2 text-success"></i> Consommation Pièces</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="small text-muted text-uppercase">
                                <th class="ps-3" style="font-size: 0.6rem;">Pièce</th>
                                <th class="text-end pe-3" style="font-size: 0.6rem;">Quantité</th>
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
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="fas fa-mobile-alt me-2 text-warning"></i> Top 10 Modèles Fréquents</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr class="small text-muted text-uppercase">
                                <th class="ps-3" style="font-size: 0.6rem;">Modèle / Article</th>
                                <th class="text-end pe-3" style="font-size: 0.6rem;">Nombre</th>
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
document.addEventListener('DOMContentLoaded', function() {
    // Chart Tech
    const techCtx = document.getElementById('techChart');
    if(techCtx) {
        new Chart(techCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($dossiersParTech->pluck('name')) !!},
                datasets: [{
                    label: 'Dossiers',
                    data: {!! json_encode($dossiersParTech->pluck('dossiers_count')) !!},
                    backgroundColor: '#10b981',
                    borderRadius: 8,
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { 
                    y: { beginAtZero: true, grid: { display: false } }, 
                    x: { grid: { display: false } } 
                }
            }
        });
    }

    // Chart Retards
    const retardCtx = document.getElementById('retardChart');
    if(retardCtx) {
        new Chart(retardCtx, {
            type: 'doughnut',
            data: {
                labels: ['24-48h', '48-72h', '>72h'],
                datasets: [{
                    data: [{{ $retards['24-48h'] }}, {{ $retards['48-72h'] }}, {{ $retards['>72h'] }}],
                    backgroundColor: ['#f59e0b', '#f97316', '#ef4444'],
                    borderWidth: 0,
                    cutout: '70%'
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 6, font: { size: 10 } } }
                }
            }
        });
    }
});
</script>
@endpush
@endsection
