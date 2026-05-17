$(document).ready(function () {
    function checkImei() {
        let imei = $('#imei').val().trim();
        if (imei.length >= 5) {
            $('#imei-status').html('<i class="fas fa-spinner fa-spin text-muted"></i> Vérification...');
            $.ajax({
                url: window.checkImeiRoute,
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
                                <div class="alert alert-danger bg-white border-danger border-2 mt-3 p-0 overflow-hidden shadow-sm" style="border-radius: 12px;">
                                    <div class="bg-danger bg-opacity-10 px-3 py-2 border-bottom border-danger border-opacity-25">
                                        <span class="fw-bold text-danger"><i class="fas fa-info-circle me-2"></i>NOTE SAV</span>
                                    </div>
                                    <div class="p-3">
                                        <p class="small mb-0 text-muted"><strong>Note :</strong> Cet appareil est connu du SAV mais n'est pas répertorié dans notre base de ventes. Prise en charge effectuée en mode <strong class="text-danger">HORS GARANTIE</strong>.</p>
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
                            <div class="alert alert-danger bg-white border-danger border-2 mt-3 p-0 overflow-hidden shadow-sm" style="border-radius: 12px;">
                                <div class="bg-danger bg-opacity-10 px-3 py-2 border-bottom border-danger border-opacity-25">
                                    <span class="fw-bold text-danger"><i class="fas fa-info-circle me-2"></i>NOTE SAV</span>
                                </div>
                                <div class="p-3">
                                    <p class="small mb-0 text-muted"><strong>Avis :</strong> Cet IMEI n'est pas répertorié dans notre registre des ventes. Ce dossier sera automatiquement traité selon les conditions <strong class="text-danger">HORS GARANTIE</strong>.</p>
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
