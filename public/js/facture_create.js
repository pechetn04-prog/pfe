document.addEventListener('DOMContentLoaded', function() {
    let laborIndex = window.initialLaborIndex || 1;

    function calculateGlobalTotal() {
        const totalPiecesEl = document.getElementById('grand-total-pieces');
        if (!totalPiecesEl) return;

        const totalPieces = parseFloat(totalPiecesEl.getAttribute('data-value')) || 0;
        let totalMO = 0;
        
        document.querySelectorAll('.labor-input').forEach(input => {
            totalMO += parseFloat(input.value) || 0;
        });

        const grandTotalLabors = document.getElementById('grand-total-labors');
        if (grandTotalLabors) grandTotalLabors.textContent = totalMO.toFixed(3) + ' DT';
        
        const totalBrut = totalPieces + totalMO;
        const remiseInput = document.getElementById('remise-input');
        const remise = remiseInput ? (parseFloat(remiseInput.value) || 0) : 0;
        const totalFinal = totalBrut * (1 - remise / 100);

        const totalFinalEl = document.getElementById('total-final');
        if (totalFinalEl) totalFinalEl.textContent = totalFinal.toFixed(3);
    }

    // Ajouter une prestation
    const addLaborBtn = document.getElementById('addLaborBtn');
    if (addLaborBtn) {
        addLaborBtn.addEventListener('click', function() {
            const template = document.getElementById('laborRowTemplate').innerHTML;
            const html = template.replace(/INDEX/g, laborIndex++);
            document.querySelector('#laborsTable tbody').insertAdjacentHTML('beforeend', html);
            calculateGlobalTotal();
        });
    }

    // Supprimer une ligne
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-row')) {
            e.target.closest('tr').remove();
            calculateGlobalTotal();
        }
    });

    // Changement de sélection (auto-prix)
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('labor-select')) {
            const price = e.target.options[e.target.selectedIndex].dataset.price || 0;
            const row = e.target.closest('tr');
            if (row) row.querySelector('.labor-input').value = price;
            calculateGlobalTotal();
        }
    });

    // Changement de montant ou remise
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('labor-input') || e.target.id === 'remise-input') {
            calculateGlobalTotal();
        }
    });

    // Calcul initial
    calculateGlobalTotal();
});
