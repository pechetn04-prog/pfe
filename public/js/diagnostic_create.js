$(document).ready(function () {
    let pieceIndex = 1;

    function bindRowEvents(row) {
        // Mise à jour de l'affichage du stock
        $(row).find('.piece-select').on('change', function () {
            const stock = $(this).find(':selected').data('stock');
            const stockDisplay = $(row).find('.stock-display');
            if (stock !== undefined) {
                stockDisplay.text(stock).removeClass('text-muted').addClass(stock > 0 ? 'text-success' : 'text-danger');
            } else {
                stockDisplay.text('—').addClass('text-muted').removeClass('text-success text-danger');
            }
        });

        // Mise à jour de l'affichage du montant de la prestation
        $(row).find('.presta-select').on('change', function () {
            const price = parseFloat($(this).find(':selected').data('price') || 0);
            const priceDisplay = $(row).find('.price-display');
            if (price > 0) {
                priceDisplay.text(price.toLocaleString('fr-FR', { minimumFractionDigits: 3 }) + ' DT');
            } else {
                priceDisplay.text('—');
            }
        });

        // Suppression d'une ligne
        $(row).find('.remove-row').on('click', function () {
            const tbody = $(this).closest('tbody');
            if (tbody.find('tr').length > 1) {
                $(row).remove();
            } else {
                $(row).find('select').val('');
                $(row).find('input').val(1);
                $(row).find('.stock-display, .price-display').text('—').addClass('text-muted').removeClass('text-success text-danger');
            }
        });
    }

    // Initialisation des lignes existantes
    $('.piece-row, .presta-row').each(function () {
        bindRowEvents(this);
    });

    // Ajouter une nouvelle ligne de pièce
    $('#add-piece-row').on('click', function () {
        const tbody = $('#pieces-table tbody');
        const firstRow = tbody.find('tr:first');
        const newRow = firstRow.clone();

        newRow.find('select').attr('name', `pieces[${pieceIndex}][id]`).val('');
        newRow.find('input[type="number"]').attr('name', `pieces[${pieceIndex}][quantite]`).val(1);
        newRow.find('.stock-display').text('—').addClass('text-muted').removeClass('text-success text-danger');

        tbody.append(newRow);
        bindRowEvents(newRow);
        pieceIndex++;
    });

    // Ajouter une nouvelle ligne de prestation
    $('#add-presta-row').on('click', function () {
        const tbody = $('#presta-table tbody');
        const firstRow = tbody.find('tr:first');
        const newRow = firstRow.clone();

        newRow.find('select').val('');
        newRow.find('.price-display').text('—');

        tbody.append(newRow);
        bindRowEvents(newRow);
    });

    // Affichage du nom du fichier sélectionné
    $('#photo_panne').on('change', function() {
        const fileName = $(this).val().split('\\').pop();
        if (fileName) {
            $('#file-label').text(fileName).addClass('text-primary');
        } else {
            $('#file-label').text('Choisir une photo').removeClass('text-primary');
        }
    });

    // Toggle Exclusion de garantie details
    $('#exclure').on('change', function() {
        if ($(this).is(':checked')) {
            $('#exclusion-details').slideDown();
            $('#exclusion-hint').hide();
        } else {
            $('#exclusion-details').slideUp();
            $('#exclusion-hint').show();
        }
    });
});
