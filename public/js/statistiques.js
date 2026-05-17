/*
 * ====================================================================
 * LOGIQUE ET REPRÉSENTATION DES STATISTIQUES AVANCÉES (CHART.JS)
 * ====================================================================
 * Ce script gère l'initialisation et l'affichage des graphiques statistiques
 * de performance de l'application SAV :
 * 1. techChart (Bar Chart) : Volume de dossiers traités par technicien.
 * 2. retardChart (Doughnut Chart) : Distribution des retards de traitement 
 *    par tranche de temps (<24h, 24-48h, 48-72h, >72h).
 * 
 * Données brutes fournies dynamiquement par Laravel via techChartData et retardChartData.
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
});
