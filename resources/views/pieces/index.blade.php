@extends('layouts.app')

@section('title', 'Gestion du stock')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/stock.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800 fw-bold">Gestion du stock</h1>
            <small class="text-muted">Suivi des pièces de rechange disponibles</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('stock.mouvements') }}" class="btn btn-outline-secondary px-3">
                <i class="fas fa-history me-1"></i> Historique
            </a>
            <a href="{{ route('stock.create') }}" class="btn btn-primary px-3 shadow">
                <i class="fas fa-plus me-1"></i> Nouvelle Pièce
            </a>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="card shadow-sm mb-4 border-0" style="border-radius: 10px;">
        <div class="card-body p-4">
            <form action="{{ route('pieces.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small fw-bold">Rechercher une pièce</label>
                    <input type="text" name="search" class="form-control" placeholder="Nom ou référence..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Catégorie</label>
                    <select name="categorie" class="form-select">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('categorie') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Filtrer</button>
                    <a href="{{ route('pieces.index') }}" class="btn btn-outline-secondary px-3"><i class="fas fa-sync-alt"></i></a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tableau --}}
    <div class="card shadow-sm border-0 overflow-hidden" style="border-radius: 10px;">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-light">
                    <tr class="text-muted small text-uppercase">
                        <th class="ps-4">ID</th>
                        <th>Désignation / Réf.</th>
                        <th>Catégorie</th>
                        <th class="text-center">Stock</th>
                        <th class="text-end">Prix Unitaire</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pieces as $piece)
                    <tr>
                        <td class="ps-4 text-muted">#{{ $piece->id }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $piece->nom }}</div>
                            <small class="text-muted text-uppercase" style="font-size: 0.7rem;">{{ $piece->reference }}</small>
                        </td>
                        <td><span class="text-muted small">{{ $piece->categorie }}</span></td>
                        <td class="text-center">
                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">{{ $piece->stock_actuel }}</span>
                        </td>
                        <td class="text-end fw-bold">{{ number_format($piece->prix_vente, 2, ',', ' ') }} TND</td>
                        <td class="text-end pe-4">
                            <a href="{{ route('stock.edit', $piece->id) }}" class="btn btn-link text-primary p-1"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('stock.destroy', $piece->id) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-link text-danger p-1" onclick="return confirm('Supprimer cette pièce ?')"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
