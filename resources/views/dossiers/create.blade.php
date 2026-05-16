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
                                @php
                                    $pannes = [
                                        'Écran & Affichage',
                                        'Batterie & Alimentation',
                                        'Connectique & Ports',
                                        'Caméra',
                                        'Audio',
                                        'Connectivité',
                                        'Logiciel & Système',
                                        'Dommages Physiques',
                                        'Sécurité & Accès',
                                        'Autre'
                                    ];
                                @endphp
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

                            <div id="autre_panne_container" class="mb-4" style="display: none;">
                                <label class="form-label text-muted small fw-bold mb-2">PRÉCISEZ LA PANNE</label>
                                <textarea name="panne_declaree" id="panne_declaree_input"
                                    class="form-control bg-light border-0 rounded-3 small" rows="3"
                                    placeholder="Détails de la panne..." required>{{ old('panne_declaree') }}</textarea>
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
            $(document).ready(function () {
                function checkImei() {
                    let imei = $('#imei').val().trim();
                    if (imei.length >= 5) {
                        $('#imei-status').html('<i class="fas fa-spinner fa-spin text-muted"></i> Vérification...');
                        $.ajax({
                            url: "{{ route('dossiers.checkImei') }}",
                            method: "GET",
                            data: { imei: imei },
                            success: function (response) {
                                if (response.found) {
                                    $('#imei-status').html('<span class="text-success fw-bold small"><i class="fas fa-check-circle me-1"></i> Appareil reconnu (' + response.source + ')</span>');
                                    $('#modele').val(response.device.modele);
                                    $('#reference').val(response.device.reference_produit);
                                    if (response.device.client_nom) $('#client_nom').val(response.device.client_nom);
                                    if (response.device.client_email) $('#client_email').val(response.device.client_email);
                                    if (response.device.client_telephone) $('#client_telephone').val(response.device.client_telephone);

                                    // Verrouiller les champs si trouvé
                                    $('#modele, #reference').attr('readonly', true).addClass('bg-light');

                                    if (response.vente) {
                                        let gColor = response.vente.sous_garantie ? 'success' : 'danger';
                                        let gText = response.vente.sous_garantie ? 'SOUS GARANTIE' : 'HORS GARANTIE';
                                        let html = `
                                        <div class="alert alert-${gColor} bg-white border-${gColor} border-2 mt-3 p-0 overflow-hidden shadow-sm" style="border-radius: 12px;">
                                            <div class="bg-${gColor} bg-opacity-10 px-3 py-2 border-bottom border-${gColor} border-opacity-25 d-flex justify-content-between align-items-center">
                                                <span class="fw-bold text-${gColor}"><i class="fas fa-check-circle me-2"></i>${gText}</span>
                                                <span class="badge bg-${gColor} small">Vérifié</span>
                                            </div>
                                            <div class="p-3">
                                                <div class="row g-3 small">
                                                    <div class="col-md-4">
                                                        <label class="text-muted d-block mb-0">Client</label>
                                                        <span class="fw-bold text-dark">${response.device.client_nom || '—'}</span>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="text-muted d-block mb-0">Article</label>
                                                        <span class="fw-bold text-dark">${response.device.modele}</span>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="text-muted d-block mb-0">Date de vente</label>
                                                        <span class="fw-bold text-dark">${response.vente.date_vente}</span>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="text-muted d-block mb-0">N° Facture</label>
                                                        <span class="fw-bold text-dark">${response.vente.facture}</span>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="text-muted d-block mb-0">Garantie</label>
                                                        <span class="fw-bold text-dark">${response.vente.duree}</span>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="text-muted d-block mb-0">Fin garantie</label>
                                                        <span class="fw-bold text-dark">${response.vente.fin_garantie}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `;
                                        $('#vente-info-display').html(html).fadeIn();
                                    } else {
                                        let html = `
                                            <div class="alert alert-secondary bg-white border-secondary border-2 mt-3 p-0 overflow-hidden shadow-sm" style="border-radius: 12px;">
                                                <div class="bg-secondary bg-opacity-10 px-3 py-2 border-bottom border-secondary border-opacity-25">
                                                    <span class="fw-bold text-secondary"><i class="fas fa-info-circle me-2"></i>NOTE SAV</span>
                                                </div>
                                                <div class="p-3">
                                                    <p class="small mb-0 text-muted">Cet appareil est connu du SAV mais <strong>non répertorié dans nos ventes</strong>. Le dossier sera traité <strong>HORS GARANTIE</strong>.</p>
                                                </div>
                                            </div>
                                        `;
                                        $('#vente-info-display').html(html).fadeIn();
                                    }
                                } else {
                                    $('#imei-status').html('<span class="text-danger small fw-bold"><i class="fas fa-exclamation-triangle me-1"></i> ' + response.message + '</span>');

                                    // Déverrouiller si non trouvé
                                    $('#modele, #reference').attr('readonly', false).removeClass('bg-light');

                                    let html = `
                                        <div class="alert alert-secondary bg-white border-secondary border-2 mt-3 p-0 overflow-hidden shadow-sm" style="border-radius: 12px;">
                                            <div class="bg-secondary bg-opacity-10 px-3 py-2 border-bottom border-secondary border-opacity-25">
                                                <span class="fw-bold text-secondary"><i class="fas fa-info-circle me-2"></i>NOTE SAV</span>
                                            </div>
                                            <div class="p-3">
                                                <p class="small mb-0 text-muted">Cet appareil ne fait pas partie de notre base de données de vente. Le dossier sera traité <strong>HORS GARANTIE</strong>.</p>
                                            </div>
                                        </div>
                                    `;
                                    $('#vente-info-display').html(html).fadeIn();
                                }
                            }
                        });
                    }
                }

                let imeiTimeout = null;
                $('#imei').on('input', function () {
                    clearTimeout(imeiTimeout);
                    let val = $(this).val().trim();
                    if (val.length >= 5) {
                        imeiTimeout = setTimeout(checkImei, 500);
                    } else {
                        $('#modele, #reference, #client_nom, #client_telephone, #client_email').val('');
                        $('#modele, #reference').attr('readonly', false).removeClass('bg-light');
                        $('#imei-status').empty();
                        $('#vente-info-display').empty().hide();
                    }
                });

                // Trigger auto-check if IMEI is already in input (e.g. from redirect)
                if ($('#imei').val().length >= 5) {
                    checkImei();
                }

                // Filtrage des techniciens par spécialité
                function filterTechnicians() {
                    let selectedPannes = [];
                    $('input[name="type_pannes[]"]:checked').each(function () {
                        selectedPannes.push($(this).val());
                    });

                    $('#technicien_id option').each(function () {
                        let option = $(this);
                        let techSpecialites = option.data('specialite') || '';

                        if (option.val() === "") return; // Garder l'option par défaut

                        if (selectedPannes.length === 0) {
                            option.prop('disabled', false).show();
                            option.css('display', '');
                            return;
                        }

                        let matchesAll = true;
                        selectedPannes.forEach(function (panne) {
                            if (!techSpecialites.toLowerCase().includes(panne.toLowerCase())) {
                                matchesAll = false;
                            }
                        });

                        if (matchesAll) {
                            option.prop('disabled', false).show().css('display', '');
                            option.css('color', '#2563eb');
                        } else {
                            option.prop('disabled', true).hide().css('display', 'none');
                            if (option.is(':selected')) {
                                $('#technicien_id').val("");
                            }
                        }
                    });
                }

                // Logique pour les accessoires "Autre"
                $('input[name="accessoires[]"]').on('change', function () {
                    let isAutreAccChecked = false;
                    $('input[name="accessoires[]"]:checked').each(function () {
                        if ($(this).val() === 'Autre...') {
                            isAutreAccChecked = true;
                        }
                    });

                    if (isAutreAccChecked) {
                        $('#autre_accessoire_container').fadeIn();
                        $('#accessoires_autre_input').prop('required', true).focus();
                    } else {
                        $('#autre_accessoire_container').fadeOut();
                        $('#accessoires_autre_input').prop('required', false).val('');
                    }
                });

                $('input[name="type_pannes[]"]').on('change', function () {
                    filterTechnicians();

                    // Logique pour afficher le champ "Autre"
                    let isAutreChecked = false;
                    $('input[name="type_pannes[]"]:checked').each(function () {
                        if ($(this).val() === 'Autre') {
                            isAutreChecked = true;
                        }
                    });

                    if (isAutreChecked) {
                        $('#autre_panne_container').fadeIn();
                        $('#panne_declaree_input').prop('required', true);
                    } else {
                        $('#autre_panne_container').fadeOut();
                        $('#panne_declaree_input').prop('required', false).val('');
                    }

                    // Logique pour afficher le select Technicien seulement si une panne est sélectionnée
                    if ($('input[name="type_pannes[]"]:checked').length > 0) {
                        $('#no_panne_message').hide();
                        $('#technicien_select_wrapper').fadeIn();
                    } else {
                        $('#no_panne_message').show();
                        $('#technicien_select_wrapper').hide();
                        $('#technicien_id').val('');
                    }
                });


            });
        </script>
    @endpush
@endsection