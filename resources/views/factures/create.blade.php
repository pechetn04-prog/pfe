@extends('layouts.app')

@section('title', 'Établir la Facture - Dossier #' . $dossier->num_dossier)

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/facture-premium.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="h3 fw-bold mb-0 text-dark">Établir la Facture Finale</h1>
                    <p class="text-muted mb-0">Dossier #{{ $dossier->num_dossier }} — Client: {{ $dossier->client->name ?? '—' }}</p>
                </div>
                <a href="{{ route('dossiers.show', $dossier->id) }}" class="btn btn-light rounded-pill px-4 shadow-sm">
                    <i class="fas fa-arrow-left me-2"></i> Annuler
                </a>
            </div>

            <form action="{{ route('facture.store', $dossier->id) }}" method="POST" id="factureForm">
                @csrf
                
                <div class="row g-4">
                    <div class="col-md-8">
                        {{-- Section 1 : Pièces Consommées (Intervention) --}}
                        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                            <div class="card-header bg-white border-0 py-3 d-flex align-items-center">
                                <div class="bg-soft-primary p-2 rounded-3 me-3" style="background-color: rgba(30, 105, 255, 0.1);">
                                    <i class="fas fa-microchip text-primary"></i>
                                </div>
                                <h6 class="fw-bold mb-0">1. Pièces consommées (Atelier)</h6>
                            </div>
                            <div class="card-body pt-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="bg-light">
                                            <tr class="small text-muted text-uppercase">
                                                <th style="width: 40%;">Désignation</th>
                                                <th class="text-center" style="width: 15%;">Qté</th>
                                                <th class="text-end" style="width: 20%;">P.U TTC</th>
                                                <th class="text-end pe-3" style="width: 25%;">Total TTC</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($dossier->intervention->pieces as $piece)
                                                @php 
                                                    $qty = $piece->pivot->quantite ?? 1;
                                                    $pu = $piece->pivot->prix_unitaire ?? $piece->prix_vente;
                                                    $totalLigne = $qty * $pu;
                                                @endphp
                                                <tr>
                                                    <td class="fw-bold text-dark">{{ $piece->nom }}</td>
                                                    <td class="text-center fw-bold">{{ $qty }}</td>
                                                    <td class="text-end text-muted">{{ number_format($pu, 3, '.', ' ') }} DT</td>
                                                    <td class="text-end fw-bold text-primary pe-3">{{ number_format($totalLigne, 3, '.', ' ') }} DT</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center py-4 text-muted fst-italic">
                                                        <i class="fas fa-info-circle me-1 opacity-50"></i> Aucune pièce consommée lors de l'intervention.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Section 2 : Prestations --}}
                        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                            <div class="card-header bg-white border-0 py-3 d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="bg-soft-info p-2 rounded-3 me-3" style="background-color: rgba(13, 202, 240, 0.1);">
                                        <i class="fas fa-hand-holding-heart text-info"></i>
                                    </div>
                                    <h6 class="fw-bold mb-0">2. Prestations & Main d'œuvre</h6>
                                </div>
                                <button type="button" class="btn btn-outline-info btn-sm rounded-pill fw-bold" id="addLaborBtn">
                                    <i class="fas fa-plus me-1"></i> Ajouter
                                </button>
                            </div>
                            <div class="card-body pt-0">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle" id="laborsTable">
                                        <thead class="bg-light">
                                            <tr class="small text-muted text-uppercase">
                                                <th style="width: 60%;">Type d'intervention</th>
                                                <th style="width: 30%;" class="text-center">Montant TTC (DT)</th>
                                                <th style="width: 10%;" class="text-end pe-3">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- Pré-remplir avec les prestations suggérées par l'intervention --}}
                                            @if($dossier->intervention && $dossier->intervention->tarifsMo)
                                                @foreach($dossier->intervention->tarifsMo as $index => $mo)
                                                    <tr class="labor-row">
                                                        <td>
                                                            <select name="labors[{{ $index }}][id]" class="form-select form-select-sm border-0 bg-light rounded-pill px-3 labor-select" required>
                                                                <option value="">-- Sélectionner --</option>
                                                                @foreach($tarifsMo as $t)
                                                                    <option value="{{ $t->id }}" data-price="{{ $t->montant }}" {{ $t->id == $mo->id ? 'selected' : '' }}>
                                                                        {{ $t->type_intervention }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="number" step="0.001" min="0" name="labors[{{ $index }}][montant]" class="form-control form-control-sm border-0 bg-light rounded-pill px-3 text-center labor-input" value="{{ $mo->pivot->montant ?? $mo->montant }}" required>
                                                        </td>
                                                        <td class="text-end pe-3">
                                                            <button type="button" class="btn btn-sm btn-outline-danger border-0 remove-row"><i class="fas fa-trash"></i></button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        {{-- Récapitulatif Financier --}}
                        <div class="card border-0 shadow-sm sticky-top" style="border-radius: 15px; top: 20px;">
                            <div class="card-body p-4">
                                <h6 class="fw-bold text-dark mb-4">Récapitulatif de la Facture</h6>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small fw-bold">TOTAL PIÈCES</span>
                                    <span class="fw-bold" id="grand-total-pieces" data-value="{{ $totalPieces }}">{{ number_format($totalPieces, 3, '.', ' ') }} DT</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom border-light">
                                    <span class="text-muted small fw-bold">TOTAL MAIN D'ŒUVRE</span>
                                    <span class="fw-bold" id="grand-total-labors">{{ number_format($totalMO, 3, '.', ' ') }} DT</span>
                                </div>

                                <div class="mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label small fw-bold text-muted text-uppercase mb-0">Remise (%)</label>
                                        @if($isGarantieValide)
                                            <span class="badge bg-success rounded-pill" style="font-size: 0.65rem;"><i class="fas fa-shield-alt me-1"></i> Sous Garantie</span>
                                        @endif
                                    </div>
                                    <div class="input-group input-group-lg bg-light rounded-pill overflow-hidden border-0">
                                        <input type="number" name="remise" id="remise-input" class="form-control bg-transparent border-0 text-center fw-bold h4 mb-0" value="{{ $defaultRemise }}" min="0" max="100" {{ $isGarantieValide ? 'readonly' : '' }}>
                                        <span class="input-group-text bg-transparent border-0 fw-bold">%</span>
                                    </div>
                                </div>

                                <div class="bg-success bg-opacity-10 p-3 rounded-4 mb-4" style="background-color: rgba(25, 135, 84, 0.1);">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-success">TOTAL NET TTC</span>
                                        <div class="text-end">
                                            <div class="h3 fw-bold text-success mb-0" id="total-final">0.000</div>
                                            <small class="text-success opacity-75">(DT)</small>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-success w-100 rounded-pill py-3 fw-bold shadow-sm mb-3">
                                    <i class="fas fa-check-double me-2"></i> GÉNÉRER LA FACTURE
                                </button>
                                
                                <p class="text-muted small text-center mb-0" style="font-size: 0.7rem;">
                                    <i class="fas fa-shield-alt me-1"></i> Cette action clôturera l'aspect financier du dossier.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Template JS pour nouvelles lignes de prestation --}}
<template id="laborRowTemplate">
    <tr class="labor-row">
        <td>
            <select name="labors[INDEX][id]" class="form-select form-select-sm border-0 bg-light rounded-pill px-3 labor-select" required>
                <option value="">-- Sélectionner --</option>
                @foreach($tarifsMo as $t)
                    <option value="{{ $t->id }}" data-price="{{ $t->montant }}">{{ $t->type_intervention }}</option>
                @endforeach
            </select>
        </td>
        <td>
            <input type="number" step="0.001" min="0" name="labors[INDEX][montant]" class="form-control form-control-sm border-0 bg-light rounded-pill px-3 text-center labor-input" value="0.000" required>
        </td>
        <td class="text-end pe-3">
            <button type="button" class="btn btn-sm btn-outline-danger border-0 remove-row"><i class="fas fa-trash"></i></button>
        </td>
    </tr>
</template>

@push('scripts')
    <script>
        // Passage de l'index initial au JS externe (préparé par le contrôleur)
        window.initialLaborIndex = {{ $initialLaborIndex }};
    </script>
    <script src="{{ asset('js/facture_create.js') }}"></script>
@endpush
@endsection
