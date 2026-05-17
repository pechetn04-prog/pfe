$(document).ready(function() {
    // Configuration commune pour les graphiques circulaires
    const doughnutOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom', labels: { boxWidth: 12, padding: 15, font: { size: 11 } } },
            tooltip: {
                callbacks: {
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
        cutout: '70%'
    };

    // 2. Graphique des Statuts (Doughnut)
    const statusChartEl = document.getElementById('statusChart');
    if (statusChartEl && window.dashboardStats && window.dashboardStats.status_distribution) {
        new Chart(statusChartEl, {
            type: 'doughnut',
            data: {
                labels: Object.keys(window.dashboardStats.status_distribution),
                datasets: [{
                    data: Object.values(window.dashboardStats.status_distribution),
                    backgroundColor: ['#3b82f6', '#f59e0b', '#10b981'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: doughnutOptions
        });
    }

    // 3. Graphique des Garanties (Doughnut)
    const warrantyChartEl = document.getElementById('warrantyChart');
    if (warrantyChartEl && window.dashboardStats && window.dashboardStats.warranty_distribution) {
        new Chart(warrantyChartEl, {
            type: 'doughnut',
            data: {
                labels: Object.keys(window.dashboardStats.warranty_distribution),
                datasets: [{
                    data: Object.values(window.dashboardStats.warranty_distribution),
                    backgroundColor: ['#10b981', '#ef4444', '#6b7280'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: doughnutOptions
        });
    }
});
