/*
 * ====================================================================
 * LOGIQUE ET DESSIN DES GRAPHIQUES DU TABLEAU DE BORD (CHART.JS)
 * ====================================================================
 * Ce script gère l'initialisation et l'affichage des graphiques statistiques
 * interactifs de la page d'administration à l'aide de la bibliothèque Chart.js.
 * Il utilise des diagrammes circulaires (Doughnut) pour illustrer :
 * 1. La répartition des dossiers SAV par statut.
 * 2. La proportion d'appareils sous garantie versus hors garantie.
 * 
 * Les données brutes proviennent directement de Laravel via la variable 'window.dashboardStats'.
 */

$(document).ready(function() {
    
    // Configuration commune des options graphiques pour un aspect premium épuré
    const doughnutOptions = {
        responsive: true,           // Ajuste la taille au changement de résolution d'écran
        maintainAspectRatio: false, // Permet d'étirer le graphique dans son conteneur HTML
        plugins: {
            legend: { 
                position: 'bottom', // Légendes disposées sous le graphique
                labels: { 
                    boxWidth: 12,   // Largeur du carré de couleur indicateur
                    padding: 15,    // Espace de confort autour du libellé
                    font: { 
                        size: 11,
                        family: "'Inter', sans-serif" 
                    } 
                } 
            },
            tooltip: {
                callbacks: {
                    // Fonction interne pour formater l'infobulle au survol d'un secteur
                    // Affiche le libellé, la quantité brute et calcule le pourcentage correspondant
                    label: function(context) {
                        let label = context.label || '';
                        let value = context.raw || 0;
                        let total = context.dataset.data.reduce((a, b) => a + b, 0);
                        let percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                        return `${label}: ${value} (${percentage}%)`;
                    }
                }
            }
        },
        cutout: '70%' // Taille de l'évidement central pour dessiner un anneau moderne et fin
    };

    // 1. Initialisation du graphique de répartition des dossiers par Statut
    const statusChartEl = document.getElementById('statusChart');
    if (statusChartEl && window.dashboardStats && window.dashboardStats.status_distribution) {
        new Chart(statusChartEl, {
            type: 'doughnut',
            data: {
                labels: Object.keys(window.dashboardStats.status_distribution),
                datasets: [{
                    data: Object.values(window.dashboardStats.status_distribution),
                    // Palette de couleurs : Bleu, Jaune-Orange, Vert
                    backgroundColor: ['#3b82f6', '#f59e0b', '#10b981'],
                    borderWidth: 0,
                    hoverOffset: 10 // Animation légère d'agrandissement au survol
                }]
            },
            options: doughnutOptions
        });
    }

    // 2. Initialisation du graphique de répartition sous/hors garantie
    const warrantyChartEl = document.getElementById('warrantyChart');
    if (warrantyChartEl && window.dashboardStats && window.dashboardStats.warranty_distribution) {
        new Chart(warrantyChartEl, {
            type: 'doughnut',
            data: {
                labels: Object.keys(window.dashboardStats.warranty_distribution),
                datasets: [{
                    data: Object.values(window.dashboardStats.warranty_distribution),
                    // Couleurs : Vert (Sous garantie), Rouge (Hors garantie), Gris (Inconnu)
                    backgroundColor: ['#10b981', '#ef4444', '#6b7280'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: doughnutOptions
        });
    }
});
