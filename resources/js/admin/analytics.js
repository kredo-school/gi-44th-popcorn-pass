import Chart from 'chart.js/auto';

const chartCanvas = document.getElementById('dailyRevenueChart');

if (chartCanvas) {
    const chartLabels = JSON.parse(
        chartCanvas.dataset.labels || '[]'
    );

    const chartData = JSON.parse(
        chartCanvas.dataset.values || '[]'
    );

    new Chart(chartCanvas, {
        type: 'bar',

        data: {
            labels: chartLabels,

            datasets: [
                {
                    label: 'Revenue',
                    data: chartData,
                    backgroundColor: '#FFD700',
                    borderColor: '#FFD700',
                    borderWidth: 1,
                    borderRadius: 4,
                },
            ],
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: {
                    display: false,
                },
            },

            scales: {
                x: {
                    ticks: {
                        color: '#c9ccd6',
                    },
                    grid: {
                        color: '#2c3252',
                    },
                },

                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#c9ccd6',
                    },
                    grid: {
                        color: '#2c3252',
                    },
                },
            },
        },
    });
}