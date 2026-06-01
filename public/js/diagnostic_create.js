/*
 * ====================================================================
 * FORMULAIRE INTERACTIF DE CRÉATION DE DIAGNOSTIC TECHNIQUE
 * ====================================================================
 * Ce script jQuery/JavaScript gère l'interactivité côté client du formulaire
 * d'expertise technique. Il évite des rechargements de page inutiles
 * et offre une expérience utilisateur fluide 
 * 
 * Rôles principaux :
 * 1. Clonage de lignes dynamique (Ajout/Suppression de pièces et de main d'œuvre).
 * 2. Formatage et affichage dynamique des prix de main d'œuvre en direct.
 * 3. Gestion ergonomique de l'upload de photo (affichage du nom du fichier sélectionné).
 * 4. Affichage/Masquage fluide avec animations de la section d'exclusion de garantie.
 */

$(document).ready(function () {
    
    // Index incrémental pour assurer l'unicité des clés de formulaire
    // Utile pour la structure $_POST de Laravel : pieces[0][id], pieces[1][id], etc.
    let pieceIndex = 1;

    // Attache des écouteurs d'événements (Event Listeners) à une ligne (tr) du tableau.
    // Cette fonction est appelée au chargement initial et à chaque fois qu'une nouvelle ligne est clonée.
    function bindRowEvents(row) {
        
        // --- ÉVÉNEMENT 1 : Changement de prestation de main d'œuvre ---
        $(row).find('.presta-select').on('change', function () {
            // Récupère le prix stocké dans l'attribut HTML5 "data-price" de l'option sélectionnée
            const price = parseFloat($(this).find(':selected').data('price') || 0);
            const priceDisplay = $(row).find('.price-display');
            
            if (price > 0) {
                // Formate le prix au standard monétaire français/tunisien avec 3 décimales (ex: 45,000 DT)
                priceDisplay.text(price.toLocaleString('fr-FR', { minimumFractionDigits: 3 }) + ' DT');
            } else {
                // Si aucune prestation n'est sélectionnée, on remet le tiret par défaut
                priceDisplay.text('—');
            }
        });

        // --- ÉVÉNEMENT 2 : Clic sur le bouton de suppression d'une ligne ---
        $(row).find('.remove-row').on('click', function () {
            // Retrouve le corps de tableau (tbody) parent direct
            const tbody = $(this).closest('tbody');
            
            // Sécurité : On empêche la suppression s'il ne reste qu'une seule ligne active dans le tableau
            if (tbody.find('tr').length > 1) {
                // S'il y a plus d'une ligne, on supprime carrément l'élément <tr> du DOM
                $(row).remove();
            } else {
                // S'il s'agit de l'unique ligne restante, on réinitialise simplement ses valeurs à blanc
                $(row).find('select').val('');
                $(row).find('input').val(1);
                $(row).find('.price-display')
                    .text('—')
                    .addClass('text-muted');
            }
        });
    }

    // --- INITIALISATION : Liaison des événements sur les lignes existantes au chargement ---
    $('.piece-row, .presta-row').each(function () {
        bindRowEvents(this);
    });

    // --- ÉVÉNEMENT 3 : Ajout dynamique d'une nouvelle pièce détachée ---
    $('#add-piece-row').on('click', function () {
        const tbody = $('#pieces-table tbody');
        const firstRow = tbody.find('tr:first'); // Cible la première ligne comme modèle
        const newRow = firstRow.clone();         // Clone le modèle (HTML, structure)

        // Réinitialise la sélection et ajuste l'attribut "name" avec le nouvel index incrémenté
        // pieces[0][id] devient pieces[pieceIndex][id] pour éviter les collisions côté serveur
        newRow.find('select').attr('name', `pieces[${pieceIndex}][id]`).val('');
        newRow.find('input[type="number"]').attr('name', `pieces[${pieceIndex}][quantite]`).val(1);

        // Insère la nouvelle ligne à la suite des autres dans le tableau
        tbody.append(newRow);
        
        // Attache les événements d'écoute (suppression, etc.) sur cette nouvelle ligne
        bindRowEvents(newRow);
        
        // Incrémente l'index pour le prochain clic
        pieceIndex++;
    });

    // --- ÉVÉNEMENT 4 : Ajout dynamique d'une nouvelle prestation de main d'œuvre ---
    $('#add-presta-row').on('click', function () {
        const tbody = $('#presta-table tbody');
        const firstRow = tbody.find('tr:first'); // Cible la première ligne comme modèle
        const newRow = firstRow.clone();         // Clone le modèle

        // Réinitialise les valeurs par défaut de la nouvelle ligne
        newRow.find('select').val('');
        newRow.find('.price-display').text('—');

        // Insère la nouvelle ligne dans le tableau
        tbody.append(newRow);
        
        // Attache les événements d'écoute sur cette nouvelle ligne de prestation
        bindRowEvents(newRow);
    });

    // --- ÉVÉNEMENT 5 : Upload de photo (Ergonomie de sélection de fichier) ---
    $('#photo_panne').on('change', function() {
        // Isole le nom du fichier sélectionné (exclut le chemin complet fictif fourni par le navigateur)
        const fileName = $(this).val().split('\\').pop();
        
        if (fileName) {
            // Affiche le nom réel du fichier sur le libellé du widget
            $('#file-label').text(fileName).addClass('text-primary');
        } else {
            // Remet le libellé d'origine si la sélection est vide
            $('#file-label').text('Choisir une photo').removeClass('text-primary');
        }
    });

    // --- ÉVÉNEMENT 6 : Affichage dynamique de l'exclusion de garantie ---
    $('#exclure').on('change', function() {
        if ($(this).is(':checked')) {
            // Fait glisser vers le bas avec animation le bloc contenant les motifs d'exclusion
            $('#exclusion-details').slideDown();
            // Masque la note d'information par défaut
            $('#exclusion-hint').hide();
        } else {
            // Fait glisser vers le haut pour masquer le bloc d'exclusion
            $('#exclusion-details').slideUp();
            // Réaffiche la note d'information par défaut
            $('#exclusion-hint').show();
        }
    });
});
