@extends('layouts.app')

@section('title', 'Nouveau Mouvement Stock')

@section('content')
<div class="container" style="max-width: 700px;">
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('stock.mouvements') }}" class="btn btn-outline-secondary btn-sm me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="h3 mb-0 text-gray-800">Enregistrer un Mouvement</h1>
            <small class="text-muted">Enregistrement d'une entrée ou sortie de stock</small>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm">
            <ul class="mb-0">@foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach</ul>
        </div>
    @endif

    <div class="card shadow border-0" style="border-radius: 15px;">
        <div class="card-body p-4">
            <form action="{{ route('stock.mouvements.store') }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label class="form-label fw-bold small text-uppercase">1. Sélectionner la Pièce <span class="text-danger">*</span></label>
                    <select name="piece_id" class="form-select form-select-lg @error('piece_id') is-invalid @enderror" required>
                        <option value="">-- Choisir une pièce --</option>
                        @foreach($pieces as $p)
                            <option value="{{ $p->id }}" {{ (request('piece_id') == $p->id || old('piece_id') == $p->id) ? 'selected' : '' }}>
                                [{{ $p->reference }}] {{ $p->nom }} (Stock actuel : {{ $p->quantite }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-uppercase">2. Type de Mouvement <span class="text-danger">*</span></label>
                        <div class="d-flex gap-2">
                            <input type="radio" class="btn-check" name="type" id="type_entree" value="entree" checked>
                            <label class="btn btn-outline-success flex-grow-1" for="type_entree">
                                <i class="fas fa-plus-circle me-1"></i> ENTRÉE
                            </label>

                            <input type="radio" class="btn-check" name="type" id="type_sortie" value="sortie">
                            <label class="btn btn-outline-danger flex-grow-1" for="type_sortie">
                                <i class="fas fa-minus-circle me-1"></i> SORTIE
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-uppercase">3. Quantité <span class="text-danger">*</span></label>
                        <input type="number" name="quantite" class="form-control form-control-lg text-center fw-bold" value="1" min="1" required>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold small text-uppercase">4. Motif / Commentaire</label>
                    <textarea name="motif" class="form-control" rows="3" placeholder="Ex: Réapprovisionnement fournisseur, Erreur d'inventaire, Casse..."></textarea>
                </div>

                <div class="bg-light p-3 rounded-3 mb-4 border-start border-4 border-primary">
                    <div class="small text-muted"><i class="fas fa-info-circle me-1"></i> Note :</div>
                    <div class="small">Les mouvements liés aux dossiers SAV sont enregistrés automatiquement lors de la validation des interventions. N'utilisez ce formulaire que pour les entrées et sorties manuelles.</div>
                </div>

                <button type="submit" class="btn btn-primary btn-lg w-100 shadow-sm fw-bold">
                    <i class="fas fa-save me-2"></i> ENREGISTRER LE MOUVEMENT
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
