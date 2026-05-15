@extends('layouts.app')

@section('title', 'Historique des Mouvements Stock')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/stock.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Mouvements de Stock</h1>
            <small class="text-muted">Traçabilité complète des entrées et sorties</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('stock.index') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-boxes me-1"></i> Retour Inventaire
            </a>
            <a href="{{ route('stock.mouvements.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-plus me-1"></i> Nouveau Mouvement
            </a>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="card shadow mb-4 border-0" style="border-radius: 12px;">
        <div class="card-body py-3">
            <form action="{{ route('stock.mouvements') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="small text-muted fw-bold text-uppercase">Filtrer par Pièce</label>
                    <select name="piece_id" class="form-select">
                        <option value="">Toutes les pièces</option>
                        @foreach($pieces as $p)
                            <option value="{{ $p->id }}" {{ request('piece_id') == $p->id ? 'selected' : '' }}>
                                [{{ $p->reference }}] {{ $p->nom }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="small text-muted fw-bold text-uppercase">Type</label>
                    <select name="type" class="form-select">
                        <option value="">Tous</option>
                        <option value="ENTREE" {{ request('type') == 'ENTREE' ? 'selected' : '' }}>Entrée</option>
                        <option value="SORTIE" {{ request('type') == 'SORTIE' ? 'selected' : '' }}>Sortie</option>
                        <option value="AJUSTEMENT" {{ request('type') == 'AJUSTEMENT' ? 'selected' : '' }}>Ajustement</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i></button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tableau --}}
    <div class="card shadow border-0 overflow-hidden" style="border-radius: 15px;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase fw-bold">
                    <tr>
                        <th class="ps-4">Date & Heure</th>
                        <th>Pièce</th>
                        <th class="text-center">Type</th>
                        <th class="text-center">Quantité</th>
                        <th>Motif / Origine</th>
                        <th>Opérateur</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mouvements as $m)
                    <tr>
                        <td class="ps-4 small">
                            <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($m->created_at)->format('d/m/Y') }}</div>
                            <div class="text-muted" style="font-size: 0.7rem;">{{ \Carbon\Carbon::parse($m->created_at)->format('H:i') }}</div>
                        </td>
                        <td>
                            <div class="fw-bold">{{ $m->piece->nom ?? 'Pièce inconnue' }}</div>
                            <small class="text-muted font-monospace">{{ $m->piece->reference ?? '' }}</small>
                        </td>
                        <td class="text-center">
                            @php
                                $colors = ['ENTREE' => 'success', 'SORTIE' => 'danger', 'AJUSTEMENT' => 'warning'];
                                $c = $colors[$m->type] ?? 'secondary';
                            @endphp
                            <span class="badge bg-{{ $c }} rounded-pill px-3" style="font-size: 0.7rem;">{{ $m->type }}</span>
                        </td>
                        <td class="text-center fw-bold fs-5 {{ $m->type == 'SORTIE' ? 'text-danger' : 'text-success' }}">
                            {{ $m->type == 'SORTIE' ? '-' : '+' }}{{ $m->quantite }}
                        </td>
                        <td>
                            <div class="small">{{ $m->motif }}</div>
                            @if($m->dossier_id)
                                <a href="{{ route('dossiers.show', $m->dossier_id) }}" class="badge bg-light text-primary border text-decoration-none">
                                    Dossier #{{ $m->dossier->num_dossier ?? $m->dossier_id }}
                                </a>
                            @endif
                        </td>
                        <td>
                            <div class="small fw-bold">{{ $m->user->name ?? 'Système' }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            Aucun mouvement enregistré.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($mouvements->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $mouvements->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
