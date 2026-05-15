@extends('layouts.app')

@section('title', 'Ajouter une Pièce')

@section('content')
<div class="container" style="max-width: 800px;">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('stock.index') }}" class="btn btn-outline-secondary btn-sm me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="h3 mb-0 text-gray-800">Nouvelle Pièce Détachée</h1>
            <small class="text-muted">Ajouter un nouvel article à l'inventaire</small>
        </div>
    </div>

    <div class="card shadow border-0" style="border-radius: 15px;">
        <div class="card-body p-4">
            <form action="{{ route('stock.store') }}" method="POST">
                @csrf
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
                        <small class="text-muted">Alerte quand le stock est inférieur ou égal à ce chiffre.</small>
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm fw-bold">
                            <i class="fas fa-save me-2"></i> ENREGISTRER LA PIÈCE
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
