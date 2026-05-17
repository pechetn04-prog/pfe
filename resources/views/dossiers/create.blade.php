@extends('layouts.app')

@section('title', 'Nouveau Ticket SAV')

@section('content')
    <div class="container-fluid px-4 py-3">
        <!-- En-tête de la page de création de ticket -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h4 fw-bold mb-0">Nouveau Ticket SAV</h1>
                <div class="d-flex align-items-center mt-1">
                    <span class="badge bg-primary bg-opacity-10 text-primary small rounded-pill px-3 py-1">
                        <i class="fas fa-plus-circle me-1"></i> CRÉATION DOSSIER
                    </span>
                </div>
            </div>
            <a href="{{ route('dossiers.index') }}" class="btn btn-white shadow-sm rounded-pill px-4 btn-sm fw-bold border">
                <i class="fas fa-arrow-left me-2"></i> Retour
            </a>
        </div>

        <form action="{{ route('dossiers.store') }}" method="POST" id="createDossierForm">
            @csrf
            <div class="row g-4">
                {{-- COLONNE GAUCHE : Saisie des données --}}
                <div class="col-lg-8">
                    {{-- Bloc : Identification de l'appareil via IMEI --}}
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                        <div class="card-header bg-white border-0 py-3">
                            <h6 class="m-0 fw-bold text-dark d-flex align-items-center">
                                <span class="p-2 bg-primary bg-opacity-10 text-primary rounded-3 me-2"
                                    style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-mobile-alt small"></i>
                                </span>
                                IDENTIFICATION APPAREIL
                            </h6>
                        </div>
                        <div class="card-body pt-0">
                            <div class="mb-4">
                                <label class="form-label text-muted small fw-bold mb-1">NUMÉRO IMEI</label>
                                <div class="input-group input-group-lg bg-light rounded-3 overflow-hidden border-0">
                                    <span class="input-group-text bg-transparent border-0 pe-0">
                                        <i class="fas fa-barcode text-muted opacity-50"></i>
                                    </span>
                                    <input type="text" name="imei" id="imei"
                                        class="form-control bg-transparent border-0 fw-bold"
                                        placeholder="Saisir l'IMEI de l'appareil..." autocomplete="off" required
                                        value="{{ old('imei') }}">
                                </div>
                                <div id="imei-status" class="small mt-2 px-1"></div>
                                <div id="vente-info-display" style="display: none;"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small fw-bold mb-1">MODÈLE / ARTICLE</label>
                                    <input type="text" name="modele" id="modele"
                                        class="form-control bg-light border-0 py-2 fw-bold text-dark" placeholder="---"
                                        required value="{{ old('modele') }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small fw-bold mb-1">RÉFÉRENCE</label>
                                    <input type="text" name="reference_produit" id="reference"
                                        class="form-control bg-light border-0 py-2 fw-bold text-dark" placeholder="---"
                                        value="{{ old('reference_produit') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- INFORMATIONS CLIENT --}}
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                        <div class="card-header bg-white border-0 py-3">
                            <h6 class="m-0 fw-bold text-dark d-flex align-items-center">
                                <span class="p-2 bg-primary bg-opacity-10 text-primary rounded-3 me-2"
                                    style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user small"></i>
                                </span>
                                INFORMATIONS CLIENT
                            </h6>
                        </div>
                        <div class="card-body pt-0">
                            <div class="mb-3">
                                <label class="form-label text-muted small fw-bold mb-1">NOM DU CLIENT</label>
                                <div class="input-group bg-light rounded-3 overflow-hidden border-0">
                                    <span class="input-group-text bg-transparent border-0 pe-0"><i
                                            class="fas fa-user text-muted opacity-50"></i></span>
                                    <input type="text" name="client_nom" id="client_nom"
                                        class="form-control bg-transparent border-0 py-2 fw-bold" placeholder="Nom complet"
                                        required value="{{ old('client_nom') }}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small fw-bold mb-1">TÉLÉPHONE</label>
                                    <div class="input-group bg-light rounded-3 overflow-hidden border-0">
                                        <span class="input-group-text bg-transparent border-0 pe-0"><i
                                                class="fas fa-phone text-muted opacity-50"></i></span>
                                        <input type="text" name="client_telephone" id="client_telephone"
                                            class="form-control bg-transparent border-0 py-2 fw-bold"
                                            placeholder="0X XX XX XX XX" required value="{{ old('client_telephone') }}">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-muted small fw-bold mb-1">EMAIL</label>
                                    <div class="input-group bg-light rounded-3 overflow-hidden border-0">
                                        <span class="input-group-text bg-transparent border-0 pe-0"><i
                                                class="fas fa-envelope text-muted opacity-50"></i></span>
                                        <input type="email" name="client_email" id="client_email"
                                            class="form-control bg-transparent border-0 py-2 fw-bold"
                                            placeholder="client@email.com" value="{{ old('client_email') }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ACCESSOIRES REMIS --}}
                    <div class="card border-0 shadow-sm" style="border-radius: 16px;">
                        <div class="card-header bg-white border-0 py-3">
                            <h6 class="m-0 fw-bold text-dark d-flex align-items-center">
                                <span class="p-2 bg-primary bg-opacity-10 text-primary rounded-3 me-2"
                                    style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-boxes small"></i>
                                </span>
                                ACCESSOIRES REMIS
                            </h6>
                        </div>
                        <div class="card-body pt-0">
                            <div class="row row-cols-2 row-cols-md-4 g-3 mb-3">
                                @foreach(['Chargeur', 'Câble USB', 'Batterie', 'Carte SIM', 'Carte Mémoire', 'Coque / Étui', 'Autre...'] as $acc)
                                    <div class="col">
                                        <div class="form-check">
                                            <input class="form-check-input acc-checkbox" type="checkbox" name="accessoires[]"
                                                value="{{ $acc }}" id="acc_{{ Str::slug($acc) }}">
                                            <label class="form-check-label text-muted small" for="acc_{{ Str::slug($acc) }}">
                                                {{ $acc }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div id="autre_accessoire_container" style="display: none;">
                                <label class="form-label text-muted small fw-bold mb-2">PRÉCISEZ LES ACCESSOIRES</label>
                                <input type="text" name="accessoires_autre" id="accessoires_autre_input"
                                    class="form-control bg-light border-0 rounded-3 small"
                                    placeholder="Saisir les autres accessoires..." value="{{ old('accessoires_autre') }}">
                            </div>


                        </div>
                    </div>
                </div>

                {{-- COLONNE DROITE --}}
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm sticky-top" style="border-radius: 16px; top: 1.5rem;">
                        <div class="card-header bg-white border-0 py-3">
                            <h6 class="m-0 fw-bold text-dark d-flex align-items-center">
                                <span class="p-2 bg-primary bg-opacity-10 text-primary rounded-3 me-2"
                                    style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-tools small"></i>
                                </span>
                                DÉTAILS SAV
                            </h6>
                        </div>
                        <div class="card-body pt-0">
                            {{-- Pannes déclarées --}}
                            <label class="form-label text-muted small fw-bold mb-3 d-flex align-items-center">
                                <i class="fas fa-search me-2 text-primary"></i> PANNE(S) DÉCLARÉE(S)
                            </label>
                            <div class="row row-cols-2 g-3 mb-4">

                                @foreach($pannes as $panne)
                                    <div class="col">
                                        <div class="form-check small">
                                            <input class="form-check-input panne-checkbox" type="checkbox" name="type_pannes[]"
                                                value="{{ $panne }}" id="panne_{{ Str::slug($panne) }}">
                                            <label class="form-check-label text-muted" for="panne_{{ Str::slug($panne) }}">
                                                {{ $panne }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mb-4" id="autre_panne_container">
                                <label class="form-label text-muted small fw-bold mb-2">
                                    <i class="fas fa-edit me-1 text-primary"></i> DESCRIPTION DÉTAILLÉE / CONSTAT DE PANNE <span class="text-danger">*</span>
                                </label>
                                <textarea name="panne_declaree" id="panne_declaree_input"
                                    class="form-control bg-light border-0 rounded-3 small" rows="3"
                                    placeholder="Décrivez précisément les symptômes constatés par le client..." required>{{ old('panne_declaree') }}</textarea>
                            </div>


                            <div id="technicien_selection_container">
                                <div class="mb-4">
                                    <label class="form-label text-muted small fw-bold mb-2">
                                        <i class="fas fa-user-cog me-1"></i> TECHNICIEN ASSIGNÉ
                                    </label>

                                    <div id="no_panne_message" class="alert alert-light border small py-2 text-muted">
                                        <i class="fas fa-info-circle me-1"></i> Sélectionnez une panne pour voir les
                                        techniciens.
                                    </div>

                                    <div id="technicien_select_wrapper" style="display: none;">
                                        <select name="technicien_id" id="technicien_id"
                                            class="form-select bg-light border-0 rounded-3 small fw-bold py-2">
                                            <option value="">-- Choisir un technicien --</option>
                                            @foreach($techniciens as $tech)
                                                <option value="{{ $tech->id }}" data-specialite="{{ $tech->specialite ?? '' }}"
                                                    {{ old('technicien_id') == $tech->id ? 'selected' : '' }}>
                                                    {{ $tech->name }} ({{ $tech->dossiers_en_cours }} en cours)
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="mt-2 text-muted" style="font-size: 0.7rem;">
                                            <i class="fas fa-info-circle me-1"></i> La liste est suggérée selon la
                                            spécialité.
                                        </div>
                                    </div>
                                </div>
                            </div>



                            <div class="mt-4 text-end">
                                <button type="submit" class="btn btn-primary rounded-pill fw-bold px-4 py-2 shadow-sm">
                                    <i class="fas fa-save me-2"></i> ENREGISTRER TICKET
                                </button>
                                <div class="mt-2">
                                    <a href="{{ route('dossiers.index') }}"
                                        class="btn btn-link btn-sm text-decoration-none text-muted">Annuler</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/dossier-create.css') }}">
    @endpush

    @push('scripts')
        <script>
            // Passage des variables Laravel au JS externe
            window.checkImeiRoute = "{{ route('dossiers.checkImei') }}";
        </script>
        <script src="{{ asset('js/dossier_create.js') }}"></script>
    @endpush
@endsection