@extends('layouts.app')

@section('title', 'Expertise & Diagnostic Technique')




@section('content')
    {{-- Conteneur principal de la page de diagnostic --}}
    <div class="container-fluid px-4 py-4">
        {{-- En-tête : Titre de la page, numéro du dossier en cours et actions rapides --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 fw-bold mb-0 text-gray-800">Expertise & Diagnostic Technique</h1>
                <p class="text-muted mb-0">Dossier <strong class="text-primary">#{{ $dossier->num_dossier }}</strong> —
                    Phase d'expertise</p>
            </div>
            <div class="d-flex gap-2">
                {{-- Bouton pour ouvrir la boîte de dialogue (modal) de demande de retrait --}}
                <button type="button" class="btn btn-outline-danger rounded-pill px-4 fw-bold" data-bs-toggle="modal"
                    data-bs-target="#modalRetrait">
                    <i class="fas fa-undo-alt me-1"></i> Demander Retrait
                </button>
                {{-- Bouton pour annuler et retourner à la liste des tickets affectés au technicien --}}
                <a href="{{ route('technicien.tickets') }}" class="btn btn-secondary rounded-pill px-4">
                    <i class="fas fa-times me-2"></i> Annuler
                </a>
            </div>
        </div>

        {{-- Formulaire principal de soumission du diagnostic technique (inclut l'upload de photo) --}}
        <form action="{{ route('diagnostics.store', $dossier->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-4">
                {{-- BARRE LATÉRALE (SIDEBAR) : Résumé des informations du dossier SAV --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm sticky-top card-diagnostic-sidebar">
                        <div class="card-header bg-white border-0 py-3">
                            <h6 class="m-0 fw-bold d-flex align-items-center gap-2">
                                <i class="fas fa-info-circle text-primary"></i> RÉSUMÉ DU DOSSIER
                            </h6>
                        </div>
                        <div class="card-body p-4 pt-0">
                            {{-- Informations d'identification de l'appareil --}}
                            <div class="mb-4">
                                <label class="small text-muted fw-bold text-uppercase mb-2 d-block">Appareil &
                                    Identification</label>
                                <div class="p-3 bg-light rounded-3 border-start border-4 border-primary">
                                    <div class="fw-bold h5 mb-1">{{ $dossier->appareil->modele ?? 'Modèle Inconnu' }}</div>
                                    <div class="small text-muted font-monospace">IMEI: {{ $dossier->imei }}</div>
                                    <div class="mt-2">
                                        {{-- Badge indiquant si l'appareil est couvert par la garantie --}}
                                        <span
                                            class="badge rounded-pill {{ $dossier->sous_garantie ? 'bg-success' : 'bg-secondary' }}">
                                            {{ $dossier->sous_garantie ? '✓ SOUS GARANTIE' : 'HORS GARANTIE' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Description du problème déclarée initialement par le client --}}
                            <div class="mb-4">
                                <label class="small text-muted fw-bold text-uppercase mb-2 d-block">Panne Déclarée par le
                                    Client</label>
                                <div class="p-3 bg-light rounded-3 border">
                                    <p class="mb-0 fst-italic text-dark">"{{ $dossier->panne_declaree }}"</p>
                                </div>
                            </div>

                            {{-- Date de réception physique du matériel en atelier --}}
                            <div class="mb-0">
                                <label class="small text-muted fw-bold text-uppercase mb-2 d-block">Informations de
                                    Réception</label>
                                <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded-2 small">
                                    <span class="text-muted">Reçu le :</span>
                                    <span
                                        class="fw-bold">{{ $dossier->date_reception->format('d/m/Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Section d'importation de pièce jointe (constat visuel en photo) --}}
                    <div class="card border-0 shadow-sm mt-4 card-diagnostic-box">
                        <div class="card-header bg-white border-0 py-3 text-center">
                            <h6 class="m-0 fw-bold text-muted small text-uppercase">
                                <i class="fas fa-camera text-primary me-2"></i>PIÈCE JOINTE
                            </h6>
                        </div>
                        <div class="card-body p-4 pt-0 text-center">
                            {{-- Zone glisser-déposer / clic pour charger une image --}}
                            <div class="upload-area border border-2 border-dashed rounded-4 p-4 bg-light mb-2 cursor-pointer position-relative">
                                <i class="fas fa-cloud-upload-alt fa-2x text-primary mb-2"></i>
                                <div class="small fw-bold text-dark" id="file-label">Choisir une photo</div>
                                <input type="file" name="photo_panne" id="photo_panne" class="position-absolute w-100 h-100 top-0 start-0 opacity-0 cursor-pointer-custom" accept="image/*">
                            </div>
                            <small class="text-muted small font-size-065">Photo du constat technique ou justificatif</small>
                        </div>
                    </div>
                </div>

                {{-- FORMULAIRE PRINCIPAL (MAIN FORM) : Saisie des données du diagnostic --}}
                <div class="col-lg-8">
                    <div class="d-flex flex-column gap-4">
                        {{-- 1. Section : Analyse Technique & Observations --}}
                        <div class="card border-0 shadow-sm card-diagnostic-box">
                            <div class="card-header bg-white border-0 py-3">
                                <h6 class="m-0 fw-bold text-primary d-flex align-items-center">
                                    <i class="fas fa-stethoscope me-2"></i>1. ANALYSE TECHNIQUE
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                {{-- Constat technique obligatoire --}}
                                <div class="mb-4">
                                    <label class="form-label fw-bold small text-uppercase text-muted">Constat Technique
                                        <span class="text-danger">*</span></label>
                                    <textarea name="constat_technique" class="form-control bg-light border-0 rounded-3 p-3"
                                        rows="4"
                                        placeholder="Décrivez précisément les défauts constatés après expertise technique..."
                                        required></textarea>
                                </div>
                                {{-- Recommandations ou solutions préconisées par le technicien --}}
                                <div class="mb-0">
                                    <label class="form-label fw-bold small text-uppercase text-muted">Recommandations &
                                        Solutions</label>
                                    <textarea name="recommandation" class="form-control bg-light border-0 rounded-3 p-3"
                                        rows="3"
                                        placeholder="Quels sont les travaux nécessaires pour la remise en état ?"></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- 2. Section : Pièces Détachées Nécessaires --}}
                        <div class="card border-0 shadow-sm card-diagnostic-box">
                            <div
                                class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 fw-bold text-primary d-flex align-items-center">
                                    <i class="fas fa-microchip me-2"></i>2. PIÈCES DÉTACHÉES NÉCESSAIRES
                                </h6>
                                {{-- Bouton pour ajouter dynamiquement une nouvelle ligne de pièce via Javascript --}}
                                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm"
                                    id="add-piece-row">
                                    <i class="fas fa-plus me-1"></i> Ajouter une ligne
                                </button>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-borderless align-middle mb-0" id="pieces-table">
                                        <thead class="small text-muted text-uppercase bg-light font-size-065 letter-spacing-05">
                                            <tr>
                                                <th class="ps-4">RÉFÉRENCE / DÉSIGNATION</th>
                                                <th class="text-center w-col-120">QUANTITÉ</th>
                                                <th class="text-end pe-4 w-col-50"></th>
                                            </tr>
                                        </thead>
                                        <tbody class="border-top border-light">
                                            {{-- Ligne modèle par défaut pour la sélection d'une pièce --}}
                                            <tr class="piece-row">
                                                <td class="ps-4">
                                                    <select name="pieces[0][id]"
                                                        class="form-select border-0 bg-light rounded-3 piece-select">
                                                        <option value="">Choisir une pièce...</option>
                                                        @foreach($pieces as $p)
                                                            <option value="{{ $p->id }}" data-stock="{{ $p->quantite }}">
                                                                {{ $p->nom }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="number" name="pieces[0][quantite]"
                                                        class="form-control border-0 bg-light text-center rounded-3 fw-bold"
                                                        value="1" min="1">
                                                </td>
                                                <td class="text-end pe-4">
                                                    {{-- Bouton de suppression de la ligne --}}
                                                    <button type="button"
                                                        class="btn btn-light btn-sm rounded-circle shadow-sm remove-row btn-remove-circle">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- 3. Section : Prestations Techniques / Main d'œuvre --}}
                        <div class="card border-0 shadow-sm card-diagnostic-box">
                            <div
                                class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                                <h6 class="m-0 fw-bold text-primary d-flex align-items-center">
                                    <i class="fas fa-user-cog me-2"></i>3. PRESTATIONS TECHNIQUES
                                </h6>
                                {{-- Bouton pour ajouter dynamiquement une ligne de prestation (main d'œuvre) via Javascript --}}
                                <button type="button" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm"
                                    id="add-presta-row">
                                    <i class="fas fa-plus me-1"></i> Ajouter une ligne
                                </button>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-borderless align-middle mb-0" id="presta-table">
                                        <thead class="small text-muted text-uppercase bg-light font-size-065 letter-spacing-05">
                                            <tr>
                                                <th class="ps-4">DÉSIGNATION PRESTATION</th>
                                                <th class="text-center w-col-180">MONTANT (DT)</th>
                                                <th class="text-end pe-4 w-col-50"></th>
                                            </tr>
                                        </thead>
                                        <tbody class="border-top border-light">
                                            {{-- Ligne modèle par défaut pour la sélection d'une prestation horaire/forfaitaire --}}
                                            <tr class="presta-row">
                                                <td class="ps-4">
                                                    <select name="labors[]"
                                                        class="form-select border-0 bg-light rounded-3 presta-select">
                                                        <option value="">Choisir une prestation...</option>
                                                        @foreach($tarifsMo as $t)
                                                            <option value="{{ $t->id }}" data-price="{{ $t->montant }}">
                                                                {{ $t->type_intervention }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                {{-- Zone d'affichage dynamique du prix de la main d'œuvre --}}
                                                <td class="text-center fw-bold text-primary price-display h6 mb-0">—</td>
                                                <td class="text-end pe-4">
                                                    {{-- Bouton de suppression de la ligne --}}
                                                    <button type="button"
                                                        class="btn btn-light btn-sm rounded-circle shadow-sm remove-row btn-remove-circle">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        {{-- 4. Section : Décision finale & validation de réparabilité --}}
                        <div class="card border-0 shadow-sm card-diagnostic-box">
                            <div class="card-body p-4">
                                <label class="small fw-bold text-uppercase text-muted mb-3 d-block">Est ce que on peut
                                    reparer appareil?</label>
                                <div class="row align-items-center g-4">
                                    {{-- Sélecteurs d'état (Oui/Non) avec boutons radio stylisés --}}
                                    <div class="col-md-7">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <input type="radio" class="btn-check" name="is_reparable" id="rep_oui"
                                                    value="1" checked>
                                                <label
                                                    class="btn btn-light border-0 w-100 p-3 text-start rounded-4 status-selector status-selector-oui"
                                                    for="rep_oui">
                                                    <div class="d-flex align-items-center">
                                                        <div class="icon-circle me-3"><i class="fas fa-check"></i></div>
                                                        <div>
                                                            <div class="fw-bold small title text-uppercase">Oui</div>
                                                            <div class="text-muted" style="font-size: 0.65rem;"></div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                            <div class="col-md-6">
                                                <input type="radio" class="btn-check" name="is_reparable" id="rep_non"
                                                    value="0">
                                                <label
                                                    class="btn btn-light border-0 w-100 p-3 text-start rounded-4 status-selector status-selector-non"
                                                    for="rep_non">
                                                    <div class="d-flex align-items-center">
                                                        <div class="icon-circle me-3"><i class="fas fa-times"></i></div>
                                                        <div>
                                                            <div class="fw-bold small title text-uppercase">Non</div>
                                                            <div class="text-muted" style="font-size: 0.65rem;"></div>
                                                        </div>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    {{-- Options d'exclusion de garantie (Visible uniquement si l'appareil était sous garantie) --}}
                                    @if($dossier->sous_garantie)
                                        <div class="col-md-5">
                                            <div
                                                class="bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-3 p-3 h-100 d-flex flex-column">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="checkbox" name="exclusion_garantie"
                                                        id="exclure">
                                                    <label class="form-check-label text-danger fw-bold" for="exclure">
                                                        EXCLUSION DE GARANTIE
                                                    </label>
                                                </div>
                                                {{-- Formulaire d'informations complémentaires d'exclusion (démasqué par JS lors de la sélection de la case) --}}
                                                <div id="exclusion-details" style="display: none;">
                                                    <select name="motif_exclusion" class="form-select form-select-sm bg-white border-danger border-opacity-25 mb-2 font-size-075">
                                                        <option value="Usage non conforme">Usage non conforme</option>
                                                        <option value="Choc / Casse">Choc / Casse</option>
                                                        <option value="Oxydation / Humidité">Oxydation / Humidité</option>
                                                        <option value="Tentative de réparation tierce">Tentative de réparation tierce</option>
                                                        <option value="Autre">Autre</option>
                                                    </select>
                                                    <textarea name="exclusion_commentaire" class="form-control form-control-sm border-danger border-opacity-25 font-size-075" rows="2" 
                                                    placeholder="Précisez le motif de l'exclusion..."></textarea>
                                                </div>
                                                <small class="text-muted d-block mt-auto" id="exclusion-hint">Usage non conforme (Choc,
                                                    Humidité...)</small>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            {{-- Section de pied de carte contenant le bouton de validation finale --}}
                            <div class="card-footer bg-light border-0 p-4 text-end">
                                <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm">
                                    <i class="fas fa-save me-2"></i> ENREGISTRER LE DIAGNOSTIC TECHNIQUE
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- MODAL : Retrait du dossier (Permet au technicien de se désister ou de rejeter le dossier) --}}
    <div class="modal fade" id="modalRetrait" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow modal-diagnostic-radius">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold text-danger">Motif du retrait</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('dossiers.rejeter', $dossier->id) }}" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <p class="text-muted small mb-4">Veuillez expliquer pourquoi vous souhaitez vous retirer de ce
                            dossier ou rejeter le diagnostic.</p>
                        <div class="mb-0">
                            <label class="form-label small fw-bold">Raison du retrait <span
                                    class="text-danger">*</span></label>
                            <textarea name="raison" class="form-control bg-light border-0 rounded-3" rows="4"
                                placeholder="Ex: Compétence technique non adaptée, pièces indisponibles, etc..." required
                                minlength="10"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">CONFIRMER LE RETRAIT</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Script JavaScript dédié pour la gestion de l'interactivité dynamique (ajout/suppression de lignes, calculs) --}}
    <script src="{{ asset('js/diagnostic_create.js') }}"></script>
@endpush