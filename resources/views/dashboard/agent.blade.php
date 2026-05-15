@extends('layouts.app')

@section('title', 'Tableau de Bord Agent SAV')

@section('content')
    <div class="container-fluid">

        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <div>
                <h1 class="h3 fw-bold mb-0 text-dark">Tableau de bord</h1>
                <small class="text-muted">Agent SAV — {{ now()->format('d/m/Y') }}</small>
            </div>
            <a href="{{ route('dossiers.create') }}" class="btn btn-primary px-4 shadow-sm">
                <i class="fas fa-plus me-2"></i> Nouveau Dossier
            </a>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show"><i
                    class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close"
                    data-bs-dismiss="alert"></button></div>
        @endif

        {{-- Toutes les cartes style Premium --}}
        @php
            $all_kpis = [
                ['label' => 'TOTAL TICKETS', 'val' => $stats['total'], 'icon' => 'fa-folder-open', 'class' => 'bg-soft-primary'],
                ['label' => 'REÇU (À AFFECTER)', 'val' => $stats['recu'], 'icon' => 'fa-plus-square', 'class' => 'bg-soft-secondary'],
                ['label' => 'EN DIAGNOSTIC', 'val' => $stats['en_diagnostic'], 'icon' => 'fa-microscope', 'class' => 'bg-soft-warning'],
                ['label' => 'EN ATTENTE DEVIS', 'val' => $stats['attente_devis'], 'icon' => 'fa-file-invoice-dollar', 'class' => 'bg-soft-warning'],
                ['label' => 'EN RÉPARATION', 'val' => $stats['en_reparation'], 'icon' => 'fa-tools', 'class' => 'bg-soft-info'],
                ['label' => 'ATTENTE PIÈCES', 'val' => $stats['attente_pieces'], 'icon' => 'fa-hour-glass-half', 'class' => 'bg-soft-danger'],
                ['label' => 'RÉPARÉS AUJOURD\'HUI', 'val' => $stats['prets_aujourdhui'], 'icon' => 'fa-check-circle', 'class' => 'bg-soft-success'],
                ['label' => 'PRÊTS À LIVRER', 'val' => $stats['prets'], 'icon' => 'fa-hand-holding-heart', 'class' => 'bg-soft-success'],
                ['label' => 'DOSSIERS FACTURÉS', 'val' => $stats['facture'], 'icon' => 'fa-file-invoice', 'class' => 'bg-soft-primary'],
                ['label' => 'DOSSIERS CLÔTURÉS', 'val' => $stats['cloture'], 'icon' => 'fa-archive', 'class' => 'bg-soft-dark'],
            ];

        @endphp

        <div class="row row-cols-xl-5 row-cols-md-3 row-cols-2 g-3 mb-4">
            @foreach($all_kpis as $k)
                <div class="col">
                    <div class="card border-0 shadow-sm h-100" style="border-radius: 12px;">
                        <div class="card-body d-flex align-items-center p-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center me-3 {{ $k['class'] }}" style="width: 48px; height: 48px; min-width: 48px;">
                                <i class="fas {{ $k['icon'] }} fa-lg"></i>
                            </div>
                            <div>
                                <div class="h4 fw-bold mb-0 text-dark">{{ $k['val'] }}</div>
                                <div class="text-muted small fw-bold text-uppercase" style="font-size: 0.6rem; letter-spacing: 0.5px;">{{ $k['label'] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <style>
            .bg-soft-primary { background-color: #e7f0ff !important; color: #0d6efd !important; }
            .bg-soft-secondary { background-color: #f8f9fa !important; color: #6c757d !important; }
            .bg-soft-warning { background-color: #fff8e1 !important; color: #f6c23e !important; }
            .bg-soft-info { background-color: #e0f7fa !important; color: #00bcd4 !important; }
            .bg-soft-danger { background-color: #ffebee !important; color: #e74a3b !important; }
            .bg-soft-success { background-color: #e8f5e9 !important; color: #1cc88a !important; }
            .bg-soft-dark { background-color: #eee !important; color: #343a40 !important; }
            
            .card { transition: transform 0.2s ease; }
            .card:hover { transform: translateY(-3px); }
        </style>

        {{-- Dossiers récents --}}
        <div class="card border-0 shadow-sm overflow-hidden" style="border-radius:15px;">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="fas fa-history me-2 text-primary"></i>Dernières Activités
                </h6>
                <a href="{{ route('dossiers.index') }}" class="btn btn-sm btn-light rounded-pill px-3 fw-bold text-primary">Voir tout</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light bg-opacity-50">
                        <tr class="small text-muted text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.5px;">
                            <th class="ps-4 py-3">Date Réception</th>
                            <th class="py-3">N° Dossier</th>
                            <th class="py-3">Client</th>
                            <th class="py-3">Appareil</th>
                            <th class="py-3">Statut</th>
                            <th class="text-end pe-4 py-3">Action</th>
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
                            <tr style="transition: all 0.2s;">
                                <td class="ps-4">
                                    <div class="fw-bold text-dark" style="font-size: 0.85rem;">{{ $d->created_at->format('d/m/Y') }}</div>
                                    <div class="text-muted small" style="font-size: 0.7rem;">{{ $d->created_at->format('H:i') }}</div>
                                </td>
                                <td>
                                    <span class="fw-bold text-primary">#{{ $d->num_dossier }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark small">{{ $d->client->name ?? '—' }}</div>
                                    <div class="text-muted small" style="font-size: 0.7rem;">{{ $d->client->telephone ?? '' }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold small">{{ $d->appareil->modele ?? '—' }}</div>
                                    <div class="text-muted small" style="font-size: 0.7rem;">IMEI: {{ $d->imei ?? '' }}</div>
                                </td>
                                <td>
                                    <span class="badge rounded-pill px-3 py-2 bg-{{ $color }} bg-opacity-10 text-{{ $color }}" style="font-size: 0.65rem; border: 1px solid rgba(0,0,0,0.05);">
                                        <i class="fas fa-circle me-1 small" style="font-size: 0.4rem;"></i> {{ $statLabels[$d->statut] ?? $d->statut }}
                                    </span>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('dossiers.show', $d->id) }}" class="btn btn-sm btn-white border shadow-sm rounded-pill px-3 fw-bold text-primary" style="font-size: 0.75rem;">
                                        <i class="fas fa-eye me-1"></i> Voir
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">Aucun dossier récent trouvé.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
@endsection