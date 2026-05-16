@extends('layouts.app')

@section('title', 'Rapport d\'Intervention Technique')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/intervention_create.css') }}">
@endpush


@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 fw-bold text-dark">Rapport d'Intervention Technique</h1>
            <small class="text-muted text-uppercase fw-bold" style="font-size: 0.7rem;">Dossier #{{ $dossier->num_dossier }}</small>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#modalRetrait">
                <i class="fas fa-undo-alt me-1"></i> Demander Retrait
            </button>
            <a href="{{ route('technicien.tickets') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="fas fa-times me-1"></i> Annuler
            </a>
        </div>
    </div>

    {{-- Modal Raison du Retrait --}}
    <div class="modal fade" id="modalRetrait" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 15px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-danger">Motif du retrait</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('dossiers.rejeter', $dossier->id) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="small text-muted mb-3">Veuillez expliquer pourquoi vous souhaitez vous retirer de ce dossier ou rejeter le diagnostic.</p>
                        <textarea name="raison" class="form-control bg-light border-0" rows="4" placeholder="Ex: Pièce manquante indisponible, erreur d'affectation, expertise complexe..." required minlength="10"></textarea>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">CONFIRMER LE RETRAIT</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Confirmation Attente Pièce --}}
    <div class="modal fade" id="modalAttentePiece" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 15px;">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-primary">Dossier en attente de pièce</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-3">
                        <i class="fas fa-hourglass-half fa-3x text-warning"></i>
                    </div>
                    <p class="text-center fw-bold">Vous allez placer ce dossier en attente de pièces.</p>
                    <p class="small text-muted text-center">L'administration sera immédiatement informée via le tableau de bord pour procéder à la commande ou à l'approvisionnement des pièces nécessaires.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Modifier</button>
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" id="confirmSubmitAttente">CONFIRMER ET INFORMER</button>
                </div>
            </div>
        </div>
    </div>

    <form id="interventionForm" action="{{ route('interventions.store', $dossier->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-4">
            {{-- COLONNE GAUCHE --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <div class="text-muted small fw-bold mb-1">APPAREIL</div>
                            <div class="h6 fw-bold text-dark mb-0">{{ $dossier->appareil->modele ?? '—' }}</div>
                            <div class="small text-muted font-monospace">IMEI: {{ $dossier->imei }}</div>
                        </div>
                        <div class="mb-0">
                            <div class="text-muted small fw-bold mb-2 text-uppercase">Diagnostic Initial</div>
                            <div class="p-3 bg-light rounded-3 border-0 small text-dark" style="min-height: 80px;">
                                <i class="fas fa-quote-left text-muted opacity-50 me-2"></i>
                                {{ $dossier->diagnostic->constat_technique ?? 'Aucun diagnostic saisi.' }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-header bg-white border-0 pt-4 pb-0 text-center">
                        <h6 class="fw-bold text-uppercase small text-muted"><i class="fas fa-camera me-2 text-primary"></i> Pièce Jointe</h6>
                    </div>
                    <div class="card-body text-center p-4">
                        <div class="upload-area border border-2 border-dashed rounded-4 p-4 bg-light mb-2 cursor-pointer position-relative" style="transition: all 0.3s;">
                            <i class="fas fa-cloud-upload-alt fa-2x text-primary mb-2"></i>
                            <div class="small fw-bold">Choisir un fichier</div>
                            <input type="file" name="photo_intervention" class="position-absolute w-100 h-100 top-0 start-0 opacity-0" style="cursor: pointer;">
                        </div>
                        <small class="text-muted italic block mt-2">Photo du résultat ou document justificatif</small>
                    </div>
                </div>
            </div>

            {{-- COLONNE DROITE --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-2">Quels travaux ont été réalisés ?</label>
                            <textarea name="compte_rendu" class="form-control border-light shadow-none" rows="5" 
                                style="border-radius: 12px; background-color: #f8fafc;" placeholder="Décrivez l'intervention effectuée..." required></textarea>
                        </div>

                        {{-- Pièces Détachées --}}
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="fw-bold small text-muted text-uppercase"><i class="fas fa-microchip me-2 text-primary"></i> 1. Pièces Détachées</label>
                                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm fw-bold" id="add-piece-row">
                                    <i class="fas fa-plus me-1"></i> Ajouter une ligne
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-borderless align-middle mb-0" id="pieces-table">
                                    <thead class="small text-muted text-uppercase bg-light" style="font-size: 0.65rem;">
                                        <tr>
                                            <th class="ps-3">Référence / Désignation</th>
                                            <th class="text-center" style="width: 100px;">Qté</th>
                                            <th class="text-center" style="width: 100px;">Stock</th>
                                            <th class="text-end pe-3" style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="ps-0">
                                                <select name="pieces[0][id]" class="form-select border-0 bg-light rounded-3 shadow-none piece-select">
                                                    <option value="">Sélectionner une pièce...</option>
                                                    @foreach($pieces as $p)
                                                        <option value="{{ $p->id }}" data-stock="{{ $p->quantite }}">{{ $p->nom }} (Ref: {{ $p->reference }})</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td><input type="number" name="pieces[0][quantite]" class="form-control border-0 bg-light text-center rounded-3 shadow-none" value="1" min="1"></td>
                                            <td class="text-center text-muted small fw-bold stock-display">—</td>
                                            <td class="text-end pe-0"><button type="button" class="btn btn-light btn-sm text-danger rounded-pill remove-row"><i class="fas fa-times"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Prestations --}}
                        <div class="mb-0">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="fw-bold small text-muted text-uppercase"><i class="fas fa-user-cog me-2 text-primary"></i> 2. Prestations Techniques</label>
                                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm fw-bold" id="add-presta-row">
                                    <i class="fas fa-plus me-1"></i> Ajouter une ligne
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-borderless align-middle mb-0" id="presta-table">
                                    <thead class="small text-muted text-uppercase bg-light" style="font-size: 0.65rem;">
                                        <tr>
                                            <th class="ps-3">Désignation Prestation</th>
                                            <th class="text-end" style="width: 150px;">Montant (DT)</th>
                                            <th class="text-end pe-3" style="width: 50px;"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td class="ps-0">
                                                <select name="labors[]" class="form-select border-0 bg-light rounded-3 shadow-none presta-select">
                                                    <option value="">Sélectionner une prestation...</option>
                                                    @foreach($tarifsMo as $t)
                                                        <option value="{{ $t->id }}" data-price="{{ $t->montant }}">{{ $t->type_intervention }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td class="text-end fw-bold text-primary pe-3 price-display">0.000 DT</td>
                                            <td class="text-end pe-0"><button type="button" class="btn btn-light btn-sm text-danger rounded-pill remove-row"><i class="fas fa-times"></i></button></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- STATUT FINAL (HORIZONTAL LIGHT BUTTONS) --}}
                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-body p-4">
                        <label class="form-label fw-bold small text-muted text-uppercase mb-3 text-center d-block">Quel est le résultat réel de la réparation ?</label>
                        
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <input type="radio" class="btn-check" name="statut_final" id="res_repare" value="REPARE" checked>
                                <label class="btn btn-light border-0 w-100 p-3 text-start rounded-4 status-selector" for="res_repare" style="--btn-color: #10b981; --btn-bg: #f0fdf4;">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-circle me-3"><i class="fas fa-check"></i></div>
                                        <div>
                                            <div class="fw-bold small title">Réparé</div>
                                            <div class="text-muted" style="font-size: 0.65rem;">L'appareil fonctionne correctement.</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <input type="radio" class="btn-check" name="statut_final" id="res_irreparable" value="IRREPARABLE">
                                <label class="btn btn-light border-0 w-100 p-3 text-start rounded-4 status-selector" for="res_irreparable" style="--btn-color: #ef4444; --btn-bg: #fef2f2;">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-circle me-3"><i class="fas fa-times"></i></div>
                                        <div>
                                            <div class="fw-bold small title">Non réparé</div>
                                            <div class="text-muted" style="font-size: 0.65rem;">Appareil irréparable ou échec.</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                            <div class="col-md-4">
                                <input type="radio" class="btn-check" name="statut_final" id="res_attente" value="ATTENTE_PIECE">
                                <label class="btn btn-light border-0 w-100 p-3 text-start rounded-4 status-selector" for="res_attente" style="--btn-color: #3b82f6; --btn-bg: #eff6ff;">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-circle me-3"><i class="fas fa-clock"></i></div>
                                        <div>
                                            <div class="fw-bold small title">Attente Pièce</div>
                                            <div class="text-muted" style="font-size: 0.65rem;">Intervention suspendue.</div>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" id="mainSubmitBtn" class="btn btn-primary shadow rounded-pill px-4 py-2 fw-bold">
                                <i class="fas fa-check-circle me-2"></i> VALIDER L'INTERVENTION
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let pieceIndex = 1;

    function checkStockLevels() {
        let outOfStock = false;
        document.querySelectorAll('#pieces-table tbody tr').forEach(row => {
            const select = row.querySelector('.piece-select');
            const qtyInput = row.querySelector('input[type="number"]');
            if (select && select.value && qtyInput) {
                const stock = parseInt(select.options[select.selectedIndex].dataset.stock || 0);
                const qty = parseInt(qtyInput.value || 0);
                if (qty > stock) {
                    outOfStock = true;
                    row.querySelector('.stock-display').classList.add('text-danger');
                } else {
                    row.querySelector('.stock-display').classList.remove('text-danger');
                }
            }
        });

        const btnRepare = document.getElementById('res_repare');
        const btnIrrepare = document.getElementById('res_irreparable');
        const btnAttente = document.getElementById('res_attente');
        const labels = document.querySelectorAll('.status-selector');

        if (outOfStock) {
            btnRepare.disabled = true;
            btnIrrepare.disabled = true;
            btnAttente.checked = true;
            labels.forEach(label => {
                const forId = label.getAttribute('for');
                if (forId === 'res_repare' || forId === 'res_irreparable') {
                    label.style.opacity = '0.5';
                    label.style.pointerEvents = 'none';
                }
            });
        } else {
            btnRepare.disabled = false;
            btnIrrepare.disabled = false;
            labels.forEach(label => {
                label.style.opacity = '1';
                label.style.pointerEvents = 'auto';
            });
        }
    }

    function bindRowEvents(row) {
        // Mise à jour stock
        const pieceSelect = row.querySelector('.piece-select');
        if (pieceSelect) {
            pieceSelect.addEventListener('change', function() {
                const stock = this.options[this.selectedIndex].dataset.stock || '—';
                row.querySelector('.stock-display').textContent = stock;
                checkStockLevels();
            });
        }

        const qtyInput = row.querySelector('input[type="number"]');
        if (qtyInput) {
            qtyInput.addEventListener('input', checkStockLevels);
        }

        // Mise à jour prix prestation
        const prestaSelect = row.querySelector('.presta-select');
        if (prestaSelect) {
            prestaSelect.addEventListener('change', function() {
                const price = parseFloat(this.options[this.selectedIndex].dataset.price || 0);
                row.querySelector('.price-display').textContent = price.toLocaleString('fr-FR', { minimumFractionDigits: 3 }) + ' DT';
            });
        }

        // Suppression
        row.querySelector('.remove-row').addEventListener('click', function() {
            if (row.parentElement.rows.length > 1) {
                row.remove();
                checkStockLevels();
            } else {
                const select = row.querySelector('select');
                if (select) select.value = '';
                const stock = row.querySelector('.stock-display');
                if (stock) stock.textContent = '—';
                const price = row.querySelector('.price-display');
                if (price) price.textContent = '0.000 DT';
                checkStockLevels();
            }
        });
    }

    // Bind initial rows
    document.querySelectorAll('#pieces-table tbody tr, #presta-table tbody tr').forEach(bindRowEvents);

    // Ajouter pièce
    document.getElementById('add-piece-row').addEventListener('click', function() {
        const tbody = document.querySelector('#pieces-table tbody');
        const firstRow = tbody.querySelector('tr');
        const newRow = firstRow.cloneNode(true);
        
        const select = newRow.querySelector('select');
        select.name = `pieces[${pieceIndex}][id]`;
        select.value = '';
        
        const qty = newRow.querySelector('input[type="number"]');
        qty.name = `pieces[${pieceIndex}][quantite]`;
        qty.value = 1;
        
        newRow.querySelector('.stock-display').textContent = '—';
        newRow.querySelector('.stock-display').classList.remove('text-danger');
        
        tbody.appendChild(newRow);
        bindRowEvents(newRow);
        pieceIndex++;
    });

    // Ajouter prestation
    document.getElementById('add-presta-row').addEventListener('click', function() {
        const tbody = document.querySelector('#presta-table tbody');
        const firstRow = tbody.querySelector('tr');
        const newRow = firstRow.cloneNode(true);
        
        const select = newRow.querySelector('select');
        select.value = '';
        newRow.querySelector('.price-display').textContent = '0.000 DT';
        
        tbody.appendChild(newRow);
        bindRowEvents(newRow);
    });

    // Interception de la soumission pour le modal Attente Pièce
    const form = document.getElementById('interventionForm');
    const modalAttente = new bootstrap.Modal(document.getElementById('modalAttentePiece'));
    const confirmSubmitBtn = document.getElementById('confirmSubmitAttente');

    form.addEventListener('submit', function(e) {
        const selectedStatus = document.querySelector('input[name="statut_final"]:checked').value;
        
        // Si c'est en attente de pièce et que le modal n'a pas encore été validé
        if (selectedStatus === 'ATTENTE_PIECE' && !form.dataset.confirmed) {
            e.preventDefault();
            modalAttente.show();
        }
    });

    confirmSubmitBtn.addEventListener('click', function() {
        form.dataset.confirmed = "true";
        form.submit();
    });

    // Affichage du nom du fichier sélectionné
    const photoInput = document.querySelector('input[name="photo_intervention"]');
    if (photoInput) {
        photoInput.addEventListener('change', function() {
            const fileName = this.value.split('\\').pop();
            const label = this.parentElement.querySelector('.small.fw-bold');
            if (fileName) {
                label.textContent = fileName;
                label.classList.add('text-primary');
            } else {
                label.textContent = 'Choisir un fichier';
                label.classList.remove('text-primary');
            }
        });
    }
});
</script>
@endpush
@endsection
