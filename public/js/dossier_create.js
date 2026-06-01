// Attend que la page HTML soit complètement chargée et prête
$(document).ready(function () {

    // Déclare la fonction principale pour vérifier l'IMEI avec AJAX
    function checkImei() {
        // Récupère la valeur saisie, et retire les espaces superflus au début et à la fin
        let imei = $('#imei').val().trim();
        
        // Exécute la vérification uniquement si la saisie contient au moins 5 caractères
        if (imei.length >= 5) {
            // Affiche un indicateur visuel de chargement avec une icône animée de chargement
            $('#imei-status').html('<i class="fas fa-spinner fa-spin text-muted"></i> Vérification...');
            
            // Lance la requête asynchrone AJAX vers le serveur Laravel
            $.ajax({
                // Définit l'adresse du serveur à interroger (récupérée de la vue Blade)
                url: window.checkImeiRoute, 
                // Utilise la méthode HTTP GET pour récupérer les données de garantie
                method: "GET",
                // Envoie l'IMEI saisi comme paramètre de la requête
                data: { imei: imei },
                // Traite la réponse renvoyée par le serveur en cas de succès
                success: function (response) {
                    // CAS 1 : Si l'appareil est identifié avec succès dans notre base
                    if (response.found) {
                        // Affiche un message de confirmation vert avec l'origine de l'appareil
                        $('#imei-status').html('<span class="text-success fw-bold small"><i class="fas fa-check-circle me-1"></i> Appareil reconnu (' + response.source + ')</span>');
                        
                        // Remplit automatiquement la zone du Modèle d'appareil
                        $('#modele').val(response.device.modele);
                        // Remplit automatiquement la zone de la Référence produit
                        $('#reference').val(response.device.reference_produit);
                        
                        // Remplit automatiquement le nom du client s'il est déjà existant en base
                        if (response.device.client_nom) $('#client_nom').val(response.device.client_nom);
                        // Remplit automatiquement l'adresse e-mail du client
                        if (response.device.client_email) $('#client_email').val(response.device.client_email);
                        // Remplit automatiquement le numéro de téléphone du client
                        if (response.device.client_telephone) $('#client_telephone').val(response.device.client_telephone);

                        // Bloque la modification des champs Modèle et Référence pour éviter toute erreur
                        $('#modele, #reference').attr('readonly', true).addClass('bg-light');

                        // Si des informations de vente et de garantie sont associées à l'appareil
                        if (response.vente) {
                            // Définit le style vert si sous garantie, ou rouge si hors garantie
                            let gColor = response.vente.sous_garantie ? 'success' : 'danger';
                            // Définit le texte du badge en lettres capitales
                            let gText = response.vente.sous_garantie ? 'SOUS GARANTIE' : 'HORS GARANTIE';
                            
                            // Génère le code HTML complet de la carte récapitulative de garantie
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
                            // Injecte la carte générée dans l'interface et l'affiche de façon animée
                            $('#vente-info-display').html(html).fadeIn();
                        } else {
                            // Si l'appareil est connu dans notre historique SAV mais n'a pas de vente directe
                            let html = `
                                <div class="alert alert-danger bg-white border-danger border-2 mt-3 p-0 overflow-hidden shadow-sm" style="border-radius: 12px;">
                                    <div class="bg-danger bg-opacity-10 px-3 py-2 border-bottom border-danger border-opacity-25">
                                        <span class="fw-bold text-danger"><i class="fas fa-info-circle me-2"></i>NOTE SAV</span>
                                    </div>
                                    <div class="p-3">
                                        <p class="small mb-0 text-muted"><strong>Note :</strong> 
                                        Cet appareil est connu du SAV mais n'est pas répertorié dans notre base de ventes. 
                                        Prise en charge effectuée en mode 
                                        <strong class="text-danger">HORS GARANTIE</strong>.</p>
                                    </div>
                                </div>
                            `;
                            // Affiche la note informative SAV
                            $('#vente-info-display').html(html).fadeIn();
                        }
                    } else {
                        // CAS 2 : Si l'IMEI saisi est introuvable ou inconnu
                        // Affiche le message d'avertissement rouge
                        $('#imei-status').html('<span class="text-danger small fw-bold"><i class="fas fa-exclamation-triangle me-1"></i> ' + response.message + '</span>');

                        // Débloque la modification des champs Modèle et Référence pour une saisie manuelle libre
                        $('#modele, #reference').attr('readonly', false).removeClass('bg-light');

                        // Génère le message informant l'agent que la prise en charge est obligatoirement hors garantie
                        let html = `
                            <div class="alert alert-danger bg-white border-danger border-2 mt-3 p-0 overflow-hidden shadow-sm" style="border-radius: 12px;">
                                <div class="bg-danger bg-opacity-10 px-3 py-2 border-bottom border-danger border-opacity-25">
                                    <span class="fw-bold text-danger"><i class="fas fa-info-circle me-2"></i>NOTE SAV</span>
                                </div>
                                <div class="p-3">
                                    <p class="small mb-0 text-muted"><strong>Avis :</strong> 
                                    Cet IMEI n'est pas répertorié dans notre registre des ventes. Ce dossier sera automatiquement traité selon les conditions <strong class="text-danger">HORS GARANTIE</strong>.</p>
                                </div>
                            </div>
                        `;
                        // Affiche le message d'avertissement
                        $('#vente-info-display').html(html).fadeIn();
                    }
                }
            });
        }
    }

    // Déclare la variable pour gérer le délai d'anti-rebond lors de la saisie au clavier
    let imeiTimeout = null;
    
    // Écoute en temps réel chaque caractère saisi par l'agent dans le champ IMEI
    $('#imei').on('input', function () {
        // Annule le timer précédent pour éviter de lancer plusieurs recherches en même temps
        clearTimeout(imeiTimeout);
        // Récupère la saisie actuelle nettoyée de ses espaces
        let val = $(this).val().trim();
        
        // Si l'IMEI fait au moins 5 caractères, planifie la recherche dans 500ms d'inactivité
        if (val.length >= 5) {
            imeiTimeout = setTimeout(checkImei, 500);
        } else {
            // Si la saisie est effacée ou trop courte, vide et réinitialise tous les champs automatiquement
            $('#modele, #reference, #client_nom, #client_telephone, #client_email').val('');
            // Débloque les champs Modèle et Référence
            $('#modele, #reference').attr('readonly', false).removeClass('bg-light');
            // Efface l'indicateur visuel sous l'IMEI
            $('#imei-status').empty();
            // Masque la boîte d'information de garantie
            $('#vente-info-display').empty().hide();
        }
    });

    // Exécute immédiatement la recherche si le champ IMEI contient déjà une valeur au chargement
    if ($('#imei').val().length >= 5) {
        checkImei();
    }

    // Crée une copie de sauvegarde de toutes les options de techniciens présentes au chargement
    let allTechOptions = $('#technicien_id option').clone();











    // Déclare la fonction de filtrage dynamique de la liste des techniciens
    function filterTechnicians() {
        // Déclare un tableau vide pour lister les pannes sélectionnées
        let selectedPannes = [];
        
        // Récupère et stocke la valeur de chaque case de panne cochée
        $('input[name="type_pannes[]"]:checked').each(function () {
            selectedPannes.push($(this).val());
        });

        // Récupère l'identifiant du technicien actuellement sélectionné
        let selectedValue = $('#technicien_id').val();
        // Vide entièrement la liste déroulante des techniciens pour la reconstruire
        $('#technicien_id').empty();

        // Si aucune panne n'est cochée par l'agent
        if (selectedPannes.length === 0) {
            // Isole le premier élément vide (le placeholder)
            let placeholderOption = allTechOptions.filter('[value=""]');
            // En fait une copie conforme
            let placeholderClone = placeholderOption.clone();
            // Modifie son texte pour indiquer qu'il faut d'abord choisir une panne
            placeholderClone.text('-- Sélectionnez d\'abord une panne --');
            // Ajoute cette option à la liste déroulante
            $('#technicien_id').append(placeholderClone);
            // Force la sélection sur l'option vide
            $('#technicien_id').val("");
            // Arrête l'exécution de la fonction
            return;
        }

        // Si des pannes sont cochées, crée l'option par défaut neutre
        let placeholderOption = allTechOptions.filter('[value=""]');
        // En fait une copie conforme
        let placeholderClone = placeholderOption.clone();
        // Définit son texte d'invitation
        placeholderClone.text('-- Choisir un technicien --');
        // L'ajoute au début de la liste déroulante
        $('#technicien_id').append(placeholderClone);

        // Parcourt chaque technicien de notre liste de sauvegarde pour filtrer
        allTechOptions.each(function () {
            // Cible l'option du technicien en cours d'analyse
            let option = $(this);
            // Ignore l'option vide de départ
            if (option.val() === "") return;

            // Récupère les spécialités associées à ce technicien
            let techSpecialites = option.data('specialite') || '';
            // Décode le texte des spécialités (enlève les codes HTML)
            let decodedSpecialites = $('<div>').html(techSpecialites).text();
            // Initialise le drapeau de correspondance à faux
            let hasMatch = false;
            
            // Filtre la liste des pannes cochées en ignorant l'option générique "Autre"
            let filteredPannes = selectedPannes.filter(function (panne) {
                return panne.toLowerCase() !== 'autre' && panne.toLowerCase() !== 'autre...';
            });

            // Si l'agent n'a coché que la panne "Autre", tous les techniciens sont acceptés
            if (filteredPannes.length === 0) {
                hasMatch = true;
            } else {
                // Sinon, vérifie si une spécialité du technicien correspond à une panne cochée
                filteredPannes.forEach(function (panne) {
                    // Si le texte de spécialité contient le nom de la panne
                    if (decodedSpecialites.toLowerCase().includes(panne.toLowerCase())) {
                        // C'est un technicien qualifié !
                        hasMatch = true;
                    }
                });
            }

            // Si le technicien est qualifié pour réparer la panne
            if (hasMatch) {
                // Fait une copie conforme de son option
                let optClone = option.clone();
                // Colorie son nom en bleu pour le mettre en valeur visuellement
                optClone.css('color', '#2563eb');
                // L'ajoute dans la liste déroulante du formulaire
                $('#technicien_id').append(optClone);
            }
        });

        // Si le technicien sélectionné avant le filtre est toujours présent dans la nouvelle liste
        if (selectedValue && $('#technicien_id option[value="' + selectedValue + '"]').length > 0) {
            // Conserve automatiquement sa sélection active
            $('#technicien_id').val(selectedValue);
        } else {
            // Sinon, réinitialise la sélection à vide
            $('#technicien_id').val("");
        }
    }

    // Écoute les cases à cocher de la section des accessoires
    $('input[name="accessoires[]"]').on('change', function () {
        // Initialise l'indicateur de l'option Autre à faux
        let isAutreAccChecked = false;
        
        // Parcourt les accessoires cochés pour voir si l'option "Autre..." est présente
        $('input[name="accessoires[]"]:checked').each(function () {
            if ($(this).val() === 'Autre...') {
                isAutreAccChecked = true;
            }
        });

        // Si l'accessoire personnalisé "Autre..." est activé
        if (isAutreAccChecked) {
            // Affiche la zone de texte libre avec un effet fluide d'apparition
            $('#autre_accessoire_container').fadeIn();
            // Rend le champ obligatoire et place directement le curseur d'écriture dedans
            $('#accessoires_autre_input').prop('required', true).focus();
        } else {
            // Sinon, masque la zone de texte libre avec un effet fluide de disparition
            $('#autre_accessoire_container').fadeOut();
            // Enlève l'obligation de saisie et vide le contenu du champ
            $('#accessoires_autre_input').prop('required', false).val('');
        }
    });

    // Relance le filtrage des techniciens qualifiés dès qu'une panne est cochée ou décochée
    $('input[name="type_pannes[]"]').on('change', function () {
        filterTechnicians();
    });

    // Lance le filtrage initial au chargement de la page pour une cohérence immédiate
    filterTechnicians();
});
