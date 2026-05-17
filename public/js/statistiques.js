document.addEventListener('DOMContentLoaded', function() {
    // Chart Tech
    const techCtx = document.getElementById('techChart');
    if(techCtx && window.techChartData) {
        new Chart(techCtx, {
            type: 'bar',
            data: {
                labels: window.techChartData.labels,
                datasets: [{
                    label: 'Dossiers',
                    data: window.techChartData.values,
                    backgroundColor: '#10b981',
                    borderRadius: 8,
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { 
                    y: { beginAtZero: true, grid: { display: false } }, 
                    x: { grid: { display: false } } 
                }
            }
        });
    }

    // Chart Retards
    const retardCtx = document.getElementById('retardChart');
    if(retardCtx && window.retardChartData) {
        new Chart(retardCtx, {
            type: 'doughnut',
            data: {
                labels: ['<24h', '24-48h', '48-72h', '>72h'],
                datasets: [{
                    data: window.retardChartData,
                    backgroundColor: ['#10b981', '#f59e0b', '#f97316', '#ef4444'],
                    borderWidth: 0,
                    cutout: '70%'
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 6, font: { size: 10 } } }
                }
            }
        });
    }
});
