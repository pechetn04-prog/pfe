@extends('layouts.app')

@section('title', 'Nouveau Ticket SAV')

@section('content')
<div class="container-fluid px-4 py-3">
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
            {{-- COLONNE GAUCHE --}}
            <div class="col-lg-8">
                {{-- IDENTIFICATION APPAREIL --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="m-0 fw-bold text-dark d-flex align-items-center">
                            <span class="p-2 bg-primary bg-opacity-10 text-primary rounded-3 me-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
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
                                <input type="text" name="imei" id="imei" class="form-control bg-transparent border-0 fw-bold" 
                                    placeholder="Saisir l'IMEI de l'appareil..." autocomplete="off" required value="{{ old('imei') }}">
                            </div>
                            <div id="imei-status" class="small mt-2 px-1"></div>
                            <div id="vente-info-display" style="display: none;"></div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small fw-bold mb-1">MODÈLE / ARTICLE</label>
                                <input type="text" name="modele" id="modele" class="form-control bg-light border-0 py-2 fw-bold text-dark" 
                                    placeholder="---" required value="{{ old('modele') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small fw-bold mb-1">RÉFÉRENCE</label>
                                <input type="text" name="reference_produit" id="reference" class="form-control bg-light border-0 py-2 fw-bold text-dark" 
                                    placeholder="---" value="{{ old('reference_produit') }}">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- INFORMATIONS CLIENT --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 16px;">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="m-0 fw-bold text-dark d-flex align-items-center">
                            <span class="p-2 bg-primary bg-opacity-10 text-primary rounded-3 me-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-user small"></i>
                            </span>
                            INFORMATIONS CLIENT
                        </h6>
                    </div>
                    <div class="card-body pt-0">
                        <div class="mb-3">
                            <label class="form-label text-muted small fw-bold mb-1">NOM DU CLIENT</label>
                            <div class="input-group bg-light rounded-3 overflow-hidden border-0">
                                <span class="input-group-text bg-transparent border-0 pe-0"><i class="fas fa-user text-muted opacity-50"></i></span>
                                <input type="text" name="client_nom" id="client_nom" class="form-control bg-transparent border-0 py-2 fw-bold" 
                                    placeholder="Nom complet" required value="{{ old('client_nom') }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small fw-bold mb-1">TÉLÉPHONE</label>
                                <div class="input-group bg-light rounded-3 overflow-hidden border-0">
                                    <span class="input-group-text bg-transparent border-0 pe-0"><i class="fas fa-phone text-muted opacity-50"></i></span>
                                    <input type="text" name="client_telephone" id="client_telephone" class="form-control bg-transparent border-0 py-2 fw-bold" 
                                        placeholder="0X XX XX XX XX" required value="{{ old('client_telephone') }}">
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label text-muted small fw-bold mb-1">EMAIL</label>
                                <div class="input-group bg-light rounded-3 overflow-hidden border-0">
                                    <span class="input-group-text bg-transparent border-0 pe-0"><i class="fas fa-envelope text-muted opacity-50"></i></span>
                                    <input type="email" name="client_email" id="client_email" class="form-control bg-transparent border-0 py-2 fw-bold" 
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
                            <span class="p-2 bg-primary bg-opacity-10 text-primary rounded-3 me-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-boxes small"></i>
                            </span>
                            ACCESSOIRES REMIS
                        </h6>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row row-cols-2 row-cols-md-3 g-3">
                            @foreach(['Chargeur', 'Câble USB', 'Batterie', 'Carte SIM', 'Carte Mémoire', 'Coque / Étui', 'Autre...'] as $acc)
                            <div class="col">
                                <div class="form-check custom-check p-3 rounded-3 border bg-light bg-opacity-25 h-100 d-flex align-items-center">
                                    <input class="form-check-input ms-0 me-3" type="checkbox" name="accessoires[]" value="{{ $acc }}" id="acc_{{ Str::slug($acc) }}">
                                    <label class="form-check-label text-dark fw-medium small mb-0" for="acc_{{ Str::slug($acc) }}">
                                        {{ $acc }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLONNE DROITE --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm sticky-top" style="border-radius: 16px; top: 1.5rem;">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="m-0 fw-bold text-dark d-flex align-items-center">
                            <span class="p-2 bg-primary bg-opacity-10 text-primary rounded-3 me-2" style="width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
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
                                    'Écran & Affichage', 'Batterie & Alimentation', 
                                    'Connectique & Ports', 'Caméra',
                                    'Audio', 'Connectivité',
                                    'Logiciel & Système', 'Dommages Physiques',
                                    'Sécurité & Accès'
                                ];
                            @endphp
                            @foreach($pannes as $panne)
                            <div class="col">
                                <div class="form-check small">
                                    <input class="form-check-input" type="checkbox" name="type_pannes[]" value="{{ $panne }}" id="panne_{{ Str::slug($panne) }}">
                                    <label class="form-check-label text-muted" for="panne_{{ Str::slug($panne) }}">
                                        {{ $panne }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold mb-2">PRÉCISIONS SUPPLÉMENTAIRES</label>
                            <textarea name="panne_declaree" class="form-control bg-light border-0 rounded-3 small" rows="5" 
                                placeholder="Ajouter des détails sur la panne..." required>{{ old('panne_declaree') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label text-muted small fw-bold mb-2">
                                <i class="fas fa-user-cog me-1"></i> TECHNICIEN ASSIGNÉ
                            </label>
                            <select name="technicien_id" class="form-select bg-light border-0 rounded-3 small fw-bold py-2">
                                <option value="">-- Choisir un technicien --</option>
                                @foreach($techniciens as $tech)
                                    <option value="{{ $tech->id }}" {{ old('technicien_id') == $tech->id ? 'selected' : '' }}>
                                        {{ $tech->name }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="mt-2 text-muted" style="font-size: 0.7rem;">
                                <i class="fas fa-info-circle me-1"></i> La liste est mise à jour selon la panne pour suggérer les meilleurs techniciens.
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold py-3 shadow-sm">
                                <i class="fas fa-save me-2"></i> ENREGISTRER TICKET
                            </button>
                            <a href="{{ route('dossiers.index') }}" class="btn btn-link btn-sm text-decoration-none text-muted text-center">Annuler</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .bg-light { background-color: #f8fafc !important; }
    .custom-check:hover { background-color: #f1f5f9 !important; border-color: #2563eb !important; }
    .form-check-input:checked { background-color: #2563eb; border-color: #2563eb; }
    .input-group-text { min-width: 45px; justify-content: center; }
    .form-control:focus, .form-select:focus { background-color: #fff !important; box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1); border-color: #2563eb !important; }
    .btn-white:hover { background-color: #f8fafc; }
</style>

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
        $('#imei').on('input', function() {
            clearTimeout(imeiTimeout);
            let val = $(this).val().trim();
            if (val.length >= 5) {
                imeiTimeout = setTimeout(checkImei, 500);
            } else {
                $('#modele, #reference, #client_nom, #client_telephone, #client_email').val('');
                $('#imei-status').empty();
                $('#vente-info-display').empty().hide();
            }
        });
    });
</script>
@endpush
@endsection