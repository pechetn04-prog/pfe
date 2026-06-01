@extends('layouts.app')

@section('title', 'Gestion du Stock')



@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800" style="font-weight: 800;">Gestion de Stock & Pièces</h1>
            <small class="text-muted fw-bold">Inventaire des pièces détachées et accessoires</small>
        </div>
        @if(auth()->user()->role !== 'Technicien')
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-success shadow-sm rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#addPieceModal">
                <i class="fas fa-plus-circle me-1"></i> Ajouter Pièce
            </button>
            <a href="{{ route('stock.mouvements') }}" class="btn btn-outline-primary shadow-sm rounded-pill px-3 fw-bold">
                <i class="fas fa-history me-1"></i> Historique
            </a>
            <button type="button" class="btn btn-primary shadow-sm rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#mouvementModal">
                <i class="fas fa-exchange-alt me-1"></i> Mouvement
            </button>
        </div>
        @endif
    </div>

    {{-- Cartes de Statistiques Stock --}}
    @if(auth()->user()->role !== 'Technicien')
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3 mb-4">
        <div class="col">
            <div class="card border-0 shadow-sm h-100 card-stock-box-15">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="bg-primary bg-opacity-10 p-3 rounded-3 me-3 text-primary">
                        <i class="fas fa-cubes fa-lg"></i>
                    </div>
                    <div>
                        <div class="h4 fw-bold mb-0">{{ $stats['total_items'] }}</div>
                        <div class="small text-muted fw-bold text-uppercase" style="font-size: 0.6rem;">Articles Total</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-danger card-stock-box-15">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="bg-danger bg-opacity-10 p-3 rounded-3 me-3 text-danger">
                        <i class="fas fa-exclamation-circle fa-lg"></i>
                    </div>
                    <div>
                        <div class="h4 fw-bold mb-0 text-danger">{{ $stats['out_of_stock'] }}</div>
                        <div class="small text-muted fw-bold text-uppercase" style="font-size: 0.6rem;">En Rupture</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm h-100 border-start border-4 border-warning card-stock-box-15">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="bg-warning bg-opacity-10 p-3 rounded-3 me-3 text-warning">
                        <i class="fas fa-bell fa-lg"></i>
                    </div>
                    <div>
                        <div class="h4 fw-bold mb-0 text-warning">{{ $stats['alerts_count'] }}</div>
                        <div class="small text-muted fw-bold text-uppercase" style="font-size: 0.6rem;">Alertes Seuil</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col">
            <div class="card border-0 shadow-sm h-100 card-stock-box-15">
                <div class="card-body d-flex align-items-center p-3">
                    <div class="bg-success bg-opacity-10 p-3 rounded-3 me-3 text-success">
                        <i class="fas fa-dollar-sign fa-lg"></i>
                    </div>
                    <div>
                        <div class="h4 fw-bold mb-0 text-success">{{ number_format($stats['total_value'], 3, ',', ' ') }}</div>
                        <div class="small text-muted fw-bold text-uppercase" style="font-size: 0.6rem;">Valeur ({{ $parametre->devise ?? 'DT' }})</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal Ajouter Pièce --}}
    <div class="modal fade" id="addPieceModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow modal-stock-box-15">
                <div class="modal-header bg-success text-white border-0 py-3 modal-header-radius-15">
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
                                    <input type="number" min="0" step="0.01" name="prix_unitaire" class="form-control" value="0" required>
                                    <span class="input-group-text">{{ $parametre->devise ?? 'DT' }}</span>
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
    <div class="card shadow border-0 overflow-hidden card-stock-box-15">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase fw-bold">
                    <tr>
                        <th>Désignation / Réf.</th>
                        <th class="text-center">Catégorie</th>
                        <th class="text-center">Stock</th>
                        <th class="text-end">Prix Unitaire</th>
                        @if(auth()->user()->role !== 'Technicien')
                        <th class="text-end pe-4">Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($pieces as $piece)
                    <tr>
                        
                        <td>
                            <div class="fw-bold text-dark">{{ $piece->nom }}</div>
                            <div class="text-muted small" style="font-size: 0.75rem;">{{ $piece->reference }}</div>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-muted border-0 py-2 px-3 fw-normal" style="font-size: 0.7rem;">{{ $piece->categorie ?? 'N/A' }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge rounded-circle {{ $piece->quantite <= $piece->seuil_alerte ? 'bg-danger text-danger' : 'bg-success text-success' }} bg-opacity-10 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                {{ $piece->quantite }}
                            </span>
                        </td>
                        <td class="text-end fw-bold text-dark">
                            {{ number_format($piece->prix_unitaire, 2, ',', ' ') }} <span class="small text-muted fw-normal">{{ $parametre->devise ?? 'TND' }}</span>
                        </td>
                        @if(auth()->user()->role !== 'Technicien')
                        <td class="text-end pe-4">
                            <div class="d-flex justify-content-end align-items-center gap-3">


                                <a href="{{ route('stock.edit', $piece->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" title="Modifier">
                                    <i class="fas fa-edit me-1"></i> Modifier
                                </a>
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
        @if($pieces->hasPages())
        <div class="p-3 bg-white border-top">
            {{ $pieces->links() }}
        </div>
        @endif
    </div>
    @include('stock.partials.mouvement-modal')
</div>
@endsection
