document.addEventListener('DOMContentLoaded', async () => {
    const canvas = document.getElementById('monthlyTherapyChart');
    if (!canvas) return;

    const labels = JSON.parse(canvas.dataset.labels || '[]');
    const extra = JSON.parse(canvas.dataset.extra || '[]');
    const total = JSON.parse(canvas.dataset.total || '[]');

    const { default: Chart } = await import('chart.js/auto');

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels,
            datasets: [
                {
                    label: 'Extra Time (30 menit)',
                    data: extra,
                    backgroundColor: '#d8cfb4',
                    borderRadius: 6,
                    barPercentage: 0.7,
                    categoryPercentage: 0.7,
                },
                {
                    label: 'Total Treatment',
                    data: total,
                    backgroundColor: '#3f3728',
                    borderRadius: 6,
                    barPercentage: 0.7,
                    categoryPercentage: 0.7,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        boxWidth: 10,
                        boxHeight: 10,
                        usePointStyle: true,
                        pointStyle: 'rectRounded',
                    },
                },
            },
            scales: {
                x: {
                    ticks: {
                        maxRotation: 60,
                        minRotation: 40,
                    },
                },
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0,
                    },
                },
            },
        },
    });
});
