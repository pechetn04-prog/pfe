@extends('layouts.app')

@section('title', 'Tableau de bord Technicien')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard-premium.css') }}">
@endpush

@section('content')
<div class="container-fluid">

    {{-- En-tête --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 dash-title mb-0">Espace Technique</h1>
            <small class="text-muted fw-bold">Bienvenue, {{ auth()->user()->name }} — {{ now()->translatedFormat('d F Y') }}</small>
        </div>
        <div class="text-end">
            <span class="badge bg-soft-info py-2 px-3 rounded-pill fw-bold" style="font-size: 0.8rem;">
                <i class="fas fa-microscope me-2"></i>{{ $dossiersEnCours }} Dossiers Actifs
            </span>
        </div>
    </div>

    {{-- KPIs Technicien --}}
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-5 g-3 mb-4">
        @php
            $tech_kpis = [
                ['label' => 'Total Assignés', 'val' => $totalAssigne, 'icon' => 'fa-briefcase', 'class' => 'bg-soft-primary'],
                ['label' => 'À Diagnostiquer', 'val' => $aDiagnostiquer, 'icon' => 'fa-search', 'class' => 'bg-soft-warning'],
                ['label' => 'En Réparation', 'val' => $enReparation, 'icon' => 'fa-tools', 'class' => 'bg-soft-success'],
                ['label' => 'Attente Pièces', 'val' => $attentePieces, 'icon' => 'fa-hourglass-half', 'class' => 'bg-soft-danger'],
                ['label' => 'Terminés (Mois)', 'val' => $terminesMois, 'icon' => 'fa-check-double', 'class' => 'bg-soft-info'],
            ];
        @endphp

        @foreach($tech_kpis as $k)
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

    <div class="row g-4 mb-4">
        {{-- À Diagnostiquer --}}
        <div class="col-xl-6">
            <div class="card dash-card-table h-100">
                <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-search me-2 text-warning"></i>Priorité Diagnostic
                    </h6>
                    <span class="badge bg-soft-warning text-warning rounded-pill px-3">{{ $dossiersDiagnostique->count() }} à faire</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 dash-table">
                        <thead>
                            <tr>
                                <th class="ps-4">Dossier</th>
                                <th>Appareil</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dossiersDiagnostique as $d)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">#{{ $d->num_dossier }}</div>
                                    <div class="extra-small text-muted">{{ $d->created_at ? $d->created_at->diffForHumans() : '—' }}</div>
                                </td>
                                <td>
                                    <div class="small fw-bold text-dark">{{ $d->appareil->modele ?? '—' }}</div>
                                    <div class="extra-small text-muted">IMEI: {{ $d->imei }}</div>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('diagnostics.create', $d->id) }}" class="btn btn-sm btn-warning rounded-pill px-3 fw-bold shadow-sm">
                                        ANALYSER
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center py-5 text-muted small fw-bold">Aucun diagnostic en attente.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- À Réparer --}}
        <div class="col-xl-6">
            <div class="card dash-card-table h-100">
                <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark">
                        <i class="fas fa-tools me-2 text-success"></i>Priorité Réparation
                    </h6>
                    <span class="badge bg-soft-success text-success rounded-pill px-3">{{ $dossiersReparation->count() }} à faire</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 dash-table">
                        <thead>
                            <tr>
                                <th class="ps-4">Dossier</th>
                                <th>Appareil</th>
                                <th class="text-end pe-4">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dossiersReparation as $d)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">#{{ $d->num_dossier }}</div>
                                    <div class="extra-small text-muted">{{ $d->created_at ? $d->created_at->diffForHumans() : '—' }}</div>
                                </td>
                                <td>
                                    <div class="small fw-bold text-dark">{{ $d->appareil->modele ?? '—' }}</div>
                                    <div class="extra-small text-muted">IMEI: {{ $d->imei }}</div>
                                </td>
                                <td class="text-end pe-4 d-flex justify-content-end gap-2">
                                    <a href="{{ route('interventions.create', $d->id) }}" class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-sm">
                                        RÉPARER
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center py-5 text-muted small fw-bold">Aucune réparation en attente.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Travaux Terminés --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card dash-card-table">
                <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark d-flex align-items-center">
                        <span class="p-2 bg-info bg-opacity-10 rounded-3 me-2"><i class="fas fa-history text-info"></i></span>
                        Dernières activités
                    </h6>
                    <a href="{{ route('technicien.tickets') }}" class="btn btn-sm btn-light border rounded-pill px-4 fw-bold">Historique Complet</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 dash-table">
                        <thead>
                            <tr>
                                <th class="ps-4">Dossier</th>
                                <th>Date</th>
                                <th>IMEI</th>
                                <th>Garantie</th>
                                <th>Client</th>
                                <th>Appareil</th>
                                <th class="text-end pe-4">Statut</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dossiersTermines as $d)
                            @php
                                $statColors = [
                                    'REPARE'=>'success', 'FACTURE'=>'success', 'LIVRE'=>'success', 
                                    'CLOTURE'=>'dark', 'IRREPARABLE'=>'danger', 'DEVIS_REFUSE'=>'danger'
                                ];
                                $color = $statColors[$d->statut] ?? 'secondary';
                                $garantieColor = $d->sous_garantie ? 'success' : 'danger';
                                $garantieText = $d->sous_garantie ? 'SOUS GARANTIE' : 'HORS GARANTIE';
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">#{{ $d->num_dossier }}</div>
                                </td>
                                <td>
                                    <div class="small fw-bold text-dark">{{ $d->updated_at->format('d/m/Y') }}</div>
                                    <div class="extra-small text-muted">{{ $d->updated_at->format('H:i') }}</div>
                                </td>
                                <td>
                                    <div class="small fw-bold text-primary">{{ $d->imei }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $garantieColor }} bg-opacity-10 text-{{ $garantieColor }} rounded-pill px-2 py-1" style="font-size: 0.6rem; font-weight: 800;">
                                        {{ $garantieText }}
                                    </span>
                                </td>
                                <td>
                                    <div class="small fw-bold text-dark">{{ $d->client->name ?? '—' }}</div>
                                </td>
                                <td>
                                    <div class="small fw-bold text-muted">{{ $d->appareil->modele ?? '—' }}</div>
                                </td>
                                <td class="text-end pe-4">
                                    <span class="badge-status-pill bg-{{ $color }} bg-opacity-10 text-{{ $color }}">
                                        <i class="fas fa-circle"></i> {{ str_replace('_', ' ', $d->statut) }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center py-5 text-muted small fw-bold">Aucune activité récente.</td></tr>
                            @endforelse
                        </tbody>


                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
