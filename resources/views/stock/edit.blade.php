@extends('layouts.app')

@section('title', 'Modifier la Pièce')

@section('content')
<div class="container py-4" style="max-width: 850px;">
    {{-- Header avec retour --}}
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('stock.index') }}" class="btn bg-white shadow-sm me-3 border-0 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px; border-radius: 12px; color: #1e69ff;">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="h4 fw-bold mb-0" style="color: #1a2332; letter-spacing: -0.5px;">Modifier : {{ $piece->nom }}</h1>
            <small class="text-muted fw-medium">Référence : {{ $piece->reference ?? '—' }}</small>
        </div>
    </div>

    <div class="card border-0 shadow-sm" style="border-radius: 20px; overflow: hidden;">
        <div class="card-header bg-white border-0 py-3 ps-4">
            <h6 class="fw-bold mb-0 text-primary"><i class="fas fa-edit me-2"></i> Informations de l'article</h6>
        </div>
        <div class="card-body p-4 ps-4 pe-4">
            <form action="{{ route('stock.update', $piece->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="row g-4">
                    <div class="col-md-8">
                        <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.5px;">DÉSIGNATION <span class="text-danger">*</span></label>
                        <input type="text" name="nom" class="form-control form-control-lg border-0 bg-light rounded-pill px-4" value="{{ $piece->nom }}" required style="font-size: 0.95rem;">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.5px;">RÉFÉRENCE</label>
                        <input type="text" name="reference" class="form-control form-control-lg border-0 bg-light rounded-pill px-4" value="{{ $piece->reference }}" style="font-size: 0.95rem;">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.5px;">CATÉGORIE <span class="text-danger">*</span></label>
                        <select name="categorie" class="form-select form-select-lg border-0 bg-light rounded-pill px-4" required style="font-size: 0.95rem;">
                            @foreach(\App\Models\Piece::CATEGORIES as $cat)
                                <option value="{{ $cat }}" {{ $piece->categorie == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.5px;">PRIX UNITAIRE (TTC) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" step="0.001" name="prix_unitaire" class="form-control form-control-lg border-0 bg-light rounded-pill-start px-4" value="{{ $piece->prix_unitaire }}" required style="font-size: 0.95rem; border-top-right-radius: 0; border-bottom-right-radius: 0;">
                            <span class="input-group-text border-0 bg-primary bg-opacity-10 text-primary fw-bold px-4" style="border-top-right-radius: 50rem; border-bottom-right-radius: 50rem; font-size: 0.85rem;">{{ $parametre->devise ?? 'DT' }}</span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.5px;">STOCK ACTUEL</label>
                        <input type="number" class="form-control form-control-lg border-0 bg-white border rounded-pill px-4" value="{{ $piece->quantite }}" readonly disabled style="font-size: 0.95rem; opacity: 0.7;">
                        <small class="text-muted mt-2 d-block px-2" style="font-size: 0.7rem;"><i class="fas fa-info-circle me-1"></i> Utilisez les mouvements de stock pour ajuster la quantité.</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-muted text-uppercase mb-2" style="font-size: 0.65rem; letter-spacing: 0.5px;">SEUIL D'ALERTE <span class="text-danger">*</span></label>
                        <input type="number" name="seuil_alerte" class="form-control form-control-lg border-0 bg-light rounded-pill px-4" value="{{ $piece->seuil_alerte }}" min="1" required style="font-size: 0.95rem;">
                    </div>

                    <div class="col-12 mt-5 text-center">
                        <button type="submit" class="btn btn-primary rounded-pill fw-bold shadow-sm transition-all hover-scale px-5 py-2">
                            <i class="fas fa-check-circle me-2"></i> METTRE À JOUR LA PIÈCE
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .rounded-pill-start { border-top-left-radius: 50rem !important; border-bottom-left-radius: 50rem !important; }
    .hover-scale:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(30, 105, 255, 0.2) !important; }
    .form-control:focus, .form-select:focus { background-color: #ffffff !important; box-shadow: 0 0 0 4px rgba(30, 105, 255, 0.1) !important; }
</style>
@endsection
