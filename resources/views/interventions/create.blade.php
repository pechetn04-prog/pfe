@extends('layouts.app')

@section('title', 'Rapport d\'Intervention Technique')

@section('content')
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0 fw-bold text-dark">Rapport d'Intervention Technique</h1>
                <small class="text-muted text-uppercase fw-bold font-size-07">Dossier
                    #{{ $dossier->num_dossier }}</small>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold" data-bs-toggle="modal"
                    data-bs-target="#modalRetrait">
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
                <div class="modal-content border-0 shadow modal-content-rounded">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-danger">Motif du retrait</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('dossiers.rejeter', $dossier->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <p class="small text-muted mb-3">Veuillez expliquer pourquoi vous souhaitez vous retirer de ce
                                dossier ou rejeter le diagnostic.</p>
                            <textarea name="raison" class="form-control bg-light border-0" rows="4"
                                placeholder="Ex: Pièce manquante indisponible, erreur d'affectation, expertise complexe..."
                                required minlength="10"></textarea>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-light rounded-pill px-4"
                                data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">CONFIRMER LE
                                RETRAIT</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal Confirmation Attente Pièce --}}
        <div class="modal fade" id="modalAttentePiece" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow modal-content-rounded">
                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-primary">Dossier en attente de pièce</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <i class="fas fa-hourglass-half fa-3x text-warning"></i>
                        </div>
                        <p class="text-center fw-bold">Vous allez placer ce dossier en attente de pièces.</p>
                        <p class="small text-muted text-center">L'administration sera immédiatement informée via le tableau
                            de bord pour procéder à la commande ou à l'approvisionnement des pièces nécessaires.</p>
                    </div>
                    <div class="modal-footer border-0 justify-content-center">
                        <button type="button" class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">Modifier</button>
                        <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold"
                            id="confirmSubmitAttente">CONFIRMER ET INFORMER</button>
                    </div>
                </div>
            </div>
        </div>

        <form id="interventionForm" action="{{ route('interventions.store', $dossier->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            <div class="row g-4">
                {{-- COLONNE GAUCHE --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm mb-4 card-intervention-box">
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <div class="text-muted small fw-bold mb-1">CLIENT</div>
                                <div class="h6 fw-bold text-dark mb-0">{{ $dossier->client->name ?? '—' }}</div>
                                @if($dossier->client && $dossier->client->telephone)
                                    <div class="small text-muted"><i class="fas fa-phone me-1"></i>
                                        {{ $dossier->client->telephone }}</div>
                                @endif
                            </div>
                            <div class="mb-4">
                                <div class="text-muted small fw-bold mb-1">APPAREIL</div>
                                <div class="h6 fw-bold text-dark mb-0">{{ $dossier->appareil->modele ?? '—' }}</div>
                                <div class="small text-muted font-monospace">IMEI: {{ $dossier->imei }}</div>
                            </div>
                            <div class="mb-4">
                                <div class="text-muted small fw-bold mb-1">STATUT GARANTIE</div>
                                <div>
                                    @if($dossier->garantie_annulee)
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fw-bold badge-status-intervention">
                                            <i class="fas fa-exclamation-triangle me-1"></i> GARANTIE EXCLUE
                                        </span>
                                    @elseif($dossier->sous_garantie)
                                        <span class="badge bg-success rounded-pill px-3 py-1 fw-bold badge-status-intervention">
                                            <i class="fas fa-check-circle me-1"></i> SOUS GARANTIE
                                        </span>
                                    @else
                                        <span class="badge bg-danger rounded-pill px-3 py-1 fw-bold badge-status-intervention">
                                            <i class="fas fa-times-circle me-1"></i> HORS GARANTIE
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="text-muted small fw-bold mb-2 text-uppercase">Panne Déclarée & État</div>
                                <div class="p-3 bg-light rounded-3 border-0 small text-dark">
                                    <div class="fw-bold mb-1 text-muted font-size-075"><i
                                            class="fas fa-exclamation-circle me-1 text-danger"></i> PANNE SIGNALEÉ PAR LE
                                        CLIENT</div>
                                    <div class="text-dark mb-2">{{ $dossier->panne_declaree }}</div>

                                    @if($dossier->etat_appareil)
                                        <div class="fw-bold mb-1 text-muted font-size-075"><i
                                                class="fas fa-mobile-alt me-1 text-info"></i> ÉTAT PHYSIQUE DE L'APPAREIL</div>
                                        <div class="text-dark">{{ $dossier->etat_appareil }}</div>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-0">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="text-muted small fw-bold text-uppercase">Diagnostic Initial</span>
                                    @if($dossier->diagnostic)
                                        <small class="text-muted">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            {{ $dossier->diagnostic->created_at->format('d/m/Y H:i') }}
                                        </small>
                                    @endif
                                </div>
                                <div class="p-3 bg-light rounded-3 border-0 small text-dark">
                                    <div class="fw-bold mb-1 text-muted font-size-075"><i
                                            class="fas fa-search me-1 text-primary"></i> CONSTAT TECHNIQUE</div>
                                    <div class="text-dark mb-2">
                                        {{ $dossier->diagnostic?->constat ?? 'Aucun diagnostic saisi.' }}
                                    </div>

                                    @if($dossier->diagnostic?->recommandation)
                                        <div class="fw-bold mb-1 mt-2 text-muted font-size-075"><i
                                                class="fas fa-comment-dots me-1 text-primary"></i> RECOMMANDATION / COMMENTAIRE
                                        </div>
                                        <div class="text-dark">{{ $dossier->diagnostic->recommandation }}</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm card-intervention-box">
                        <div class="card-header bg-white border-0 pt-4 pb-0 text-center">
                            <h6 class="fw-bold text-uppercase small text-muted"><i
                                    class="fas fa-camera me-2 text-primary"></i> Pièce Jointe</h6>
                        </div>
                        <div class="card-body text-center p-4">
                            <div class="upload-area border border-2 border-dashed rounded-4 p-4 bg-light mb-2 cursor-pointer position-relative upload-area-transition">
                                <i class="fas fa-cloud-upload-alt fa-2x text-primary mb-2"></i>
                                <div class="small fw-bold">Choisir un fichier</div>
                                <input type="file" name="photo_intervention"
                                    class="position-absolute w-100 h-100 top-0 start-0 opacity-0 cursor-pointer-custom">
                            </div>
                            <small class="text-muted italic block mt-2">Photo du résultat ou document justificatif</small>
                        </div>
                    </div>
                </div>

                {{-- COLONNE DROITE --}}
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm mb-4 card-intervention-box">
                        <div class="card-body p-4">
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-muted text-uppercase mb-2">
                                    <i class="fas fa-clipboard-list me-1 text-primary"></i> Compte Rendu & Commentaires sur
                                    l'Intervention
                                </label>
                                <textarea name="compte_rendu" class="form-control border-light shadow-none p-3 compte-rendu-textarea" rows="5"
                                    placeholder="Décrivez en détail l'intervention technique réalisée, les tests effectués, ainsi que vos remarques ou commentaires spécifiques sur ce dossier..."
                                    required></textarea>
                            </div>

                            {{-- Pièces Détachées --}}
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="fw-bold small text-muted text-uppercase"><i
                                            class="fas fa-microchip me-2 text-primary"></i> 1. Pièces Détachées</label>
                                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm fw-bold"
                                        id="add-piece-row">
                                        <i class="fas fa-plus me-1"></i> Ajouter une ligne
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-borderless align-middle mb-0" id="pieces-table">
                                        <thead class="small text-muted text-uppercase bg-light font-size-065">
                                            <tr>
                                                <th class="ps-3">Référence / Désignation</th>
                                                <th class="text-center th-center-w-100">Qté</th>
                                                <th class="text-center th-center-w-100">Stock</th>
                                                <th class="text-end pe-3 th-end-w-50"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="ps-0">
                                                    <select name="pieces[0][id]"
                                                        class="form-select border-0 bg-light rounded-3 shadow-none piece-select">
                                                        <option value="">Sélectionner une pièce...</option>
                                                        @foreach($pieces as $p)
                                                            <option value="{{ $p->id }}" data-stock="{{ $p->quantite }}">
                                                                {{ $p->nom }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td><input type="number" name="pieces[0][quantite]"
                                                        class="form-control border-0 bg-light text-center rounded-3 shadow-none"
                                                        value="1" min="1"></td>
                                                <td class="text-center text-muted small fw-bold stock-display">—</td>
                                                <td class="text-end pe-0"><button type="button"
                                                        class="btn btn-light btn-sm text-danger rounded-pill remove-row"><i
                                                            class="fas fa-times"></i></button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- Prestations --}}
                            <div class="mb-0">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="fw-bold small text-muted text-uppercase"><i
                                            class="fas fa-user-cog me-2 text-primary"></i> 2. Prestations Techniques</label>
                                    <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm fw-bold"
                                        id="add-presta-row">
                                        <i class="fas fa-plus me-1"></i> Ajouter une ligne
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-borderless align-middle mb-0" id="presta-table">
                                        <thead class="small text-muted text-uppercase bg-light font-size-065">
                                            <tr>
                                                <th class="ps-3">Désignation Prestation</th>
                                                <th class="text-end th-end-w-150">Montant (DT)</th>
                                                <th class="text-end pe-3 th-end-w-50"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="ps-0">
                                                    <select name="labors[]"
                                                        class="form-select border-0 bg-light rounded-3 shadow-none presta-select">
                                                        <option value="">Sélectionner une prestation...</option>
                                                        @foreach($tarifsMo as $t)
                                                            <option value="{{ $t->id }}" data-price="{{ $t->montant }}">
                                                                {{ $t->type_intervention }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="text-end fw-bold text-primary pe-3 price-display">0.000 DT</td>
                                                <td class="text-end pe-0"><button type="button"
                                                        class="btn btn-light btn-sm text-danger rounded-pill remove-row"><i
                                                            class="fas fa-times"></i></button></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- STATUT FINAL (HORIZONTAL LIGHT BUTTONS) --}}
                    <div class="card border-0 shadow-sm card-intervention-box">
                        <div class="card-body p-4">
                            <label class="form-label fw-bold small text-muted text-uppercase mb-3 text-center d-block">Quel
                                est le résultat réel de la réparation ?</label>

                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <input type="radio" class="btn-check" name="statut_final" id="res_repare" value="REPARE"
                                        checked>
                                    <label class="btn btn-light border-0 w-100 p-3 text-start rounded-4 status-selector status-selector-repare"
                                        for="res_repare">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle me-3"><i class="fas fa-check"></i></div>
                                            <div>
                                                <div class="fw-bold small title">Réparé</div>
                                                <div class="text-muted font-size-065">L'appareil fonctionne
                                                    correctement.</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <input type="radio" class="btn-check" name="statut_final" id="res_irreparable"
                                        value="IRREPARABLE">
                                    <label class="btn btn-light border-0 w-100 p-3 text-start rounded-4 status-selector status-selector-irreparable"
                                        for="res_irreparable">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle me-3"><i class="fas fa-times"></i></div>
                                            <div>
                                                <div class="fw-bold small title">Non réparé</div>
                                                <div class="text-muted font-size-065">Appareil irréparable ou
                                                    échec.</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <input type="radio" class="btn-check" name="statut_final" id="res_attente"
                                        value="ATTENTE_PIECE">
                                    <label class="btn btn-light border-0 w-100 p-3 text-start rounded-4 status-selector status-selector-attente"
                                        for="res_attente">
                                        <div class="d-flex align-items-center">
                                            <div class="icon-circle me-3"><i class="fas fa-clock"></i></div>
                                            <div>
                                                <div class="fw-bold small title">Attente Pièce</div>
                                                <div class="text-muted font-size-065">Intervention suspendue.
                                                </div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="text-center">
                                <button type="submit" id="mainSubmitBtn"
                                    class="btn btn-primary shadow rounded-pill px-4 py-2 fw-bold">
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