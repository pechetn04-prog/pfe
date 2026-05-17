/*
 * ====================================================================
 * LOGIQUE INTERACTIVE DU FORMULAIRE DE CRÉATION DE FACTURE
 * ====================================================================
 * Ce script gère l'interface de facturation de l'application SAV.
 * Fonctionnalités interactives :
 * 1. Ajouter et supprimer dynamiquement des lignes de prestations de main d'œuvre.
 * 2. Mettre à jour automatiquement le tarif indicatif de la prestation sélectionnée.
 * 3. Calculer en temps réel le total brut (somme des pièces validées et de la main d'œuvre).
 * 4. Appliquer dynamiquement un pourcentage de remise (%) saisi par l'agent.
 * 5. Mettre à jour instantanément le total général final TTC.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Index incrémental unique pour éviter les conflits d'inputs dans les tableaux
    let laborIndex = window.initialLaborIndex || 1;

    // Calcule le total final de la facture en appliquant les tarifs de main d'œuvre et la remise
    function calculateGlobalTotal() {
        const totalPiecesEl = document.getElementById('grand-total-pieces');
        if (!totalPiecesEl) return;

        // Récupère la valeur brute du montant des pièces détachées déjà facturées
        const totalPieces = parseFloat(totalPiecesEl.getAttribute('data-value')) || 0;
        let totalMO = 0;
        
        // Somme des montants de main d'œuvre ajoutés
        document.querySelectorAll('.labor-input').forEach(input => {
            totalMO += parseFloat(input.value) || 0;
        });

        // Met à jour l'affichage du sous-total de la main d'œuvre
        const grandTotalLabors = document.getElementById('grand-total-labors');
        if (grandTotalLabors) grandTotalLabors.textContent = totalMO.toFixed(3) + ' DT';
        
        // Application de la remise en pourcentage
        const totalBrut = totalPieces + totalMO;
        const remiseInput = document.getElementById('remise-input');
        const remise = remiseInput ? (parseFloat(remiseInput.value) || 0) : 0;
        
        // Formule de calcul avec réduction en pourcentage
        const totalFinal = totalBrut * (1 - remise / 100);

        // Affichage final du montant général TTC
        const totalFinalEl = document.getElementById('total-final');
        if (totalFinalEl) totalFinalEl.textContent = totalFinal.toFixed(3);
    }

    // Gestion de l'ajout d'une nouvelle prestation de main d'œuvre
    const addLaborBtn = document.getElementById('addLaborBtn');
    if (addLaborBtn) {
        addLaborBtn.addEventListener('click', function() {
            // Lecture du template HTML et remplacement du jeton INDEX pour le rendre unique
            const template = document.getElementById('laborRowTemplate').innerHTML;
            const html = template.replace(/INDEX/g, laborIndex++);
            document.querySelector('#laborsTable tbody').insertAdjacentHTML('beforeend', html);
            calculateGlobalTotal();
        });
    }

    // Suppression d'une ligne de prestation au clic (délégation d'événements)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-row')) {
            e.target.closest('tr').remove();
            calculateGlobalTotal();
        }
    });

    // Renseignement automatique du prix unitaire lors de la sélection d'une prestation
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('labor-select')) {
            const price = e.target.options[e.target.selectedIndex].dataset.price || 0;
            const row = e.target.closest('tr');
            if (row) row.querySelector('.labor-input').value = price;
            calculateGlobalTotal();
        }
    });

    // Recalcule le montant total si le tarif de la prestation ou la remise change
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('labor-input') || e.target.id === 'remise-input') {
            calculateGlobalTotal();
        }
    });

    // Lance le calcul initial au chargement pour renseigner les montants par défaut
    calculateGlobalTotal();
});
