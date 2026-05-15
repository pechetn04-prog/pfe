@extends('layouts.app')

@section('title', 'Modifier la Pièce')

@section('content')
<div class="container" style="max-width: 800px;">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('stock.index') }}" class="btn btn-outline-secondary btn-sm me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="h3 mb-0 text-gray-800">Modifier : {{ $piece->nom }}</h1>
            <small class="text-muted">Mise à jour des informations de l'article</small>
        </div>
    </div>

    <div class="card shadow border-0" style="border-radius: 15px;">
        <div class="card-body p-4">
            <form action="{{ route('stock.update', $piece->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-bold small text-uppercase">Désignation <span class="text-danger">*</span></label>
                        <input type="text" name="nom" class="form-control" value="{{ $piece->nom }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-uppercase">Référence</label>
                        <input type="text" name="reference" class="form-control" value="{{ $piece->reference }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-uppercase">Catégorie <span class="text-danger">*</span></label>
                        <select name="categorie" class="form-select" required>
                            @foreach(\App\Models\Piece::CATEGORIES as $cat)
                                <option value="{{ $cat }}" {{ $piece->categorie == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-uppercase">Prix Unitaire (TTC) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="0.01" name="prix_unitaire" class="form-control" value="{{ $piece->prix_unitaire }}" required>
                            <span class="input-group-text">DA</span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-uppercase">Stock Actuel</label>
                        <input type="number" name="quantite" class="form-control bg-light" value="{{ $piece->quantite }}" readonly>
                        <small class="text-muted">Utilisez les mouvements de stock pour ajuster la quantité.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-uppercase">Seuil d'Alerte <span class="text-danger">*</span></label>
                        <input type="number" name="seuil_alerte" class="form-control" value="{{ $piece->seuil_alerte }}" min="1" required>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm fw-bold">
                            <i class="fas fa-save me-2"></i> METTRE À JOUR LA PIÈCE
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
