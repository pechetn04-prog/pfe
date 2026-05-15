@extends('layouts.app')

@section('title', 'Établir la Facture')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/tickets.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Établir la Facture Finale</h1>
            <small class="text-muted">DOSSIER #{{ $dossier->num_dossier }} — {{ $dossier->client->name ?? 'Client' }}</small>
        </div>
        <a href="{{ route('dossiers.show', $dossier->id) }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Annuler
        </a>
    </div>

    <form action="{{ route('facture.store', $dossier->id) }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                {{-- Détails des Pièces Consommées --}}
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-white border-0">
                        <h6 class="m-0 font-weight-bold text-primary text-uppercase small">
                            <i class="fas fa-microchip me-2"></i> 1. Pièces Consommées (Intervention)
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm align-middle">
                                <thead class="small text-muted text-uppercase">
                                    <tr>
                                        <th>Désignation</th>
                                        <th class="text-center">Qté</th>
                                        <th class="text-end">Prix Unit. TTC</th>
                                        <th class="text-end">Total TTC</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $totalPieces = 0; @endphp
                                    @forelse($dossier->intervention->pieces as $piece)
                                        @php 
                                            $qty = $piece->pivot->quantite ?? 1;
                                            $pu = $piece->pivot->prix_unitaire ?? $piece->prix_vente;
                                            $totalLigne = $qty * $pu;
                                            $totalPieces += $totalLigne;
                                        @endphp
                                        <tr>
                                            <td>{{ $piece->nom }}</td>
                                            <td class="text-center">{{ $qty }}</td>
                                            <td class="text-end">{{ number_format($pu, 3, ',', ' ') }} DT</td>
                                            <td class="text-end fw-bold">{{ number_format($totalLigne, 3, ',', ' ') }} DT</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-3 text-muted fst-italic">
                                                Aucune pièce consommée lors de l'intervention.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                @if($totalPieces > 0)
                                <tfoot class="border-top-0">
                                    <tr class="table-light">
                                        <td colspan="3" class="text-end fw-bold">Sous-total Pièces :</td>
                                        <td class="text-end fw-bold text-primary">{{ number_format($totalPieces, 3, ',', ' ') }} DT</td>
                                    </tr>
                                </tfoot>
                                @endif
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Main d'Œuvre et Prestations --}}
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-white border-0 d-flex justify-content-between align-items-center">
                        <h6 class="m-0 font-weight-bold text-primary text-uppercase small">
                            <i class="fas fa-user-cog me-2"></i> 2. Main d'Œuvre & Prestations
                        </h6>
                        <button type="button" class="btn btn-outline-primary btn-sm rounded-pill px-3" onclick="addLaborRow()">
                            <i class="fas fa-plus me-1"></i> Ajouter
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm align-middle" id="labor-table">
                                <thead class="small text-muted text-uppercase">
                                    <tr>
                                        <th>Prestation</th>
                                        <th style="width: 150px;" class="text-end">Montant TTC (DT)</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $totalMO = 0; @endphp
                                    {{-- Pré-remplir avec les prestations suggérées par le diagnostic si elles existent --}}
                                    @if(isset($dossier->intervention->tarifsMo))
                                        @foreach($dossier->intervention->tarifsMo as $mo)
                                            @php $totalMO += $mo->pivot->montant ?? $mo->montant; @endphp
                                            <tr>
                                                <td>
                                                    <select name="labors[0][id]" class="form-select form-select-sm labor-select" onchange="updateLaborPrice(this)">
                                                        <option value="">-- Sélectionner --</option>
                                                        @foreach($tarifsMo as $t)
                                                            <option value="{{ $t->id }}" data-price="{{ $t->montant }}" {{ $t->id == $mo->id ? 'selected' : '' }}>
                                                                {{ $t->type_intervention }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" step="0.001" name="labors[0][montant]" class="form-control form-control-sm text-end labor-price" value="{{ $mo->pivot->montant ?? $mo->montant }}" onchange="calculateGlobalTotal()">
                                                </td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-link text-danger p-0" onclick="removeRow(this)">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
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

            {{-- RÉCAPITULATIF FINANCIER --}}
            <div class="col-lg-4">
                <div class="card shadow border-0" style="border-radius: 15px; background: #f8fafc;">
                    <div class="card-header bg-primary text-white py-3" style="border-radius: 15px 15px 0 0;">
                        <h6 class="m-0 font-weight-bold text-center text-uppercase small">Récapitulatif de la Facture</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Pièces :</span>
                            <span class="fw-bold" id="summary-pieces" data-value="{{ $totalPieces }}">{{ number_format($totalPieces, 3, ',', ' ') }} DT</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Total Main d'Œuvre :</span>
                            <span class="fw-bold" id="summary-mo">{{ number_format($totalMO, 3, ',', ' ') }} DT</span>
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Remise (%)</label>
                            <input type="number" name="remise" id="remise-input" class="form-control form-control-lg text-center fw-bold" value="0" min="0" max="100" onchange="calculateGlobalTotal()">
                        </div>
                        <div class="bg-white p-3 rounded-3 border mb-4">
                            <div class="text-center small text-muted text-uppercase mb-1">TOTAL NET À PAYER (TTC)</div>
                            <div class="text-center h2 fw-bold text-primary mb-0" id="total-final">0,000 DT</div>
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 fw-bold shadow-sm py-3">
                            <i class="fas fa-file-invoice me-2"></i> GÉNÉRER LA FACTURE
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    let laborIndex = {{ isset($dossier->intervention->tarifsMo) ? $dossier->intervention->tarifsMo->count() : 1 }};

    function addLaborRow() {
        const tbody = document.querySelector('#labor-table tbody');
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <select name="labors[${laborIndex}][id]" class="form-select form-select-sm labor-select" onchange="updateLaborPrice(this)">
                    <option value="">-- Sélectionner --</option>
                    @foreach($tarifsMo as $t)
                        <option value="{{ $t->id }}" data-price="{{ $t->montant }}">{{ $t->type_intervention }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="number" step="0.001" name="labors[${laborIndex}][montant]" class="form-control form-control-sm text-end labor-price" value="0" onchange="calculateGlobalTotal()">
            </td>
            <td class="text-end">
                <button type="button" class="btn btn-link text-danger p-0" onclick="removeRow(this)">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
        laborIndex++;
    }

    function removeRow(btn) {
        btn.closest('tr').remove();
        calculateGlobalTotal();
    }

    function updateLaborPrice(select) {
        const price = select.options[select.selectedIndex].getAttribute('data-price') || 0;
        select.closest('tr').querySelector('.labor-price').value = price;
        calculateGlobalTotal();
    }

    function calculateGlobalTotal() {
        const totalPieces = parseFloat(document.getElementById('summary-pieces').getAttribute('data-value')) || 0;
        let totalMO = 0;
        document.querySelectorAll('.labor-price').forEach(input => {
            totalMO += parseFloat(input.value) || 0;
        });

        document.getElementById('summary-mo').textContent = totalMO.toLocaleString('fr-FR', {minimumFractionDigits: 3, maximumFractionDigits: 3}) + ' DT';
        
        const totalBrut = totalPieces + totalMO;
        const remise = parseFloat(document.getElementById('remise-input').value) || 0;
        const totalFinal = totalBrut * (1 - remise / 100);

        document.getElementById('total-final').textContent = totalFinal.toLocaleString('fr-FR', {minimumFractionDigits: 3, maximumFractionDigits: 3}) + ' DT';
    }

    // Initial calculation
    document.addEventListener('DOMContentLoaded', calculateGlobalTotal);
</script>
@endpush
@endsection
