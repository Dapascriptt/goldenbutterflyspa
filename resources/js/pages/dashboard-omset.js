const formatCurrency = new Intl.NumberFormat('id-ID');

document.addEventListener('DOMContentLoaded', async () => {
    const lineCanvas = document.getElementById('dashboardLineChart');
    const donutCanvas = document.getElementById('dashboardDonutChart');

    if (!lineCanvas && !donutCanvas) return;

    const { default: Chart } = await import('chart.js/auto');

    if (lineCanvas) {
        const series = JSON.parse(lineCanvas.dataset.series || '[]');
        const labels = series.map((row) => row.date);
        const values = series.map((row) => row.total || 0);

        new Chart(lineCanvas, {
            type: 'line',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Omset',
                        data: values,
                        borderColor: '#9c7a4c',
                        backgroundColor: 'rgba(156, 122, 76, 0.18)',
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
                            label: (ctx) => `Rp ${formatCurrency.format(ctx.parsed.y || 0)}`,
                        },
                    },
                },
                scales: {
                    y: {
                        ticks: {
                            callback: (value) => `Rp ${formatCurrency.format(value)}`,
                        },
                    },
                },
            },
        });
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
                            borderWidth: 0,
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
                                label: (ctx) => `${ctx.label}: Rp ${formatCurrency.format(ctx.parsed || 0)}`,
                            },
                        },
                    },
                },
            });
        }
    }
});
