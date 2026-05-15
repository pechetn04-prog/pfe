@extends('layouts.app')

@section('title', 'Tableau de bord Technicien')

@section('content')
<div class="container-fluid">

    {{-- En-tête --}}
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 fw-bold mb-0">Tableau de bord</h1>
            <small class="text-muted">Espace Technicien — {{ now()->format('d/m/Y') }}</small>
        </div>
        <div class="text-end">
            <div class="small fw-bold text-dark">Bonjour, {{ auth()->user()->name }}</div>
            <div class="small text-muted">Aujourd'hui : {{ $dossiersEnCours }} dossiers en cours</div>
        </div>
    </div>



    {{-- 5 KPIs principaux pour le Technicien --}}
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-5 g-3 mb-4">
        {{-- Card 1: Total Assigné --}}
        <div class="col">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="rounded-4 p-3 me-3 d-flex align-items-center justify-content-center" style="background: #eff6ff; width: 56px; height: 56px; flex-shrink: 0;">
                        <i class="fas fa-briefcase text-primary"></i>
                    </div>
                    <div>
                        <div class="h4 fw-bolder mb-0 text-dark" style="font-weight: 800;">{{ $totalAssigne }}</div>
                        <div class="text-muted text-uppercase fw-bold" style="font-size: 0.55rem; letter-spacing: 0.8px;">TOTAL ASSIGNÉS</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 2: À Diagnostiquer --}}
        <div class="col">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="rounded-4 p-3 me-3 d-flex align-items-center justify-content-center" style="background: #fff7ed; width: 56px; height: 56px; flex-shrink: 0;">
                        <i class="fas fa-search text-warning"></i>
                    </div>
                    <div>
                        <div class="h4 fw-bolder mb-0 text-dark" style="font-weight: 800;">{{ $aDiagnostiquer }}</div>
                        <div class="text-muted text-uppercase fw-bold" style="font-size: 0.55rem; letter-spacing: 0.8px;">À DIAGNOSTIQUER</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: En Réparation --}}
        <div class="col">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="rounded-4 p-3 me-3 d-flex align-items-center justify-content-center" style="background: #ecfdf5; width: 56px; height: 56px; flex-shrink: 0;">
                        <i class="fas fa-tools text-success"></i>
                    </div>
                    <div>
                        <div class="h4 fw-bolder mb-0 text-dark" style="font-weight: 800;">{{ $enReparation }}</div>
                        <div class="text-muted text-uppercase fw-bold" style="font-size: 0.55rem; letter-spacing: 0.8px;">EN RÉPARATION</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Attente Pièces --}}
        <div class="col">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="rounded-4 p-3 me-3 d-flex align-items-center justify-content-center" style="background: #fef2f2; width: 56px; height: 56px; flex-shrink: 0;">
                        <i class="fas fa-hourglass-half text-danger"></i>
                    </div>
                    <div>
                        <div class="h4 fw-bolder mb-0 text-dark" style="font-weight: 800;">{{ $attentePieces }}</div>
                        <div class="text-muted text-uppercase fw-bold" style="font-size: 0.55rem; letter-spacing: 0.8px;">ATTENTE PIÈCES</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 5: Terminés (Mois) --}}
        <div class="col">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="rounded-4 p-3 me-3 d-flex align-items-center justify-content-center" style="background: #f0f9ff; width: 56px; height: 56px; flex-shrink: 0;">
                        <i class="fas fa-check-double text-info"></i>
                    </div>
                    <div>
                        <div class="h4 fw-bolder mb-0 text-dark" style="font-weight: 800;">{{ $terminesMois }}</div>
                        <div class="text-muted text-uppercase fw-bold" style="font-size: 0.55rem; letter-spacing: 0.8px;">TERMINÉS (MOIS)</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- GAUCHE : Dossiers à Diagnostiquer --}}
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark d-flex align-items-center">
                        <span class="p-2 bg-warning bg-opacity-10 rounded-3 me-2"><i class="fas fa-search text-warning"></i></span>
                        À Diagnostiquer
                    </h6>
                    <span class="badge bg-soft-warning text-warning rounded-pill px-3">{{ $dossiersDiagnostique->count() }} dossiers</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase fw-bold">
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
                                        <div class="fw-bolder text-dark" style="font-weight: 800;">#{{ $d->num_dossier }}</div>
                                        <div class="small text-muted">{{ $d->created_at ? $d->created_at->diffForHumans() : '—' }}</div>
                                    </td>
                                    <td>
                                        <div class="small fw-bold text-dark">{{ $d->appareil->modele ?? 'Appareil' }}</div>
                                        <div class="small text-muted">IMEI: {{ $d->imei }}</div>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('diagnostics.create', $d->id) }}" class="btn btn-sm btn-warning rounded-pill px-3 shadow-sm fw-bold" style="font-size: 0.7rem;">DIAGNOSTIQUER</a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center py-5 text-muted small">Aucun diagnostic en attente.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- DROITE : Dossiers à Réparer --}}
        <div class="col-xl-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark d-flex align-items-center">
                        <span class="p-2 bg-success bg-opacity-10 rounded-3 me-2"><i class="fas fa-tools text-success"></i></span>
                        À Réparer
                    </h6>
                    <span class="badge bg-soft-success text-success rounded-pill px-3">{{ $dossiersReparation->count() }} dossiers</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase fw-bold">
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
                                        <div class="fw-bolder text-dark" style="font-weight: 800;">#{{ $d->num_dossier }}</div>
                                        <div class="small text-muted">{{ $d->created_at ? $d->created_at->diffForHumans() : '—' }}</div>
                                    </td>
                                    <td>
                                        <div class="small fw-bold text-dark">{{ $d->appareil->modele ?? 'Appareil' }}</div>
                                        <div class="small text-muted">IMEI: {{ $d->imei }}</div>
                                    </td>
                                    <td class="text-end pe-4 d-flex justify-content-end gap-2">
                                        @if($d->diagnostic)
                                            <a href="{{ route('diagnostics.show', $d->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-3 fw-bold" style="font-size: 0.7rem;">
                                                <i class="fas fa-eye me-1"></i> DIAG
                                            </a>
                                        @endif
                                        <a href="{{ route('interventions.create', $d->id) }}" class="btn btn-sm btn-success rounded-pill px-3 shadow-sm fw-bold" style="font-size: 0.7rem;">RÉPARER</a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center py-5 text-muted small">Aucune réparation en attente.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- NOUVEAU : Travaux Terminés / Historique --}}
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark d-flex align-items-center">
                        <span class="p-2 bg-info bg-opacity-10 rounded-3 me-2"><i class="fas fa-history text-info"></i></span>
                        Mes Travaux Terminés
                    </h6>
                    <a href="{{ route('technicien.tickets') }}" class="btn btn-sm btn-outline-info rounded-pill px-3 fw-bold" style="font-size: 0.7rem;">VOIR TOUT</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase fw-bold">
                                <tr>
                                    <th class="ps-4">Dossier</th>
                                    <th>Client</th>
                                    <th>Appareil</th>
                                    <th>Statut final</th>
                                    <th class="text-end pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dossiersTermines as $d)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bolder text-dark" style="font-weight: 800;">#{{ $d->num_dossier }}</div>
                                        <div class="small text-muted">Mis à jour le {{ $d->updated_at->format('d/m/Y') }}</div>
                                    </td>
                                    <td>
                                        <div class="small fw-bold">{{ $d->client->name ?? '—' }}</div>
                                    </td>
                                    <td>
                                        <div class="small fw-bold">{{ $d->appareil->modele ?? '—' }}</div>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill bg-light text-dark border px-3" style="font-size: 0.65rem;">
                                            {{ $d->statut }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="{{ route('diagnostics.show', $d->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm fw-bold" style="font-size: 0.7rem;">CONSULTER</a>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center py-4 text-muted small">Aucun travail terminé récemment.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
@endsection
