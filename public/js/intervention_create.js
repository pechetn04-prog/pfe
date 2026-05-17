document.addEventListener('DOMContentLoaded', function() {
    let pieceIndex = 1;

    function checkStockLevels() {
        let outOfStock = false;
        document.querySelectorAll('#pieces-table tbody tr').forEach(row => {
            const select = row.querySelector('.piece-select');
            const qtyInput = row.querySelector('input[type="number"]');
            if (select && select.value && qtyInput) {
                const stock = parseInt(select.options[select.selectedIndex].dataset.stock || 0);
                const qty = parseInt(qtyInput.value || 0);
                if (qty > stock) {
                    outOfStock = true;
                    row.querySelector('.stock-display').classList.add('text-danger');
                } else {
                    row.querySelector('.stock-display').classList.remove('text-danger');
                }
            }
        });

        const btnRepare = document.getElementById('res_repare');
        const btnIrrepare = document.getElementById('res_irreparable');
        const btnAttente = document.getElementById('res_attente');
        const labels = document.querySelectorAll('.status-selector');

        if (outOfStock) {
            if (btnRepare) btnRepare.disabled = true;
            if (btnIrrepare) btnIrrepare.disabled = true;
            if (btnAttente) btnAttente.checked = true;
            labels.forEach(label => {
                const forId = label.getAttribute('for');
                if (forId === 'res_repare' || forId === 'res_irreparable') {
                    label.style.opacity = '0.5';
                    label.style.pointerEvents = 'none';
                }
            });
        } else {
            if (btnRepare) btnRepare.disabled = false;
            if (btnIrrepare) btnIrrepare.disabled = false;
            labels.forEach(label => {
                label.style.opacity = '1';
                label.style.pointerEvents = 'auto';
            });
        }
    }

    function bindRowEvents(row) {
        // Mise à jour stock
        const pieceSelect = row.querySelector('.piece-select');
        if (pieceSelect) {
            pieceSelect.addEventListener('change', function() {
                const stock = this.options[this.selectedIndex].dataset.stock || '—';
                row.querySelector('.stock-display').textContent = stock;
                checkStockLevels();
            });
        }

        const qtyInput = row.querySelector('input[type="number"]');
        if (qtyInput) {
            qtyInput.addEventListener('input', checkStockLevels);
        }

        // Mise à jour prix prestation
        const prestaSelect = row.querySelector('.presta-select');
        if (prestaSelect) {
            prestaSelect.addEventListener('change', function() {
                const price = parseFloat(this.options[this.selectedIndex].dataset.price || 0);
                row.querySelector('.price-display').textContent = price.toLocaleString('fr-FR', { minimumFractionDigits: 3 }) + ' DT';
            });
        }

        // Suppression
        const removeBtn = row.querySelector('.remove-row');
        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                if (row.parentElement.rows.length > 1) {
                    row.remove();
                    checkStockLevels();
                } else {
                    const select = row.querySelector('select');
                    if (select) select.value = '';
                    const stock = row.querySelector('.stock-display');
                    if (stock) stock.textContent = '—';
                    const price = row.querySelector('.price-display');
                    if (price) price.textContent = '0.000 DT';
                    checkStockLevels();
                }
            });
        }
    }

    // Bind initial rows
    document.querySelectorAll('#pieces-table tbody tr, #presta-table tbody tr').forEach(bindRowEvents);

    // Ajouter pièce
    const addPieceBtn = document.getElementById('add-piece-row');
    if (addPieceBtn) {
        addPieceBtn.addEventListener('click', function() {
            const tbody = document.querySelector('#pieces-table tbody');
            const firstRow = tbody.querySelector('tr');
            const newRow = firstRow.cloneNode(true);
            
            const select = newRow.querySelector('select');
            select.name = `pieces[${pieceIndex}][id]`;
            select.value = '';
            
            const qty = newRow.querySelector('input[type="number"]');
            qty.name = `pieces[${pieceIndex}][quantite]`;
            qty.value = 1;
            
            newRow.querySelector('.stock-display').textContent = '—';
            newRow.querySelector('.stock-display').classList.remove('text-danger');
            
            tbody.appendChild(newRow);
            bindRowEvents(newRow);
            pieceIndex++;
        });
    }

    // Ajouter prestation
    const addPrestaBtn = document.getElementById('add-presta-row');
    if (addPrestaBtn) {
        addPrestaBtn.addEventListener('click', function() {
            const tbody = document.querySelector('#presta-table tbody');
            const firstRow = tbody.querySelector('tr');
            const newRow = firstRow.cloneNode(true);
            
            const select = newRow.querySelector('select');
            select.value = '';
            newRow.querySelector('.price-display').textContent = '0.000 DT';
            
            tbody.appendChild(newRow);
            bindRowEvents(newRow);
        });
    }

    // Interception de la soumission pour le modal Attente Pièce
    const form = document.getElementById('interventionForm');
    const modalEl = document.getElementById('modalAttentePiece');
    if (form && modalEl) {
        const modalAttente = new bootstrap.Modal(modalEl);
        const confirmSubmitBtn = document.getElementById('confirmSubmitAttente');

        form.addEventListener('submit', function(e) {
            const selectedStatusInput = document.querySelector('input[name="statut_final"]:checked');
            if (selectedStatusInput) {
                const selectedStatus = selectedStatusInput.value;
                // Si c'est en attente de pièce et que le modal n'a pas encore été validé
                if (selectedStatus === 'ATTENTE_PIECE' && !form.dataset.confirmed) {
                    e.preventDefault();
                    modalAttente.show();
                }
            }
        });

        if (confirmSubmitBtn) {
            confirmSubmitBtn.addEventListener('click', function() {
                form.dataset.confirmed = "true";
                form.submit();
            });
        }
    }

    // Affichage du nom du fichier sélectionné
    const photoInput = document.querySelector('input[name="photo_intervention"]');
    if (photoInput) {
        photoInput.addEventListener('change', function() {
            const fileName = this.value.split('\\').pop();
            const label = this.parentElement.querySelector('.small.fw-bold');
            if (fileName) {
                label.textContent = fileName;
                label.classList.add('text-primary');
            } else {
                label.textContent = 'Choisir un fichier';
                label.classList.remove('text-primary');
            }
        });
    }
});
