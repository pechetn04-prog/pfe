/*

 * Ce script gère l'initialisation et l'affichage des graphiques statistiques
 * de performance de l'application SAV :
 * 1. techChart (Bar Chart) : Volume de dossiers traités par technicien.
 * 2. retardChart (Doughnut Chart) : Distribution des retards de traitement 
 *    par tranche de temps (<24h, 24-48h, 48-72h, >72h).
 * 
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // 1. Initialisation du graphique d'activité des techniciens (Histogramme / Bar Chart)
    const techCtx = document.getElementById('techChart');
    if (techCtx && window.techChartData) {
        new Chart(techCtx, {
            type: 'bar', // Histogramme vertical
            data: {
                labels: window.techChartData.labels, // Noms des techniciens
                datasets: [{
                    label: 'Dossiers',
                    data: window.techChartData.values, // Quantités
                    backgroundColor: '#10b981',         // Vert premium
                    borderRadius: 8,                    // Sommets arrondis modernes
                }]
            },
            options: {
                maintainAspectRatio: false, // Permet le responsive complet
                plugins: { 
                    legend: { display: false } // Masque la légende pour une série unique
                },
                scales: { 
                    // Axes épurés sans lignes de grille de fond
                    y: { 
                        beginAtZero: true, 
                        grid: { display: false } 
                    }, 
                    x: { 
                        grid: { display: false } 
                    } 
                }
            }
        });
    }

    
    // 2. Initialisation du graphique de retard de prise en charge (Doughnut Chart)
    const retardCtx = document.getElementById('retardChart');
    if (retardCtx && window.retardChartData) {
        new Chart(retardCtx, {
            type: 'doughnut',
            data: {
                // Catégorisation temporelle des délais de traitement
                labels: ['<24h', '24-48h', '48-72h', '>72h'],
                datasets: [{
                    data: window.retardChartData,
                    // Code couleur logique : Vert (OK), Orange (Moyen), Orange Foncé (Haut), Rouge (Bloquant)
                    backgroundColor: ['#10b981', '#f59e0b', '#f97316', '#ef4444'],
                    borderWidth: 0,
                    cutout: '70%' // Anneau fin moderne
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        position: 'bottom', // Légende sous le graphique
                        labels: { 
                            usePointStyle: true, // Puces rondes esthétiques
                            boxWidth: 6,
                            font: { 
                                size: 10,
                                family: "'Inter', sans-serif"
                            } 
                        } 
                    }
                }
            }
        });
    }

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
                    label: function (context) {
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

    // 3. Initialisation du graphique de répartition des dossiers par Statut
    const statusChartEl = document.getElementById('statusChart');
    if (statusChartEl && window.dashboardStats && window.dashboardStats.status_distribution) {
        new Chart(statusChartEl, {
            type: 'doughnut',
            data: {
                labels: Object.keys(window.dashboardStats.status_distribution),
                datasets: [{
                    data: Object.values(window.dashboardStats.status_distribution),
                    // Palette de couleurs premium étendue pour tous les statuts
                    backgroundColor: [
                        '#3b82f6', '#10b981', '#f59e0b', '#ef4444',
                        '#8b5cf6', '#ec4899', '#06b6d4', '#14b8a6',
                        '#6366f1', '#f43f5e', '#10b981', '#6b7280',
                        '#84cc16', '#eab308', '#d946ef', '#fb923c',
                        '#22d3ee', '#c084fc'
                    ],
                    borderWidth: 0,
                    hoverOffset: 10 // Animation légère d'agrandissement au survol
                }]
            },
            options: doughnutOptions
        });
    }

    // 4. Initialisation du graphique de répartition sous/hors garantie
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
