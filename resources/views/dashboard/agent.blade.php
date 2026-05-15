@extends('layouts.app')

@section('title', 'Tableau de Bord Agent SAV')

@section('content')
<div class="container-fluid">

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 fw-bold mb-0">Bonjour, {{ $user->name }} <span class="text-muted fs-5 fw-normal">👋</span></h1>
            <small class="text-muted">Agent SAV — {{ now()->format('d/m/Y') }}</small>
        </div>
        <a href="{{ route('dossiers.create') }}" class="btn btn-primary px-4 shadow-sm">
            <i class="fas fa-plus me-2"></i> Nouveau Dossier
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show"><i class="fas fa-check-circle me-2"></i>{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    {{-- Toutes les cartes style Premium --}}
    @php
        $all_kpis = [
            ['label'=>'TOTAL TICKETS',       'val'=>$stats['total'],            'icon'=>'fa-folder-open',  'class'=>'bg-soft-primary'],
            ['label'=>'REÇU (À AFFECTER)',   'val'=>$stats['recu'],             'icon'=>'fa-plus-square',  'class'=>'bg-soft-secondary'],
            ['label'=>'EN DIAGNOSTIC',       'val'=>$stats['en_diagnostic'],    'icon'=>'fa-microscope',   'class'=>'bg-soft-warning'],
            ['label'=>'EN ATTENTE DEVIS',    'val'=>$stats['attente_devis'],    'icon'=>'fa-file-invoice-dollar', 'class'=>'bg-soft-warning'],
            ['label'=>'EN RÉPARATION',       'val'=>$stats['en_reparation'],    'icon'=>'fa-tools',        'class'=>'bg-soft-info'],
            ['label'=>'ATTENTE PIÈCES',      'val'=>$stats['attente_pieces'],   'icon'=>'fa-hour-glass-half','class'=>'bg-soft-danger'],
            ['label'=>'RÉPARÉS AUJOURD\'HUI','val'=>$stats['prets_aujourdhui'], 'icon'=>'fa-check-circle', 'class'=>'bg-soft-success'],
            ['label'=>'PRÊTS À LIVRER',      'val'=>$stats['prets'],            'icon'=>'fa-hand-holding-heart','class'=>'bg-soft-success'],
        ];
    @endphp

    <div class="row g-3 mb-4">
        @foreach($all_kpis as $k)
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card border-0 shadow-sm" style="border-radius: 12px;">
                <div class="card-body">
                    <div class="kpi-icon-wrapper {{ $k['class'] }}">
                        <i class="fas {{ $k['icon'] }}"></i>
                    </div>
                    <div class="kpi-content">
                        <div class="kpi-value h4 fw-bold mb-0">{{ $k['val'] }}</div>
                        <div class="kpi-label text-muted small fw-bold" style="font-size: 0.65rem;">{{ $k['label'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Dossiers récents --}}
    <div class="card border-0 shadow-sm" style="border-radius:12px;">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0"><i class="fas fa-clock me-2 text-primary"></i>Activités Récentes</h6>
            <a href="{{ route('dossiers.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Voir tout</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="small text-muted text-uppercase">
                        <th class="ps-4">N° Dossier</th>
                        <th>Client</th>
                        <th>Technicien</th>
                        <th>Statut</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentDossiers as $d)
                    @php
                        $statClasses = ['AFFECTE'=>'bg-secondary','EN_DIAGNOSTIC'=>'bg-info text-dark','EN_REPARATION'=>'bg-primary','EN_ATTENTE_DEVIS'=>'bg-warning text-dark','REPARE'=>'bg-success','FACTURE'=>'bg-success','LIVRE'=>'bg-success','CLOTURE'=>'bg-dark','IRREPARABLE'=>'bg-danger','DEVIS_REFUSE'=>'bg-danger'];
                    @endphp
                    <tr>
                        <td class="ps-4 fw-bold text-primary">#{{ $d->num_dossier }}</td>
                        <td>{{ $d->client->name ?? '—' }}</td>
                        <td>{{ $d->technicien->name ?? '<span class="text-muted">Non assigné</span>' }}</td>
                        <td><span class="badge {{ $statClasses[$d->statut] ?? 'bg-secondary' }} rounded-pill px-3">{{ $d->statut }}</span></td>
                        <td class="text-end pe-4">
                            <a href="{{ route('dossiers.show', $d->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">Voir</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">Aucun dossier récent.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
