@extends('layouts.app')

@section('title', 'Gestion du Stock')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/stock.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Gestion de Stock & Pièces</h1>
            <small class="text-muted">Inventaire des pièces détachées et accessoires</small>
        </div>
        @if(auth()->user()->role !== 'Technicien')
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#addPieceModal">
                <i class="fas fa-plus-circle me-1"></i> Ajouter Pièce
            </button>
            <a href="{{ route('stock.mouvements') }}" class="btn btn-outline-primary shadow-sm">
                <i class="fas fa-history me-1"></i> Historique
            </a>
            <a href="{{ route('stock.mouvements.create') }}" class="btn btn-primary shadow-sm">
                <i class="fas fa-exchange-alt me-1"></i> Mouvement
            </a>
        </div>
        @endif
    </div>

    {{-- Modal Ajouter Pièce --}}
    <div class="modal fade" id="addPieceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow" style="border-radius: 15px;">
                <div class="modal-header bg-success text-white border-0 py-3" style="border-top-left-radius: 15px; border-top-right-radius: 15px;">
                    <h5 class="modal-title fw-bold"><i class="fas fa-plus-circle me-2"></i> Ajouter une nouvelle pièce</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('stock.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label fw-bold small text-uppercase">Désignation <span class="text-danger">*</span></label>
                                <input type="text" name="nom" class="form-control" placeholder="Ex: Écran LCD iPhone 13" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-uppercase">Référence</label>
                                <input type="text" name="reference" class="form-control" placeholder="REF-001">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Catégorie <span class="text-danger">*</span></label>
                                <select name="categorie" class="form-select" required>
                                    <option value="">-- Sélectionner --</option>
                                    @foreach(\App\Models\Piece::CATEGORIES as $cat)
                                        <option value="{{ $cat }}">{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Prix Unitaire (TTC) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="prix_unitaire" class="form-control" value="0" required>
                                    <span class="input-group-text">DA</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Quantité Initiale <span class="text-danger">*</span></label>
                                <input type="number" name="quantite" class="form-control" value="0" min="0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small text-uppercase">Seuil d'Alerte <span class="text-danger">*</span></label>
                                <input type="number" name="seuil_alerte" class="form-control" value="5" min="1" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-success rounded-pill px-5 fw-bold shadow-sm">ENREGISTRER</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="card shadow-sm mb-4 border-0" style="border-radius: 12px;">
        <div class="card-body p-4">
            <form action="{{ route('stock.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small fw-bold text-muted">Rechercher une pièce</label>
                    <input type="text" name="search" class="form-control bg-light border-0" placeholder="Nom ou référence..." value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-muted">Catégorie</label>
                    <select name="categorie" class="form-select bg-light border-0">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('categorie') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1 fw-bold">Filtrer</button>
                    <a href="{{ route('stock.index') }}" class="btn btn-light border" title="Réinitialiser">
                        <i class="fas fa-sync-alt"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tableau des pièces --}}
    <div class="card shadow border-0 overflow-hidden" style="border-radius: 15px;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase fw-bold">
                    <tr>
                        <th class="ps-4" style="width: 80px;">ID</th>
                        <th>Désignation / Réf.</th>
                        <th class="text-center">Catégorie</th>
                        <th class="text-center">Stock</th>
                        <th class="text-end">Prix Unitaire</th>
                        @if(auth()->user()->role !== 'Technicien')
                        <th class="text-center">Actif</th>
                        <th class="text-end pe-4">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($pieces as $piece)
                    @php
                        $stock = $piece->quantite;
                        $lowStock = $stock <= $piece->seuil_alerte;
                    @endphp
                    <tr>
                        <td class="ps-4 text-muted small">#{{ $piece->id }}</td>
                        <td>
                            <div class="fw-bold text-dark">{{ $piece->nom }}</div>
                            <div class="text-muted small" style="font-size: 0.75rem;">{{ $piece->reference }}</div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-muted border-0 py-2 px-3 fw-normal" style="font-size: 0.7rem;">{{ $piece->categorie ?? 'N/A' }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge rounded-circle {{ $lowStock ? 'bg-danger' : 'bg-success' }} bg-opacity-10 {{ $lowStock ? 'text-danger' : 'text-success' }} d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                {{ $stock }}
                            </span>
                        </td>
                        <td class="text-end fw-bold text-dark">
                            {{ number_format($piece->prix_unitaire, 2, ',', ' ') }} <span class="small text-muted fw-normal">{{ $parametre->devise ?? 'TND' }}</span>
                        </td>
                        @if(auth()->user()->role !== 'Technicien')
                        <td class="text-center">
                            <form action="{{ route('stock.toggle', $piece->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <div class="form-check form-switch d-inline-block">
                                    <input class="form-check-input cursor-pointer" type="checkbox" 
                                        onchange="this.form.submit()" {{ $piece->actif ? 'checked' : '' }}
                                        style="width: 2.2em; height: 1.1em;">
                                </div>
                            </form>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('stock.edit', $piece->id) }}" class="text-primary" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('stock.destroy', $piece->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Supprimer cette pièce ?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-link p-0 text-danger border-0" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-boxes fa-3x text-muted opacity-25 mb-3"></i>
                            <p class="text-muted">Aucune pièce trouvée dans l'inventaire.</p>
                            @if(auth()->user()->role !== 'Technicien')
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPieceModal">
                                Ajouter ma première pièce
                            </button>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
