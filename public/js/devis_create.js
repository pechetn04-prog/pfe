/*
 * ====================================================================
 * LOGIQUE DYNAMIQUE DU FORMULAIRE DE CRÉATION DE DEVIS
 * ====================================================================
 * Ce script gère l'interface interactive de création de devis.
 * Il s'occupe de :
 * 1. Ajouter dynamiquement des lignes de pièces détachées ou de main d'œuvre.
 * 2. Mettre à jour automatiquement les prix unitaires selon la sélection.
 * 3. Calculer en temps réel le total partiel (pièces & main d'œuvre) et le total général TTC.
 * 4. Gérer les suppressions de lignes et la synchronisation avec le formulaire HTML.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Ciblage des éléments du DOM
    const finalTotalDisplay = document.getElementById('finalTotalDisplay');       // Affichage texte du total
    const finalTotalInput = document.getElementById('finalTotalInput');           // Input caché envoyé au serveur
    const grandTotalPiecesDisplay = document.getElementById('grand-total-pieces'); // Sous-total des pièces détachées
    const grandTotalLaborsDisplay = document.getElementById('grand-total-labors'); // Sous-total de la main d'œuvre
    
    // Indices de lignes uniques (évite les conflits d'inputs dans les tableaux en PHP)
    let pieceIndex = window.initialPieceIndex || 0;
    let laborIndex = window.initialLaborIndex || 0;

    // Fonction interne pour calculer tous les totaux du devis en temps réel
    function calculate() {
        let totalPieces = 0;
        let totalLabors = 0;

        // Calcul du coût total des pièces détachées ajoutées
        document.querySelectorAll('.piece-row').forEach(row => {
            const price = parseFloat(row.querySelector('.price-input').value) || 0;
            const qty = parseFloat(row.querySelector('.qty-input').value) || 0;
            const lineTotal = price * qty;
            
            const lineTotalDisplay = row.querySelector('.line-total');
            if (lineTotalDisplay) {
                lineTotalDisplay.textContent = lineTotal.toFixed(3);
            }
            totalPieces += lineTotal;
        });

        // Calcul du coût total des prestations de main d'œuvre ajoutées
        document.querySelectorAll('.labor-input').forEach(input => {
            totalLabors += parseFloat(input.value) || 0;
        });

        // Calcul du total général
        const total = totalPieces + totalLabors;

        // Mise à jour des affichages (Format monétaire tunisien avec 3 décimales)
        if (grandTotalPiecesDisplay) grandTotalPiecesDisplay.textContent = totalPieces.toFixed(3) + ' DT';
        if (grandTotalLaborsDisplay) grandTotalLaborsDisplay.textContent = totalLabors.toFixed(3) + ' DT';
        if (finalTotalDisplay) finalTotalDisplay.textContent = total.toFixed(3);
        
        // Renseignement de la valeur dans le champ masqué pour la soumission en POST
        if (finalTotalInput) finalTotalInput.value = total.toFixed(3);
    }

    // Gestion de l'ajout d'une ligne de pièce détachée
    const addPieceBtn = document.getElementById('addPieceBtn');
    if (addPieceBtn) {
        addPieceBtn.addEventListener('click', function() {
            // Lecture du template HTML et remplacement du jeton INDEX pour le rendre unique
            const template = document.getElementById('pieceRowTemplate').innerHTML;
            const html = template.replace(/INDEX/g, pieceIndex++);
            document.querySelector('#piecesTable tbody').insertAdjacentHTML('beforeend', html);
            calculate();
        });
    }

    // Gestion de l'ajout d'une prestation de main d'œuvre
    const addLaborBtn = document.getElementById('addLaborBtn');
    if (addLaborBtn) {
        addLaborBtn.addEventListener('click', function() {
            // Lecture du template HTML et remplacement du jeton INDEX
            const template = document.getElementById('laborRowTemplate').innerHTML;
            const html = template.replace(/INDEX/g, laborIndex++);
            document.querySelector('#laborsTable tbody').insertAdjacentHTML('beforeend', html);
            calculate();
        });
    }

    // Gestionnaire global pour la suppression d'une ligne (délégation d'événements)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-row')) {
            e.target.closest('tr').remove();
            calculate();
        }
    });

    // Écouteur pour la mise à jour automatique des prix unitaires lors de la sélection
    document.addEventListener('change', function(e) {
        // Sélection d'une pièce détachée : affecte son tarif par défaut
        if (e.target.classList.contains('piece-select')) {
            const price = e.target.options[e.target.selectedIndex].dataset.price || 0;
            const row = e.target.closest('tr');
            if (row) row.querySelector('.price-input').value = price;
            calculate();
        }
        // Sélection d'une prestation de main d'œuvre : affecte son tarif par défaut
        if (e.target.classList.contains('labor-select')) {
            const price = e.target.options[e.target.selectedIndex].dataset.price || 0;
            const row = e.target.closest('tr');
            if (row) row.querySelector('.labor-input').value = price;
            calculate();
        }
    });

    // Recalcule le montant total instantanément si l'utilisateur modifie un input à la main
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('price-input') || e.target.classList.contains('qty-input') || e.target.classList.contains('labor-input')) {
            calculate();
        }
    });

    // Lancement du calcul initial au chargement pour afficher les montants par défaut
    calculate();
});
