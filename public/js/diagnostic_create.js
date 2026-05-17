/*
 * ====================================================================
 * FORMULAIRE INTERACTIF DE CRÉATION DE DIAGNOSTIC TECHNIQUE
 * ====================================================================
 * Ce script jQuery gère la saisie interactive d'une fiche d'expertise technique.
 * Il s'occupe de :
 * 1. Mettre à jour l'état visuel du stock en direct (couleur verte si dispo, rouge si rupture).
 * 2. Mettre à jour le tarif indicatif de la main d'œuvre sélectionnée.
 * 3. Gérer le clonage dynamique des lignes pour ajouter plusieurs pièces détachées ou prestations.
 * 4. Renseigner le nom du fichier image sélectionné dans le widget d'upload premium.
 * 5. Gérer l'apparition dynamique du formulaire d'exclusion de garantie (si applicable).
 */

$(document).ready(function () {
    // Index incrémental pour assurer l'unicité des champs à la soumission
    let pieceIndex = 1;

    // Attache les événements d'écoute aux éléments d'une ligne spécifique (pièce ou prestation)
    function bindRowEvents(row) {
        
        // Mise à jour de l'affichage du stock restant pour la pièce choisie
        $(row).find('.piece-select').on('change', function () {
            // Lecture du stock stocké dans l'attribut data-stock de l'option sélectionnée
            const stock = $(this).find(':selected').data('stock');
            const stockDisplay = $(row).find('.stock-display');
            
            if (stock !== undefined) {
                // Affiche en vert si disponible, sinon en rouge
                stockDisplay.text(stock)
                    .removeClass('text-muted')
                    .addClass(stock > 0 ? 'text-success' : 'text-danger');
            } else {
                stockDisplay.text('—')
                    .addClass('text-muted')
                    .removeClass('text-success text-danger');
            }
        });

        // Affichage du coût de la prestation de main d'œuvre sélectionnée
        $(row).find('.presta-select').on('change', function () {
            const price = parseFloat($(this).find(':selected').data('price') || 0);
            const priceDisplay = $(row).find('.price-display');
            if (price > 0) {
                // Formatage à 3 décimales (DT tunisien)
                priceDisplay.text(price.toLocaleString('fr-FR', { minimumFractionDigits: 3 }) + ' DT');
            } else {
                priceDisplay.text('—');
            }
        });

        // Gestion de la suppression de la ligne courante
        $(row).find('.remove-row').on('click', function () {
            const tbody = $(this).closest('tbody');
            // Empêche de supprimer la dernière ligne pour préserver la structure de saisie
            if (tbody.find('tr').length > 1) {
                $(row).remove();
            } else {
                // Si c'est l'unique ligne, on réinitialise simplement ses champs à blanc
                $(row).find('select').val('');
                $(row).find('input').val(1);
                $(row).find('.stock-display, .price-display')
                    .text('—')
                    .addClass('text-muted')
                    .removeClass('text-success text-danger');
            }
        });
    }

    // Écoute initiale des lignes présentes au chargement
    $('.piece-row, .presta-row').each(function () {
        bindRowEvents(this);
    });

    // Ajout dynamique d'une nouvelle ligne de pièce détachée par clonage
    $('#add-piece-row').on('click', function () {
        const tbody = $('#pieces-table tbody');
        const firstRow = tbody.find('tr:first');
        const newRow = firstRow.clone();

        // Réinitialisation des inputs et attributs name
        newRow.find('select').attr('name', `pieces[${pieceIndex}][id]`).val('');
        newRow.find('input[type="number"]').attr('name', `pieces[${pieceIndex}][quantite]`).val(1);
        newRow.find('.stock-display').text('—').addClass('text-muted').removeClass('text-success text-danger');

        // Ajout au tableau HTML et écoute des nouveaux éléments
        tbody.append(newRow);
        bindRowEvents(newRow);
        pieceIndex++;
    });

    // Ajout dynamique d'une nouvelle ligne de prestation de main d'œuvre par clonage
    $('#add-presta-row').on('click', function () {
        const tbody = $('#presta-table tbody');
        const firstRow = tbody.find('tr:first');
        const newRow = firstRow.clone();

        newRow.find('select').val('');
        newRow.find('.price-display').text('—');

        tbody.append(newRow);
        bindRowEvents(newRow);
    });

    // Renseignement du nom de fichier choisi dans le widget d'upload premium
    $('#photo_panne').on('change', function() {
        const fileName = $(this).val().split('\\').pop(); // Récupère le nom pur
        if (fileName) {
            $('#file-label').text(fileName).addClass('text-primary');
        } else {
            $('#file-label').text('Choisir une photo').removeClass('text-primary');
        }
    });

    // Affichage/Masquage fluide du bloc d'exclusion de garantie
    $('#exclure').on('change', function() {
        if ($(this).is(':checked')) {
            // Fait glisser le bloc vers le bas pour l'afficher
            $('#exclusion-details').slideDown();
            $('#exclusion-hint').hide();
        } else {
            // Fait glisser le bloc vers le haut pour le masquer
            $('#exclusion-details').slideUp();
            $('#exclusion-hint').show();
        }
    });
});
