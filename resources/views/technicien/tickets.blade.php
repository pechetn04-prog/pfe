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

    {{-- Section Filtres (Premium Style) --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form method="GET" class="row g-3 align-items-center">
                <div class="col-md-4">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted">
                            <i class="fas fa-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0" placeholder="N° Dossier, IMEI ou Client..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="statut" class="form-select form-select-sm">
                        <option value="">Tous les statuts</option>
                        @foreach($statuts as $val => $label)
                        <option value="{{ $val }}" {{ request('statut') == $val ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5 d-flex gap-2 justify-content-md-end">
                    <button type="submit" class="btn btn-sm btn-primary px-4 rounded-pill">
                        <i class="fas fa-filter me-1"></i> Filtrer
                    </button>
                    <a href="{{ route('technicien.tickets') }}" class="btn btn-sm btn-light px-4 rounded-pill">
                        <i class="fas fa-undo me-1"></i> Réinitialiser
                    </a>
                </div>
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
                        <th>IMEI</th>
                        <th>Garantie</th>
                        <th>Statut</th>
                        <th>Date réception</th>
                        <th class="text-center">Action</th>
                        <th class="text-end pe-4">Historique</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dossiers as $dossier)
                    <tr>
                        <td class="ps-4 fw-bold text-primary">#{{ $dossier->num_dossier }}</td>
                        <td>
                            <div class="fw-bold text-dark small">{{ $dossier->client->name ?? '—' }}</div>
                            <div class="text-muted small" style="font-size: 0.7rem;">{{ $dossier->client->telephone ?? '' }}</div>
                        </td>
                        <td class="small fw-bold">{{ $dossier->imei }}</td>
                        <td>
                            @php
                                if ($dossier->garantie_annulee) {
                                    $garantieColor = 'warning';
                                    $garantieText = 'GARANTIE EXCLUE';
                                } elseif ($dossier->sous_garantie) {
                                    $garantieColor = 'success';
                                    $garantieText = 'SOUS GARANTIE';
                                } else {
                                    $garantieColor = 'danger';
                                    $garantieText = 'HORS GARANTIE';
                                }
                            @endphp
                            <span class="badge bg-{{ $garantieColor }} bg-opacity-10 text-{{ $garantieColor }} border border-{{ $garantieColor }} border-opacity-25 rounded-pill" style="font-size: 0.6rem; font-weight: 800;">
                                {{ $garantieText }}
                            </span>
                        </td>
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
                                    'CLOTURE'          => 'bg-dark',
                                    'LIVRE'            => 'bg-success',
                                ];
                            @endphp
                            <span class="badge {{ $map[$dossier->statut] ?? 'bg-secondary' }} rounded-pill px-3">
                                {{ $statuts[$dossier->statut] ?? $dossier->statut }}
                            </span>
                        </td>
                        <td class="small">{{ \Carbon\Carbon::parse($dossier->date_reception)->format('d/m/Y') }}</td>
                        
                        {{-- Colonne Action (Opérationnel) --}}
                        <td class="text-center">
                            @if(in_array($dossier->statut, ['AFFECTE', 'EN_DIAGNOSTIC']))
                                <a href="{{ route('diagnostics.create', $dossier->id) }}" class="btn btn-sm btn-warning rounded-pill px-3 fw-bold shadow-sm">
                                    <i class="fas fa-microscope me-1"></i> Diagnostiquer
                                </a>
                            @elseif($dossier->statut === 'EN_REPARATION')
                                <a href="{{ route('interventions.create', $dossier->id) }}" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold shadow-sm">
                                    <i class="fas fa-tools me-1"></i> Réparer
                                </a>
                            @else
                                <span class="text-muted small">Aucune action</span>
                            @endif
                        </td>

                        {{-- Colonne Historique (Consultation) --}}
                        <td class="text-end pe-4">
                            <div class="d-flex flex-column align-items-end gap-1">
                                @if($dossier->diagnostic)
                                    <a href="{{ route('diagnostics.show', $dossier->id) }}" class="btn btn-sm btn-outline-info rounded-pill px-2 fw-bold w-100" style="font-size: 0.65rem; max-width: 70px;" title="Consulter le diagnostic">
                                        <i class="fas fa-file-alt"></i> Diag
                                    </a>
                                @endif
                                @if($dossier->intervention)
                                    <a href="{{ route('interventions.show', $dossier->id) }}" class="btn btn-sm btn-outline-success rounded-pill px-2 fw-bold w-100" style="font-size: 0.65rem; max-width: 70px;" title="Consulter l'intervention">
                                        <i class="fas fa-check-double"></i> Interv
                                    </a>
                                @endif
                            </div>
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
