@extends('layouts.app')

@section('title', 'Mes Dossiers — Technicien')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold mb-0">Mes Dossiers</h1>
            <small class="text-muted">Dossiers qui vous sont assignés</small>
        </div>
    </div>

    {{-- Filtres par statut --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-2">
            <form method="GET" class="d-flex gap-2 align-items-center flex-wrap">
                <select name="statut" class="form-select form-select-sm" style="max-width: 220px;">
                    <option value="">Tous les statuts</option>
                    @foreach($statuts as $val => $label)
                    <option value="{{ $val }}" {{ request('statut') == $val ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-sm btn-primary px-4">Filtrer</button>
                <a href="{{ route('technicien.tickets') }}" class="btn btn-sm btn-outline-secondary">Réinitialiser</a>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="small text-muted text-uppercase">
                        <th class="ps-4">N° Dossier</th>
                        <th>Client</th>
                        <th>Panne déclarée</th>
                        <th>Statut</th>
                        <th>Date réception</th>
                        <th class="text-end pe-4">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dossiers as $dossier)
                    <tr>
                        <td class="ps-4 fw-bold text-primary">#{{ $dossier->num_dossier }}</td>
                        <td>{{ $dossier->client->name ?? '—' }}</td>
                        <td>{{ Str::limit($dossier->panne_declaree, 50) }}</td>
                        <td>
                            @php
                                $map = [
                                    'AFFECTE'          => 'bg-secondary',
                                    'EN_DIAGNOSTIC'    => 'bg-info text-dark',
                                    'EN_ATTENTE_DEVIS' => 'bg-warning text-dark',
                                    'EN_REPARATION'    => 'bg-primary',
                                    'ATTENTE_PIECE'    => 'bg-dark',
                                    'REPARE'           => 'bg-success',
                                    'IRREPARABLE'      => 'bg-danger',
                                ];
                            @endphp
                            <span class="badge {{ $map[$dossier->statut] ?? 'bg-secondary' }} rounded-pill px-3">
                                {{ $statuts[$dossier->statut] ?? $dossier->statut }}
                            </span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($dossier->date_reception)->format('d/m/Y') }}</td>
                        <td class="text-end pe-4 d-flex justify-content-end gap-2">
                            @if($dossier->diagnostic)
                                <a href="{{ route('diagnostics.show', $dossier->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-3 fw-bold" title="Consulter le diagnostic">
                                    <i class="fas fa-eye me-1"></i> Diag
                                </a>
                            @endif

                            @if($dossier->intervention)
                                <a href="{{ route('interventions.show', $dossier->id) }}" class="btn btn-sm btn-outline-success rounded-pill px-3 fw-bold" title="Consulter l'intervention">
                                    <i class="fas fa-wrench me-1"></i> Interv
                                </a>
                            @endif

                            @if(in_array($dossier->statut, ['AFFECTE', 'EN_DIAGNOSTIC']))
                                <a href="{{ route('diagnostics.create', $dossier->id) }}" class="btn btn-sm btn-warning rounded-pill px-3 fw-bold">
                                    <i class="fas fa-microscope me-1"></i> Diagnostic
                                </a>
                            @elseif($dossier->statut === 'EN_REPARATION')
                                <a href="{{ route('interventions.create', $dossier->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold">
                                    <i class="fas fa-tools me-1"></i> Intervention
                                </a>
                            @else
                                <a href="{{ route('dossiers.show', $dossier->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold">
                                    <i class="fas fa-eye me-1"></i> Voir
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fas fa-inbox fa-2x mb-3 d-block"></i>
                            Aucun dossier trouvé.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($dossiers->hasPages())
        <div class="card-footer bg-white border-0">
            {{ $dossiers->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
