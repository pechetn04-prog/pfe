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

        {{-- Statistiques --}}
        @php
            $all_kpis = [
                ['label' => 'Total Tickets', 'val' => $stats['total'], 'icon' => 'fa-folder-open', 'class' => 'bg-soft-primary'],
                ['label' => 'À Affecter', 'val' => $stats['recu'], 'icon' => 'fa-plus-square', 'class' => 'bg-soft-secondary'],
                ['label' => 'Diagnostic', 'val' => $stats['en_diagnostic'], 'icon' => 'fa-microscope', 'class' => 'bg-soft-warning'],
                ['label' => 'Attente Devis', 'val' => $stats['attente_devis'], 'icon' => 'fa-file-invoice-dollar', 'class' => 'bg-soft-warning'],
                ['label' => 'En Réparation', 'val' => $stats['en_reparation'], 'icon' => 'fa-tools', 'class' => 'bg-soft-info'],
                ['label' => 'Attente Pièces', 'val' => $stats['attente_pieces'], 'icon' => 'fa-hourglass-half', 'class' => 'bg-soft-danger'],
                ['label' => 'Réparés / Jour', 'val' => $stats['prets_aujourdhui'], 'icon' => 'fa-check-circle', 'class' => 'bg-soft-success'],
                ['label' => 'Prêts à livrer', 'val' => $stats['prets'], 'icon' => 'fa-hand-holding-heart', 'class' => 'bg-soft-success'],
                ['label' => 'Facturés', 'val' => $stats['facture'], 'icon' => 'fa-file-invoice', 'class' => 'bg-soft-primary'],
                ['label' => 'Clôturés', 'val' => $stats['cloture'], 'icon' => 'fa-archive', 'class' => 'bg-soft-dark'],
            ];
        @endphp

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
                            @php
                                $statColors = [
                                    'RECU'=>'secondary', 'AFFECTE'=>'info', 'EN_DIAGNOSTIC'=>'warning', 
                                    'EN_REPARATION'=>'primary', 'EN_ATTENTE_DEVIS'=>'warning', 
                                    'REPARE'=>'success', 'FACTURE'=>'success', 'LIVRE'=>'success', 
                                    'CLOTURE'=>'dark', 'IRREPARABLE'=>'danger', 'DEVIS_REFUSE'=>'danger',
                                    'ATTENTE_PIECE'=>'danger'
                                ];
                                $statLabels = [
                                    'RECU'=>'Reçu', 'AFFECTE'=>'Affecté', 'EN_DIAGNOSTIC'=>'Diagnostic', 
                                    'EN_REPARATION'=>'Réparation', 'EN_ATTENTE_DEVIS'=>'Attente Devis', 
                                    'REPARE'=>'Réparé', 'FACTURE'=>'Facturé', 'LIVRE'=>'Livré', 
                                    'CLOTURE'=>'Clôturé', 'IRREPARABLE'=>'Irréparable', 'DEVIS_REFUSE'=>'Refusé',
                                    'ATTENTE_PIECE'=>'Attente Pièce'
                                ];
                                $color = $statColors[$d->statut] ?? 'secondary';
                            @endphp
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
                                    <span class="badge-status-pill bg-{{ $color }} bg-opacity-10 text-{{ $color }}">
                                        <i class="fas fa-circle"></i> {{ $statLabels[$d->statut] ?? $d->statut }}
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