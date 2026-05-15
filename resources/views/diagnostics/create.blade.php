@extends('layouts.app')

@section('title', 'Expertise & Diagnostic Technique')

@section('content')
    <div class="container-fluid px-4 py-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h4 fw-bold mb-0">Expertise & Diagnostic Technique</h1>
                <small class="text-muted">Dossier <strong class="text-primary">#{{ $dossier->num_dossier }}</strong></small>
            </div>
            <a href="{{ route('technicien.tickets') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Annuler
            </a>
        </div>

        <form action="{{ route('diagnostics.store', $dossier->id) }}" method="POST">
            @csrf
            <div class="row g-4">

                {{-- ═══ COLONNE GAUCHE : Infos dossier ═══ --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm sticky-top" style="border-radius:16px; top:1.5rem;">
                        <div class="card-header bg-white border-0 py-3">
                            <h6 class="m-0 fw-bold d-flex align-items-center gap-2">
                                <span class="p-2 bg-primary bg-opacity-10 text-primary rounded-3"
                                    style="width:32px;height:32px;display:flex;align-items:center;justify-content:center;">
                                    <i class="fas fa-folder-open small"></i>
                                </span>
                                DOSSIER CLIENT
                            </h6>
                        </div>
                        <div class="card-body d-flex flex-column gap-3">
                            <div class="p-3 bg-light rounded-3">
                                <div class="text-muted" style="font-size:.65rem; font-weight:700; letter-spacing:.5px;">
                                    APPAREIL</div>
                                <div class="fw-bold">{{ $dossier->appareil->modele ?? '—' }}</div>
                                <div class="small text-muted font-monospace">IMEI: {{ $dossier->imei }}</div>
                            </div>
                            <div class="p-3 bg-light rounded-3">
                                <div class="text-muted mb-1"
                                    style="font-size:.65rem; font-weight:700; letter-spacing:.5px;">PANNE DÉCLARÉE</div>
                                <div class="small fst-italic">"{{ $dossier->panne_declaree }}"</div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Reçu le
                                    <strong>{{ \Carbon\Carbon::parse($dossier->date_reception)->format('d/m/Y') }}</strong></small>
                                <span
                                    class="badge rounded-pill {{ $dossier->sous_garantie ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $dossier->sous_garantie ? '✓ SOUS GARANTIE' : 'HORS GARANTIE' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ═══ COLONNE DROITE : Formulaire ═══ --}}
                <div class="col-lg-8 d-flex flex-column gap-4">

                    {{-- ─ Constat & Recommandation ─ --}}
                    <div class="card border-0 shadow-sm" style="border-radius:16px;">
                        <div class="card-body p-4 d-flex flex-column gap-4">
                            <div>
                                <label class="form-label fw-bold small text-uppercase text-muted">
                                    <i class="fas fa-microscope me-2 text-primary"></i>Constat Technique
                                </label>
                                <textarea name="constat_technique" class="form-control bg-light border-0 rounded-3" rows="4"
                                    placeholder="Panne constatée après expertise technique..." required></textarea>
                            </div>
                            <div>
                                <label class="form-label fw-bold small text-uppercase text-muted">
                                    <i class="fas fa-wrench me-2 text-primary"></i>Recommandation
                                </label>
                                <textarea name="recommandation" class="form-control bg-light border-0 rounded-3" rows="3"
                                    placeholder="Travaux à réaliser..."></textarea>
                            </div>
                        </div>
                    </div>

                    {{-- ─ Pièces & Prestations côte à côte ─ --}}
                    <div class="row g-4">

                    {{-- ─ Pièces & Prestations côte à côte ─ --}}
                    <div class="row g-4">
                        {{-- ── PIÈCES ── --}}
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
                                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 fw-bold"><i class="fas fa-boxes me-2 text-warning"></i>PIÈCES DÉTACHÉES</h6>
                                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold" id="add-piece-row">
                                        <i class="fas fa-plus me-1"></i> Ajouter
                                    </button>
                                </div>
                                <div class="card-body p-3">
                                    <div class="table-responsive">
                                        <table class="table table-borderless align-middle mb-0" id="pieces-table">
                                            <thead class="small text-muted text-uppercase bg-light" style="font-size: 0.6rem;">
                                                <tr>
                                                    <th>Désignation</th>
                                                    <th class="text-center">Qté</th>
                                                    <th class="text-center">Stock</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <select name="pieces[0][id]" class="form-select form-select-sm border-0 bg-light rounded-3 piece-select shadow-none">
                                                            <option value="">Choisir...</option>
                                                            @foreach($pieces as $p)
                                                                <option value="{{ $p->id }}" data-stock="{{ $p->quantite }}">{{ $p->nom }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input type="number" name="pieces[0][quantite]" class="form-control form-control-sm border-0 bg-light text-center rounded-3 shadow-none" value="1" min="1" style="width: 50px;"></td>
                                                    <td class="text-center small fw-bold stock-display">—</td>
                                                    <td class="text-end"><button type="button" class="btn btn-link text-danger btn-sm p-0 remove-row"><i class="fas fa-times"></i></button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ── PRESTATIONS ── --}}
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
                                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                                    <h6 class="m-0 fw-bold"><i class="fas fa-user-cog me-2 text-info"></i>PRESTATIONS M.O.</h6>
                                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold" id="add-presta-row">
                                        <i class="fas fa-plus me-1"></i> Ajouter
                                    </button>
                                </div>
                                <div class="card-body p-3">
                                    <div class="table-responsive">
                                        <table class="table table-borderless align-middle mb-0" id="presta-table">
                                            <thead class="small text-muted text-uppercase bg-light" style="font-size: 0.6rem;">
                                                <tr>
                                                    <th>Prestation</th>
                                                    <th class="text-end">Prix</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <select name="labors[]" class="form-select form-select-sm border-0 bg-light rounded-3 presta-select shadow-none">
                                                            <option value="">Choisir...</option>
                                                            @foreach($tarifsMo as $t)
                                                                <option value="{{ $t->id }}" data-price="{{ $t->montant }}">{{ $t->type_intervention }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td class="text-end small fw-bold text-primary price-display">—</td>
                                                    <td class="text-end"><button type="button" class="btn btn-link text-danger btn-sm p-0 remove-row"><i class="fas fa-times"></i></button></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>

                    {{-- ─ Décision technique ─ --}}
                    <div class="card border-0 shadow-sm" style="border-radius:16px;">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                                <div>
                                    <label class="small fw-bold text-uppercase text-muted mb-2 d-block">Décision
                                        Technique</label>
                                    <div class="btn-group" role="group">
                                        <input type="radio" class="btn-check" name="is_reparable" id="rep_oui" value="1"
                                            checked>
                                        <label class="btn btn-outline-success px-4 fw-bold" for="rep_oui">
                                            <i class="fas fa-check-circle me-2"></i>RÉPARABLE
                                        </label>
                                        <input type="radio" class="btn-check" name="is_reparable" id="rep_non" value="0">
                                        <label class="btn btn-outline-danger px-4 fw-bold" for="rep_non">
                                            <i class="fas fa-times-circle me-2"></i>IRRÉPARABLE
                                        </label>
                                    </div>
                                </div>

                                @if($dossier->sous_garantie)
                                    <div class="border border-danger border-opacity-25 rounded-3 p-3 bg-danger bg-opacity-10">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="exclusion_garantie"
                                                id="exclure">
                                            <label class="form-check-label text-danger fw-bold small" for="exclure">
                                                EXCLURE DE LA GARANTIE
                                            </label>
                                        </div>
                                        <div class="small text-muted mt-1">Panne due à une mauvaise utilisation.</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="card-footer bg-white border-0 text-end py-3 px-4">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-sm">
                                <i class="fas fa-save me-2"></i> ENREGISTRER LE DIAGNOSTIC
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function () {
                let pieceIndex = 1;

                function bindRowEvents(row) {
                    // Mise à jour stock
                    $(row).find('.piece-select').on('change', function() {
                        const stock = $(this).find(':selected').data('stock') || '—';
                        $(row).find('.stock-display').text(stock);
                    });

                    // Mise à jour prix prestation
                    $(row).find('.presta-select').on('change', function() {
                        const price = parseFloat($(this).find(':selected').data('price') || 0);
                        $(row).find('.price-display').text(price.toLocaleString('fr-FR', { minimumFractionDigits: 3 }) + ' DT');
                    });

                    // Suppression
                    $(row).find('.remove-row').on('click', function() {
                        if ($(row).closest('tbody').find('tr').length > 1) {
                            $(row).remove();
                        } else {
                            $(row).find('select').val('');
                            $(row).find('.stock-display').text('—');
                            $(row).find('.price-display').text('—');
                        }
                    });
                }

                // Bind initial rows
                $('#pieces-table tbody tr, #presta-table tbody tr').each(function() {
                    bindRowEvents(this);
                });

                // Ajouter pièce
                $('#add-piece-row').on('click', function() {
                    const tbody = $('#pieces-table tbody');
                    const firstRow = tbody.find('tr:first');
                    const newRow = firstRow.clone();
                    
                    newRow.find('select').attr('name', `pieces[${pieceIndex}][id]`).val('');
                    newRow.find('input[type="number"]').attr('name', `pieces[${pieceIndex}][quantite]`).val(1);
                    newRow.find('.stock-display').text('—');
                    
                    tbody.appendChild(newRow[0]); // jQuery compatible
                    bindRowEvents(newRow[0]);
                    pieceIndex++;
                });

                // Ajouter prestation
                $('#add-presta-row').on('click', function() {
                    const tbody = $('#presta-table tbody');
                    const firstRow = tbody.find('tr:first');
                    const newRow = firstRow.clone();
                    
                    newRow.find('select').val('');
                    newRow.find('.price-display').text('—');
                    
                    tbody.appendChild(newRow[0]);
                    bindRowEvents(newRow[0]);
                });
            });
        </script>
    @endpush
@endsection