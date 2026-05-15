@extends('layouts.app')

@section('title', 'Établir un Devis - Dossier #' . $dossier->num_dossier)

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div>
                    <h1 class="h3 fw-bold mb-0 text-dark">Établir un Devis</h1>
                    <p class="text-muted mb-0">Dossier #{{ $dossier->num_dossier }} — Client: {{ $dossier->client->name ?? '—' }}</p>
                </div>
                <a href="{{ route('dossiers.show', $dossier->id) }}" class="btn btn-light rounded-pill px-4 shadow-sm">
                    <i class="fas fa-arrow-left me-2"></i> Retour au dossier
                </a>
            </div>

            <form action="{{ route('devis.store', $dossier->id) }}" method="POST" id="devisForm">
                @csrf
                
                <div class="row g-4">
                    <div class="col-md-8">
                        {{-- Section 1 : Pièces --}}
                        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                            <div class="card-header bg-white border-0 py-3 d-flex align-items-center">
                                <div class="bg-soft-primary p-2 rounded-3 me-3">
                                    <i class="fas fa-microchip text-primary"></i>
                                </div>
                                <h6 class="fw-bold mb-0">1. Pièces détachées (Suggérées par le technicien)</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle" id="piecesTable">
                                        <thead class="bg-light">
                                            <tr class="small text-muted text-uppercase">
                                                <th style="width: 40%;">Désignation</th>
                                                <th style="width: 20%;">P.U (DA)</th>
                                                <th style="width: 15%;">Qté</th>
                                                <th style="width: 25%;" class="text-end">Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($dossier->diagnostic->pieces as $index => $piece)
                                            <tr>
                                                <td>
                                                    <input type="hidden" name="pieces[{{ $index }}][id]" value="{{ $piece->id }}">
                                                    <div class="fw-bold text-dark">{{ $piece->nom }}</div>
                                                    <small class="text-muted">{{ $piece->reference }}</small>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.01" name="pieces[{{ $index }}][prix_unitaire]" class="form-control form-control-sm border-0 bg-light rounded-pill px-3 price-input" value="{{ $piece->prix_unitaire }}" data-index="{{ $index }}">
                                                </td>
                                                <td>
                                                    <input type="number" name="pieces[{{ $index }}][quantite]" class="form-control form-control-sm border-0 bg-light rounded-pill px-3 qty-input" value="{{ $piece->pivot->quantite ?? 1 }}" data-index="{{ $index }}">
                                                </td>
                                                <td class="text-end fw-bold text-primary">
                                                    <span class="line-total" id="total-piece-{{ $index }}">{{ number_format(($piece->prix_unitaire * ($piece->pivot->quantite ?? 1)), 2, '.', '') }}</span> DA
                                                </td>
                                            </tr>
                                            @endforeach
                                            @if($dossier->diagnostic->pieces->isEmpty())
                                            <tr><td colspan="4" class="text-center py-4 text-muted small">Aucune pièce suggérée.</td></tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- Section 2 : Prestations --}}
                        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                            <div class="card-header bg-white border-0 py-3 d-flex align-items-center">
                                <div class="bg-soft-info p-2 rounded-3 me-3">
                                    <i class="fas fa-hand-holding-heart text-info"></i>
                                </div>
                                <h6 class="fw-bold mb-0">2. Prestations & Main d'œuvre</h6>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle" id="laborsTable">
                                        <thead class="bg-light">
                                            <tr class="small text-muted text-uppercase">
                                                <th style="width: 60%;">Type d'intervention</th>
                                                <th style="width: 40%;" class="text-end">Montant (DA)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($dossier->diagnostic->tarifsMo as $index => $tarif)
                                            <tr>
                                                <td>
                                                    <input type="hidden" name="labors[{{ $index }}][id]" value="{{ $tarif->id }}">
                                                    <div class="fw-bold text-dark">{{ $tarif->type_intervention }}</div>
                                                </td>
                                                <td class="text-end">
                                                    <input type="number" step="0.01" name="labors[{{ $index }}][montant]" class="form-control form-control-sm border-0 bg-light rounded-pill px-3 text-end labor-input" value="{{ $tarif->montant }}" style="width: 150px; display: inline-block;">
                                                </td>
                                            </tr>
                                            @endforeach
                                            @if($dossier->diagnostic->tarifsMo->isEmpty())
                                            <tr><td colspan="2" class="text-center py-4 text-muted small">Aucune prestation sélectionnée.</td></tr>
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
                                <h6 class="fw-bold text-dark mb-4">Résumé du Devis</h6>
                                
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Total Pièces</span>
                                    <span class="fw-bold" id="grand-total-pieces">0.000 DT</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3 pb-3 border-bottom border-light">
                                    <span class="text-muted">Total Main d'œuvre</span>
                                    <span class="fw-bold" id="grand-total-labors">0.000 DT</span>
                                </div>

                                <div class="mb-3">
                                    <label class="small fw-bold text-muted text-uppercase mb-1">Remise Globale (%)</label>
                                    <input type="number" name="remise" id="remiseInput" class="form-control border-0 bg-light rounded-pill px-3" value="0" min="0" max="100">
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

                                <button type="submit" class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-sm mb-3">
                                    <i class="fas fa-file-invoice-dollar me-2"></i> GÉNÉRER LE DEVIS
                                </button>
                                
                                <p class="text-muted small text-center mb-0">
                                    <i class="fas fa-info-circle me-1"></i> Le client recevra une notification par email dès la validation.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const remiseInput = document.getElementById('remiseInput');
    const finalTotalDisplay = document.getElementById('finalTotalDisplay');
    const finalTotalInput = document.getElementById('finalTotalInput');
    const grandTotalPiecesDisplay = document.getElementById('grand-total-pieces');
    const grandTotalLaborsDisplay = document.getElementById('grand-total-labors');

    function parseVal(val) {
        if (!val) return 0;
        // Remplacer virgule par point pour le calcul
        let cleanVal = val.toString().replace(',', '.');
        let parsed = parseFloat(cleanVal);
        return isNaN(parsed) ? 0 : parsed;
    }

    function calculate() {
        let totalPieces = 0;
        let totalLabors = 0;

        // Calcul pièces
        const pieceRows = document.querySelectorAll('#piecesTable tbody tr');
        pieceRows.forEach(row => {
            const priceInput = row.querySelector('.price-input');
            const qtyInput = row.querySelector('.qty-input');
            
            if (priceInput && qtyInput) {
                const price = parseVal(priceInput.value);
                const qty = parseVal(qtyInput.value);
                const lineTotal = price * qty;
                
                const totalSpan = row.querySelector('.line-total');
                if (totalSpan) {
                    totalSpan.textContent = lineTotal.toLocaleString('fr-FR', { minimumFractionDigits: 3, maximumFractionDigits: 3 });
                }
                totalPieces += lineTotal;
            }
        });

        // Calcul MO
        const laborInputs = document.querySelectorAll('.labor-input');
        laborInputs.forEach(input => {
            totalLabors += parseVal(input.value);
        });

        const subtotal = totalPieces + totalLabors;
        const remisePct = parseVal(remiseInput.value);
        const montantRemise = subtotal * (remisePct / 100);
        const finalTotal = subtotal - montantRemise;

        // Affichage
        grandTotalPiecesDisplay.textContent = totalPieces.toLocaleString('fr-FR', { minimumFractionDigits: 3 }) + ' DT';
        grandTotalLaborsDisplay.textContent = totalLabors.toLocaleString('fr-FR', { minimumFractionDigits: 3 }) + ' DT';
        finalTotalDisplay.textContent = finalTotal.toLocaleString('fr-FR', { minimumFractionDigits: 3 });
        
        // Valeur pour le formulaire (doit être avec un point)
        finalTotalInput.value = finalTotal.toFixed(3);
    }

    // Ecouter tous les changements
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('price-input') || 
            e.target.classList.contains('qty-input') || 
            e.target.classList.contains('labor-input') || 
            e.target.id === 'remiseInput') {
            calculate();
        }
    });

    calculate(); // Appel initial
});
</script>
@endpush
@endsection
