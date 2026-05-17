document.addEventListener('DOMContentLoaded', function() {
    const finalTotalDisplay = document.getElementById('finalTotalDisplay');
    const finalTotalInput = document.getElementById('finalTotalInput');
    const grandTotalPiecesDisplay = document.getElementById('grand-total-pieces');
    const grandTotalLaborsDisplay = document.getElementById('grand-total-labors');
    
    let pieceIndex = window.initialPieceIndex || 0;
    let laborIndex = window.initialLaborIndex || 0;

    function calculate() {
        let totalPieces = 0;
        let totalLabors = 0;

        document.querySelectorAll('.piece-row').forEach(row => {
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const lineTotal = price * qty;
            const lineTotalDisplay = row.querySelector('.line-total');
            if (lineTotalDisplay) lineTotalDisplay.textContent = lineTotal.toFixed(3);
            totalPieces += lineTotal;
        });

        document.querySelectorAll('.labor-input').forEach(input => {
            totalLabors += parseFloat(input.value) || 0;
        });

        const total = totalPieces + totalLabors;

        if (grandTotalPiecesDisplay) grandTotalPiecesDisplay.textContent = totalPieces.toFixed(3) + ' DT';
        if (grandTotalLaborsDisplay) grandTotalLaborsDisplay.textContent = totalLabors.toFixed(3) + ' DT';
        if (finalTotalDisplay) finalTotalDisplay.textContent = total.toFixed(3);
        if (finalTotalInput) finalTotalInput.value = total.toFixed(3);
    }

    // Ajouter une pièce
    const addPieceBtn = document.getElementById('addPieceBtn');
    if (addPieceBtn) {
        addPieceBtn.addEventListener('click', function() {
            const template = document.getElementById('pieceRowTemplate').innerHTML;
            const html = template.replace(/INDEX/g, pieceIndex++);
            document.querySelector('#piecesTable tbody').insertAdjacentHTML('beforeend', html);
            calculate();
        });
    }

    // Ajouter une prestation
    const addLaborBtn = document.getElementById('addLaborBtn');
    if (addLaborBtn) {
        addLaborBtn.addEventListener('click', function() {
            const template = document.getElementById('laborRowTemplate').innerHTML;
            const html = template.replace(/INDEX/g, laborIndex++);
            document.querySelector('#laborsTable tbody').insertAdjacentHTML('beforeend', html);
            calculate();
        });
    }

    // Supprimer une ligne
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-row')) {
            e.target.closest('tr').remove();
            calculate();
        }
    });

    // Changement de sélection (auto-prix)
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('piece-select')) {
            const price = e.target.options[e.target.selectedIndex].dataset.price || 0;
            const row = e.target.closest('tr');
            if (row) row.querySelector('.price-input').value = price;
            calculate();
        }
        if (e.target.classList.contains('labor-select')) {
            const price = e.target.options[e.target.selectedIndex].dataset.price || 0;
            const row = e.target.closest('tr');
            if (row) row.querySelector('.labor-input').value = price;
            calculate();
        }
    });

    // Changement de prix/quantité
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('price-input') || e.target.classList.contains('qty-input') || e.target.classList.contains('labor-input')) {
            calculate();
        }
    });

    calculate();
});
