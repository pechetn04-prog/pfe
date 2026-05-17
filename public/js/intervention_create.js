/*
 * ====================================================================
 * LOGIQUE ET VALIDATIONS DU FORMULAIRE D'INTERVENTION TECHNIQUE
 * ====================================================================
 * Ce script gère l'interface de rédaction de rapport d'intervention.
 * Il comprend des contrôles de sécurité et des validations métier :
 * 1. Validation de Stock en temps réel (checkStockLevels) :
 *    - Si la quantité de pièce saisie dépasse le stock disponible en magasin :
 *      - Il marque la ligne de pièce en rouge.
 *      - Il désactive automatiquement les choix "Réparé" et "Non réparé".
 *      - Il coche et force l'état "Attente Pièce" pour empêcher de fausses déclarations.
 * 2. Remplacement et clonage dynamique des lignes pour pièces et main d'œuvre.
 * 3. Interception de la soumission du formulaire :
 *    - Si l'appareil est placé en "Attente Pièce", affiche un modal de confirmation
 *      Bootstrap pour valider l'information envoyée à l'administration.
 * 4. Gestion esthétique des noms de fichiers dans le widget d'upload premium.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Index pour assurer l'unicité des clés de tableaux lors de l'ajout dynamique
    let pieceIndex = 1;

    // Analyse les pièces saisies et compare les quantités demandées au stock disponible
    function checkStockLevels() {
        let outOfStock = false;

        // Parcourt chaque ligne de pièce détachée
        document.querySelectorAll('#pieces-table tbody tr').forEach(row => {
            const select = row.querySelector('.piece-select');
            const qtyInput = row.querySelector('input[type="number"]');
            
            if (select && select.value && qtyInput) {
                // Récupère la quantité en stock via l'attribut data-stock de l'option choisie
                const stock = parseInt(select.options[select.selectedIndex].dataset.stock || 0);
                const qty = parseInt(qtyInput.value || 0);
                
                if (qty > stock) {
                    outOfStock = true;
                    // Met en évidence en rouge la zone de stock
                    row.querySelector('.stock-display').classList.add('text-danger');
                } else {
                    row.querySelector('.stock-display').classList.remove('text-danger');
                }
            }
        });

        // Boutons radio de statut de fin
        const btnRepare = document.getElementById('res_repare');
        const btnIrrepare = document.getElementById('res_irreparable');
        const btnAttente = document.getElementById('res_attente');
        const labels = document.querySelectorAll('.status-selector');

        if (outOfStock) {
            // Cas A : Rupture de stock détectée
            if (btnRepare) btnRepare.disabled = true;
            if (btnIrrepare) btnIrrepare.disabled = true;
            if (btnAttente) btnAttente.checked = true; // Coche automatiquement "Attente Pièce"

            // Rend inactifs visuellement les boutons incompatibles
            labels.forEach(label => {
                const forId = label.getAttribute('for');
                if (forId === 'res_repare' || forId === 'res_irreparable') {
                    label.style.opacity = '0.5';
                    label.style.pointerEvents = 'none';
                }
            });
        } else {
            // Cas B : Toutes les pièces demandées sont disponibles en magasin
            if (btnRepare) btnRepare.disabled = false;
            if (btnIrrepare) btnIrrepare.disabled = false;
            
            // Rétablit l'interactivité complète sur tous les boutons de choix
            labels.forEach(label => {
                label.style.opacity = '1';
                label.style.pointerEvents = 'auto';
            });
        }
    }

    // Attache les événements d'écoute aux éléments d'une ligne spécifique
    function bindRowEvents(row) {
        
        // Changement de pièce détachée : met à jour le stock disponible
        const pieceSelect = row.querySelector('.piece-select');
        if (pieceSelect) {
            pieceSelect.addEventListener('change', function() {
                const stock = this.options[this.selectedIndex].dataset.stock || '—';
                row.querySelector('.stock-display').textContent = stock;
                checkStockLevels();
            });
        }

        // Modification de quantité demandée : réévalue le stock
        const qtyInput = row.querySelector('input[type="number"]');
        if (qtyInput) {
            qtyInput.addEventListener('input', checkStockLevels);
        }

        // Changement de main d'œuvre : met à jour le tarif
        const prestaSelect = row.querySelector('.presta-select');
        if (prestaSelect) {
            prestaSelect.addEventListener('change', function() {
                const price = parseFloat(this.options[this.selectedIndex].dataset.price || 0);
                row.querySelector('.price-display').textContent = price.toLocaleString('fr-FR', { minimumFractionDigits: 3 }) + ' DT';
            });
        }

        // Gestion du clic sur le bouton de suppression
        const removeBtn = row.querySelector('.remove-row');
        if (removeBtn) {
            removeBtn.addEventListener('click', function() {
                if (row.parentElement.rows.length > 1) {
                    row.remove();
                    checkStockLevels();
                } else {
                    // Si c'est l'unique ligne, réinitialisation à blanc
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

    // Initialise les lignes présentes au démarrage
    document.querySelectorAll('#pieces-table tbody tr, #presta-table tbody tr').forEach(bindRowEvents);

    // Ajout d'une ligne de pièce détachée par clonage profond
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

    // Ajout d'une ligne de prestation de main d'œuvre par clonage
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

    // Interception de la soumission de formulaire (Modal pour Attente Pièce)
    const form = document.getElementById('interventionForm');
    const modalEl = document.getElementById('modalAttentePiece');
    
    if (form && modalEl) {
        const modalAttente = new bootstrap.Modal(modalEl);
        const confirmSubmitBtn = document.getElementById('confirmSubmitAttente');

        form.addEventListener('submit', function(e) {
            const selectedStatusInput = document.querySelector('input[name="statut_final"]:checked');
            if (selectedStatusInput) {
                const selectedStatus = selectedStatusInput.value;
                // Affiche le modal de mise en garde de commande de pièces si "ATTENTE_PIECE" est choisi
                if (selectedStatus === 'ATTENTE_PIECE' && !form.dataset.confirmed) {
                    e.preventDefault(); // Suspend l'envoi
                    modalAttente.show();  // Ouvre le modal
                }
            }
        });

        // Confirmation au clic sur le bouton de confirmation interne du modal
        if (confirmSubmitBtn) {
            confirmSubmitBtn.addEventListener('click', function() {
                form.dataset.confirmed = "true"; // Marque comme validé
                form.submit();                  // Soumet réellement le formulaire
            });
        }
    }

    // Gestion du libellé du widget d'upload premium
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
