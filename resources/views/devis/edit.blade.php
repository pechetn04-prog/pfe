@extends('layouts.app')

@section('title', 'Modifier le Devis - Dossier #' . $dossier->num_dossier)



@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="h3 fw-bold mb-0 text-dark">Modifier le Devis</h1>
                    <p class="text-muted mb-0">Devis #{{ $devis->numero }} — Dossier #{{ $dossier->num_dossier }} — Client: {{ $dossier->client->name ?? '—' }}</p>
                </div>
                <a href="{{ route('dossiers.show', $dossier->id) }}" class="btn btn-light rounded-pill px-4 shadow-sm">
                    <i class="fas fa-arrow-left me-2"></i> Retour au dossier
                </a>
            </div>

            <form action="{{ route('devis.update', $devis->id) }}" method="POST" id="devisForm">
                @csrf
                @method('PUT')
                
                <div class="row g-4">
                    <div class="col-md-8">
                        {{-- Section 1 : Pièces --}}
                        <div class="card border-0 shadow-sm mb-4 card-devis-box-15">
                            <div class="card-header bg-white border-0 py-3 d-flex align-items-center">
                                <div class="icon-box-soft bg-soft-primary p-2 rounded-3 me-3">
                                    <i class="fas fa-microchip text-primary"></i>
                                </div>
                                <h6 class="fw-bold mb-0">1. Pièces détachées</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle" id="piecesTable">
                                        <thead class="bg-light">
                                            <tr class="small text-muted text-uppercase">
                                                <th class="w-col-35">Désignation</th>
                                                <th class="w-col-20">P.U (DT)</th>
                                                <th class="w-col-15">Qté</th>
                                                <th class="w-col-20">Total</th>
                                                <th class="text-end w-col-10">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($devis->pieces as $index => $piece)
                                            <tr class="piece-row">
                                                <td>
                                                    <select name="pieces[{{ $index }}][id]" class="form-select form-select-sm border-0 bg-light rounded-pill piece-select" required>
                                                        @foreach($pieces as $p)
                                                            <option value="{{ $p->id }}" {{ $piece->id == $p->id ? 'selected' : '' }} data-price="{{ $p->prix_unitaire }}">{{ $p->nom }} ({{ $p->reference }})</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.001" name="pieces[{{ $index }}][prix_unitaire]" class="form-control form-control-sm border-0 bg-light rounded-pill px-3 price-input text-center" value="{{ $piece->pivot->prix_unitaire ?? $piece->prix_unitaire }}" min="0" required>
                                                </td>
                                                <td>
                                                    <input type="number" name="pieces[{{ $index }}][quantite]" class="form-control form-control-sm border-0 bg-light rounded-pill px-3 qty-input text-center" value="{{ $piece->pivot->quantite ?? 1 }}" min="1" required>
                                                </td>
                                                <td class="fw-bold text-primary line-total">0.000</td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-sm btn-outline-danger border-0 remove-row"><i class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm rounded-pill fw-bold" id="addPieceBtn">
                                    <i class="fas fa-plus me-1"></i> Ajouter une pièce
                                </button>
                            </div>
                        </div>
 
                        {{-- Section 2 : Prestations --}}
                        <div class="card border-0 shadow-sm mb-4 card-devis-box-15">
                            <div class="card-header bg-white border-0 py-3 d-flex align-items-center">
                                <div class="icon-box-soft bg-soft-info p-2 rounded-3 me-3">
                                    <i class="fas fa-hand-holding-heart text-info"></i>
                                </div>
                                <h6 class="fw-bold mb-0">2. Prestations & Main d'œuvre</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle" id="laborsTable">
                                        <thead class="bg-light">
                                            <tr class="small text-muted text-uppercase">
                                                <th class="w-col-50">Type d'intervention</th>
                                                <th class="text-center w-col-30">Montant (DT)</th>
                                                <th class="text-end w-col-20">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($devis->tarifsMo as $index => $tarif)
                                            <tr class="labor-row">
                                                <td>
                                                    <select name="labors[{{ $index }}][id]" class="form-select form-select-sm border-0 bg-light rounded-pill labor-select" required>
                                                        @foreach($tarifsMo as $t)
                                                            <option value="{{ $t->id }}" {{ $tarif->id == $t->id ? 'selected' : '' }} data-price="{{ $t->montant }}">{{ $t->type_intervention }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.001" name="labors[{{ $index }}][montant]" class="form-control form-control-sm border-0 bg-light rounded-pill px-3 text-center labor-input" value="{{ $tarif->pivot->montant ?? $tarif->montant }}" min="0" required>
                                                </td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-sm btn-outline-danger border-0 remove-row"><i class="fas fa-trash"></i></button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-outline-info btn-sm rounded-pill fw-bold" id="addLaborBtn">
                                    <i class="fas fa-plus me-1"></i> Ajouter une prestation
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        {{-- Récapitulatif Financier --}}
                        <div class="card border-0 shadow-sm sticky-top card-devis-box-15 sticky-devis-top">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-dark mb-4">Résumé du Devis</h6>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Total Pièces</span>
                                    <span class="fw-bold" id="grand-total-pieces">0.000 DT</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom border-light">
                                    <span class="text-muted">Total Main d'œuvre</span>
                                    <span class="fw-bold" id="grand-total-labors">0.000 DT</span>
                                </div>

                                <div class="bg-primary bg-opacity-10 p-3 rounded-4 mb-4">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-primary">TOTAL TTC</span>
                                        <div class="text-end">
                                            <div class="h3 fw-bold text-primary mb-0" id="finalTotalDisplay">0.000</div>
                                            <input type="hidden" name="total_ttc" id="finalTotalInput">
                                            <small class="text-primary opacity-75">Dinar Tunisien (DT)</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-end">
                                    <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm mb-3 w-100">
                                        <i class="fas fa-save me-2"></i> ENREGISTRER LES MODIFICATIONS
                                    </button>
                                </div>
                                
                                <p class="text-muted small text-center mb-0">
                                    <i class="fas fa-info-circle me-1"></i> Modifier le devis recalculera les montants sans changer le numéro du devis.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Templates JS --}}
<template id="pieceRowTemplate">
    <tr class="piece-row">
        <td>
            <select name="pieces[INDEX][id]" class="form-select form-select-sm border-0 bg-light rounded-pill piece-select" required>
                <option value="">-- Choisir --</option>
                @foreach($pieces as $p)
                    <option value="{{ $p->id }}" data-price="{{ $p->prix_unitaire }}">{{ $p->nom }} ({{ $p->reference }})</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" step="0.001" name="pieces[INDEX][prix_unitaire]" class="form-control form-control-sm border-0 bg-light rounded-pill px-3 price-input text-center" value="0.000" min="0" required>
        </td>
        <td>
            <input type="number" name="pieces[INDEX][quantite]" class="form-control form-control-sm border-0 bg-light rounded-pill px-3 qty-input text-center" value="1" min="1" required>
        </td>
        <td class="fw-bold text-primary line-total">0.000</td>
        <td class="text-end">
            <button type="button" class="btn btn-sm btn-outline-danger border-0 remove-row"><i class="fas fa-trash"></i></button>
        </td>
    </tr>
</template>

<template id="laborRowTemplate">
    <tr class="labor-row">
        <td>
            <select name="labors[INDEX][id]" class="form-select form-select-sm border-0 bg-light rounded-pill labor-select" required>
                <option value="">-- Choisir --</option>
                @foreach($tarifsMo as $t)
                    <option value="{{ $t->id }}" data-price="{{ $t->montant }}">{{ $t->type_intervention }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" step="0.001" name="labors[INDEX][montant]" class="form-control form-control-sm border-0 bg-light rounded-pill px-3 text-center labor-input" value="0.000" min="0" required>
        </td>
        <td class="text-end">
            <button type="button" class="btn btn-sm btn-outline-danger border-0 remove-row"><i class="fas fa-trash"></i></button>
        </td>
    </tr>
</template>

@push('scripts')
    <script>
        // Passage des variables d'index au JS externe
        window.initialPieceIndex = {{ $devis->pieces->count() }};
        window.initialLaborIndex = {{ $devis->tarifsMo->count() }};
    </script>
    <script src="{{ asset('js/devis_create.js') }}"></script>
@endpush
@endsection
