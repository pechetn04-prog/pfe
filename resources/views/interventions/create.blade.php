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
                                                        <option value="{{ $p->id }}" data-stock="{{ $p->quantite }}">{{ $p->nom }}</option>
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
    <script src="{{ asset('js/intervention_create.js') }}"></script>
@endpush
@endsection
