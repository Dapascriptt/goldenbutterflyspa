const numberFormat = new Intl.NumberFormat('id-ID');

async function initOmsetCharts() {
    const lineCanvas = document.getElementById('omsetLineChart');
    const donutCanvas = document.getElementById('omsetDonutChart');

    if (!lineCanvas && !donutCanvas) return;

    const { default: Chart } = await import('chart.js/auto');

    if (lineCanvas) {
        const labels = JSON.parse(lineCanvas.dataset.labels || '[]');
        const values = JSON.parse(lineCanvas.dataset.values || '[]');

        if (labels.length) {
            new Chart(lineCanvas, {
                type: 'line',
                data: {
                    labels,
                    datasets: [
                        {
                            label: 'Omset',
                            data: values,
                            borderColor: '#9c7a4c',
                            backgroundColor: 'rgba(156, 122, 76, 0.15)',
                            tension: 0.35,
                            fill: true,
                            pointRadius: 3,
                            pointBackgroundColor: '#9c7a4c',
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => `Rp ${numberFormat.format(ctx.parsed.y || 0)}`,
                            },
                        },
                    },
                    scales: {
                        y: {
                            ticks: {
                                callback: (value) => `Rp ${numberFormat.format(value)}`,
                            },
                        },
                    },
                },
            });
        }
    }

    if (donutCanvas) {
        const labels = JSON.parse(donutCanvas.dataset.labels || '[]');
        const values = JSON.parse(donutCanvas.dataset.values || '[]');

        if (labels.length) {
            const palette = [
                '#9c7a4c', '#c7b49a', '#7b5f3d', '#4b2f1a', '#eadfce',
                '#b08c56', '#8a6a44', '#d7c2a3', '#6b4b2e', '#f1e7d8'
            ];
            new Chart(donutCanvas, {
                type: 'doughnut',
                data: {
                    labels,
                    datasets: [
                        {
                            data: values,
                            backgroundColor: labels.map((_, i) => palette[i % palette.length]),
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 10, boxHeight: 10 },
                        },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => `${ctx.label}: Rp ${numberFormat.format(ctx.parsed || 0)}`,
                            },
                        },
                    },
                },
            });
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initOmsetCharts();
});
