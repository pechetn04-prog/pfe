@extends('layouts.app')

@section('title', 'Ajouter une Pièce')

@section('content')
<div class="container py-4 max-w-850">
    {{-- Header avec retour --}}
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('stock.index') }}" class="btn bg-white shadow-sm me-3 border-0 d-flex align-items-center justify-content-center btn-premium-back">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="h4 fw-bold mb-0 text-premium-navy">Nouvelle Pièce Détachée</h1>
            <small class="text-muted fw-medium">Ajouter un nouvel article à l'inventaire</small>
        </div>
    </div>

    <div class="card border-0 shadow-sm card-premium-rounded overflow-hidden">
        <div class="card-header bg-white border-0 py-3 ps-4">
            <h6 class="fw-bold mb-0 text-success"><i class="fas fa-plus-circle me-2"></i> Formulaire de création</h6>
        </div>
        <div class="card-body p-4 ps-4 pe-4">
            <form action="{{ route('stock.store') }}" method="POST">
                @csrf
                <div class="row g-4">
                    <div class="col-md-8">
                        <label class="form-label fw-bold small text-muted text-uppercase mb-2 text-uppercase-small">DÉSIGNATION <span class="text-danger">*</span></label>
                        <input type="text" name="nom" class="form-control form-control-lg border-0 bg-light rounded-pill px-4 input-premium-text" placeholder="Ex: Écran LCD iPhone 13" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted text-uppercase mb-2 text-uppercase-small">RÉFÉRENCE</label>
                        <input type="text" name="reference" class="form-control form-control-lg border-0 bg-light rounded-pill px-4 input-premium-text" placeholder="REF-001">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-muted text-uppercase mb-2 text-uppercase-small">CATÉGORIE <span class="text-danger">*</span></label>
                        <select name="categorie" class="form-select form-select-lg border-0 bg-light rounded-pill px-4 input-premium-text" required>
                            <option value="">-- Sélectionner --</option>
                            @foreach(\App\Models\Piece::CATEGORIES as $cat)
                                <option value="{{ $cat }}">{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-muted text-uppercase mb-2 text-uppercase-small">PRIX UNITAIRE (TTC) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="0.001" name="prix_unitaire" class="form-control form-control-lg border-0 bg-light rounded-pill-start px-4 input-premium-text" value="0" required>
                            <span class="input-group-text border-0 bg-success bg-opacity-10 text-success fw-bold px-4 input-addon-pill-end">{{ $parametre->devise ?? 'DT' }}</span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-muted text-uppercase mb-2 text-uppercase-small">QUANTITÉ INITIALE <span class="text-danger">*</span></label>
                        <input type="number" name="quantite" class="form-control form-control-lg border-0 bg-light rounded-pill px-4 input-premium-text" value="0" min="0" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-muted text-uppercase mb-2 text-uppercase-small">SEUIL D'ALERTE <span class="text-danger">*</span></label>
                        <input type="number" name="seuil_alerte" class="form-control form-control-lg border-0 bg-light rounded-pill px-4 input-premium-text" value="5" min="1" required>
                        <small class="text-muted mt-2 d-block px-2 text-uppercase-small"><i class="fas fa-bell me-1 text-warning"></i> Alerte quand le stock est inférieur ou égal à ce chiffre.</small>
                    </div>

                    <div class="col-12 mt-5">
                        <button type="submit" class="btn btn-success btn-lg w-100 rounded-pill fw-bold shadow-sm transition-all hover-scale py-3">
                            <i class="fas fa-save me-2"></i> ENREGISTRER LA PIÈCE
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
