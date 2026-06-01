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
            <div class="card border-0 shadow-sm mb-4 overflow-hidden card-admin-alert-banner">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="position-relative">
                                <div class="bg-warning bg-opacity-20 p-3 rounded-circle pulse-warning">
                                    <i class="fas fa-exclamation-triangle text-warning fs-3"></i>
                                </div>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm font-size-06">{{ $stats['demandes_rejet_count'] }}</span>
                            </div>
                        </div>
                        <div class="col">
                            <h5 class="fw-bold text-dark mb-1">Demandes de Retrait Prioritaires</h5>
                            <p class="text-muted small mb-0">Plusieurs techniciens ont soumis des demandes de désistement pour des dossiers critiques.</p>
                            <div class="d-flex flex-wrap gap-3 mt-3">
                                @foreach($stats['demandes_rejet_recent'] as $rj)
                                    <div class="bg-white p-2 px-3 rounded-3 shadow-sm border d-flex align-items-center min-width-200">
                                        <div class="me-3 text-center">
                                            <div class="fw-bold text-primary font-size-09">#{{ $rj->dossier->num_dossier ?? '???' }}</div>
                                            <div class="text-muted font-size-065">{{ $rj->user->name ?? 'Tech' }}</div>
                                        </div>
                                        <div class="border-start ps-3">
                                            <div class="text-dark fw-medium small text-truncate max-width-150">{{ $rj->raison }}</div>
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


        @endif

        {{-- KPIs principaux Style Premium --}}
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-3 row-cols-xl-3 g-3 mb-4">

            {{-- TOTAL DOSSIERS --}}
            <div class="col">
                <div class="card kpi-card">
                    <div class="card-body">
                        <div class="kpi-icon-wrapper bg-soft-sky">
                            <i class="fas fa-folder-open"></i>
                        </div>
                        <div class="kpi-content">
                            <div class="kpi-value font-weight-800">{{ $stats['total'] }}</div>
                            <div class="kpi-label">TOTAL DOSSIERS</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ATTENTE DEVIS --}}
            <div class="col">
                <div class="card kpi-card">
                    <div class="card-body">
                        <div class="kpi-icon-wrapper bg-soft-info">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <div class="kpi-content">
                            <div class="kpi-value font-weight-800">{{ $stats['attente_devis'] }}</div>
                            <div class="kpi-label">ATTENTE DEVIS</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ATTENTE PIÈCES --}}
            <div class="col">
                <div class="card kpi-card">
                    <div class="card-body">
                        <div class="kpi-icon-wrapper bg-soft-danger">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="kpi-content">
                            <div class="kpi-value font-weight-800">{{ $stats['attente_pieces'] }}</div>
                            <div class="kpi-label">ATTENTE PIÈCES</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ATTENTE REMPLACEMENT --}}
            <div class="col">
                <div class="card kpi-card">
                    <div class="card-body">
                        <div class="kpi-icon-wrapper bg-soft-warning">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <div class="kpi-content">
                            <div class="kpi-value font-weight-800">{{ $stats['attente_validation_remplacement'] }}</div>
                            <div class="kpi-label">ATTENTE REMPLACEMENT</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- IRRÉPARABLES --}}
            <div class="col">
                <div class="card kpi-card">
                    <div class="card-body">
                        <div class="kpi-icon-wrapper bg-soft-danger">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="kpi-content">
                            <div class="kpi-value font-weight-800">{{ $stats['irreparable'] }}</div>
                            <div class="kpi-label">IRRÉPARABLES</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- LIVRÉS / CLOS --}}
            <div class="col">
                <div class="card kpi-card">
                    <div class="card-body">
                        <div class="kpi-icon-wrapper bg-soft-secondary">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <div class="kpi-content">
                            <div class="kpi-value font-weight-800">{{ $stats['cloture'] }}</div>
                            <div class="kpi-label">LIVRÉS / CLOS</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Dossiers Récents (2ème Position - Élargi pour une lisibilité maximale) --}}
        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm overflow-hidden card-admin-box-15">
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
                                    <th class="ps-4 table-th-small">N° Dossier</th>
                                    <th class="table-th-small">Client</th>
                                    <th class="table-th-small">Statut</th>
                                    <th class="table-th-small">Date</th>
                                    <th class="text-end pe-4 table-th-small">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentDossiers as $d)
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="fw-bolder text-dark font-weight-800">#{{ $d->num_dossier }}</div>
                                            <div class="text-muted font-size-065">
                                                {{ $d->appareil->modele ?? '—' }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-bolder text-dark font-weight-800">{{ $d->client->name ?? '—' }}</div>
                                            <div class="text-muted font-size-065">
                                                {{ $d->client->telephone ?? '—' }}</div>
                                        </td>
                                        <td>
                                             <span class="status-badge-capsule {{ $d->statut_class }} text-uppercase font-size-065">
                                                 {{ $d->statut_text }}
                                             </span>
                                        </td>
                                        <td class="text-muted small">
                                            {{ $d->created_at ? $d->created_at->format('d/m/Y') : '—' }}</td>
                                        <td class="text-end pe-4">
                                            <a href="{{ route('dossiers.show', $d->id) }}"
                                                class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold font-size-075">
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
        </div>

        {{-- Alertes et Blocages (3ème Position) --}}
        @php
            // Calculer la taille de colonne en fonction du nombre de sections actives
            $activeSectionsCount = 1; // Alertes Stock est toujours présente
            if($dossiersAttenteRemplacement->count() > 0) $activeSectionsCount++;
            if($dossiersAttentePieces->count() > 0) $activeSectionsCount++;
            
            $colClass = 'col-xl-4 col-md-6';
            if ($activeSectionsCount == 2) {
                $colClass = 'col-xl-6 col-md-6';
            } elseif ($activeSectionsCount == 1) {
                $colClass = 'col-xl-12';
            }
        @endphp

        <div class="row g-3 mb-4">
            
            {{-- Dossiers en attente de remplacement --}}
            @if($dossiersAttenteRemplacement->count() > 0)
                <div class="{{ $colClass }}">
                    <div class="card border-0 shadow-sm h-100 card-replacement-waiting">
                        <div class="card-header bg-white border-0 py-3">
                            <h6 class="fw-bold mb-0 text-dark">
                                <span class="p-2 bg-warning bg-opacity-10 rounded-3 me-2"><i class="fas fa-exchange-alt text-warning"></i></span>
                                En Attente Remplacement
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @foreach($dossiersAttenteRemplacement as $d)
                                <a href="{{ route('dossiers.show', $d->id) }}" class="list-group-item list-group-item-action border-0 border-bottom mx-2 px-2 py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="small fw-bolder text-dark font-weight-800">#{{ $d->num_dossier }}</div>
                                            <div class="text-muted fw-bold font-size-065">{{ $d->appareil->modele ?? '—' }}</div>
                                        </div>
                                        <div class="text-end">
                                            <div class="small text-muted fw-bold font-size-06">{{ $d->updated_at->diffForHumans() }}</div>
                                            <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-2 py-1 font-size-06 font-weight-800">À VALIDER</span>
                                        </div>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0 text-center pb-3 pt-2 mt-auto">
                            <a href="{{ route('dossiers.index', ['statut' => 'ATTENTE_VALIDATION_REMPLACEMENT']) }}" class="btn btn-sm btn-light rounded-pill px-4 fw-bold shadow-sm">
                                Voir tout
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Dossiers en attente de pièces --}}
            @if($dossiersAttentePieces->count() > 0)
                <div class="{{ $colClass }}">
                    <div class="card border-0 shadow-sm h-100 card-stat-box">
                        <div class="card-header bg-white border-0 py-3">
                            <h6 class="fw-bold mb-0 text-dark">
                                <span class="p-2 bg-danger bg-opacity-10 rounded-3 me-2"><i class="fas fa-clock text-danger"></i></span>
                                En Attente Pièces
                            </h6>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-group list-group-flush">
                                @foreach($dossiersAttentePieces as $d)
                                <a href="{{ route('dossiers.show', $d->id) }}" class="list-group-item list-group-item-action border-0 border-bottom mx-2 px-2 py-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="small fw-bolder text-dark font-weight-800">#{{ $d->num_dossier }}</div>
                                            <div class="text-muted fw-bold font-size-065">{{ $d->appareil->modele ?? '—' }}</div>
                                        </div>
                                        <div class="text-end">
                                            <div class="small text-muted fw-bold font-size-06">{{ $d->updated_at->diffForHumans() }}</div>
                                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-2 py-1 font-size-06 font-weight-800">BLOQUÉ</span>
                                        </div>
                                    </div>
                                </a>
                                @endforeach
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0 text-center pb-3 pt-2 mt-auto">
                            <a href="{{ route('dossiers.index', ['statut' => 'ATTENTE_PIECE']) }}" class="btn btn-sm btn-light rounded-pill px-4 fw-bold shadow-sm">
                                Voir tout
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Alertes de Stock --}}
            <div class="{{ $colClass }}">
                <div class="card border-0 shadow-sm h-100 card-stat-box">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="fw-bold mb-0 text-dark">
                            <span class="p-2 bg-danger bg-opacity-10 rounded-3 me-2"><i class="fas fa-exclamation-circle text-danger"></i></span>
                            Alertes Stock
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse($stockAlerts as $alert)
                                <div class="list-group-item d-flex justify-content-between align-items-center py-2 border-0 border-bottom mx-2 px-2">
                                    <div>
                                        <div class="small fw-800 text-dark">{{ $alert->nom }}</div>
                                        <div class="text-muted fw-bold font-size-065">Réf: {{ $alert->reference }}</div>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge {{ $alert->quantite == 0 ? 'bg-danger' : 'bg-warning text-dark' }} rounded-pill fw-bold font-size-065">
                                            {{ $alert->quantite }} restant(s)
                                        </span>
                                        <div class="text-muted fw-bold font-size-06">Seuil: {{ $alert->seuil_alerte }}</div>
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
                        <div class="card-footer bg-white border-0 text-center pb-3 pt-2 mt-auto">
                            <a href="{{ route('stock.index') }}" class="btn btn-sm btn-light rounded-pill px-4 fw-bold shadow-sm">Gérer le stock</a>
                        </div>
                    @endif
                </div>
            </div>

        </div>

    </div>
</div>



</div>
@endsection