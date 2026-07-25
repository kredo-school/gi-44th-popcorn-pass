import Chart from 'chart.js/auto';

const chartElement = document.getElementById('revenueTrendChart');

if (chartElement) {

    const revenueData = JSON.parse(chartElement.dataset.revenue);

    new Chart(chartElement, {
        type: 'line',

        data: {
            labels: [
                'Jan','Feb','Mar','Apr','May','Jun',
                'Jul','Aug','Sep','Oct','Nov','Dec'
            ],

            datasets: [{
                label: 'Revenue',
                data: revenueData,
                borderColor: '#FFD400',
                backgroundColor: 'rgba(255,212,0,0.15)',
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: '#FFD400',
            }]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: {
                    display: false
                }
            },

            scales: {
                x: {
                    ticks: {
                        color: '#ddd'
                    },
                    grid: {
                        color: 'rgba(255,255,255,.05)'
                    }
                },

                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#ddd'
                    },
                    grid: {
                        color: 'rgba(255,255,255,.05)'
                    }
                }
            }
        }
    });
}