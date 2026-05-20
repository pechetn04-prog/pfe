@extends('layouts.app')

@section('title', 'Historique des Mouvements Stock')



@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mouvements-title mb-0">Mouvements de Stock</h1>
                <small class="text-muted fw-bold">Traçabilité complète des flux d'inventaire</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('stock.index') }}" class="btn btn-light border shadow-sm">
                    <i class="fas fa-boxes me-1"></i> Inventaire
                </a>
            </div>
        </div>

        {{-- Filtres --}}
        <div class="filter-mouv shadow-sm">
            <form action="{{ route('stock.mouvements') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="small text-muted fw-bold text-uppercase mb-2">Filtrer par Pièce</label>
                    <select name="piece_id" class="form-select">
                        <option value="">Toutes les pièces détachées</option>
                        @foreach($pieces as $p)
                            <option value="{{ $p->id }}" {{ request('piece_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->nom }} ({{ $p->reference }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="small text-muted fw-bold text-uppercase mb-2">Type de Flux</label>
                    <select name="type" class="form-select">
                        <option value="">Tous les types</option>
                        <option value="entree" {{ request('type') == 'entree' ? 'selected' : '' }}>Entrée en stock</option>
                        <option value="sortie" {{ request('type') == 'sortie' ? 'selected' : '' }}>Sortie de stock</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1 shadow-sm">FILTRER LES FLUX</button>
                    <a href="{{ route('stock.mouvements') }}" class="btn btn-light border-0 fw-bold" style="background: #f1f5f9; color: #64748b;">
                        <i class="fas fa-undo me-2"></i> RAZ
                    </a>
                </div>
            </form>
        </div>

        {{-- Tableau --}}
        <div class="card mouvements-card overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 table-mouvements">
                    <thead>
                        <tr>
                            <th class="ps-4">Horodatage</th>
                            <th>Désignation Pièce</th>
                            <th class="text-center">Type</th>
                            <th class="text-center">Quantité</th>
                            <th>Motif / Dossier</th>
                            <th class="pe-4">Opérateur</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mouvements as $m)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($m->created_at)->format('d/m/Y') }}</div>
                                    <div class="text-muted small">{{ \Carbon\Carbon::parse($m->created_at)->format('H:i') }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $m->piece->nom ?? '—' }}</div>
                                    <span class="piece-ref">{{ $m->piece->reference ?? '—' }}</span>
                                </td>
                                <td class="text-center">
                                    <span class="mouv-type-badge badge-{{ $m->type }}">
                                        {{ $m->type }}
                                    </span>
                                </td>
                                <td class="text-center qty-text {{ $m->type == 'sortie' ? 'qty-minus' : 'qty-plus' }}">
                                    {{ $m->type == 'sortie' ? '-' : '+' }}{{ $m->quantite }}
                                </td>
                                <td>
                                    @php
                                        // Nettoyage du motif redondant (retirer la mention répétitive du dossier si le badge est présent)
                                        $displayMotif = $m->motif;
                                        if ($m->reference_type === 'App\Models\Intervention' && $m->reference && $m->reference->dossier) {
                                            $displayMotif = str_replace(' pour dossier #' . $m->reference->dossier->num_dossier, '', $displayMotif);
                                        }
                                    @endphp
                                    <div class="small text-muted mb-1" style="font-size: 0.85rem; font-weight: 500; color: #475569 !important;">{{ $displayMotif }}</div>
                                    @if($m->reference_type === 'App\Models\Intervention' && $m->reference)
                                        <a href="{{ route('dossiers.show', $m->reference->dossier_id) }}"
                                            class="badge-dossier text-decoration-none">
                                            <i class="fas fa-folder-open"></i> Dossier #{{ $m->reference->dossier->num_dossier ?? '???' }}
                                        </a>
                                    @endif
                                </td>
                                <td class="pe-4">
                                    <div class="op-name">{{ $m->user->name ?? 'Système' }}</div>
                                    <div class="small text-muted">{{ $m->user->role ?? '' }}</div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted fw-bold">
                                    <i class="fas fa-history me-2"></i> Aucun mouvement enregistré.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($mouvements->hasPages())
                <div class="p-4 bg-white border-top">
                    {{ $mouvements->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection