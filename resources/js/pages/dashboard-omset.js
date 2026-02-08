document.addEventListener('DOMContentLoaded', async () => {
    const canvas = document.getElementById('omsetChart');
    if (!canvas) return;

    const series = JSON.parse(canvas.dataset.series || '[]');
    const labels = series.map((row) => row.date);
    const values = series.map((row) => row.total);
    const formatter = new Intl.NumberFormat('id-ID');

    const { default: Chart } = await import('chart.js/auto');

    new Chart(canvas, {
        type: 'line',
        data: {
            labels,
            datasets: [
                {
                    label: 'Omset',
                    data: values,
                    borderColor: '#9c7a4c',
                    backgroundColor: 'rgba(156, 122, 76, 0.2)',
                    tension: 0.35,
                    fill: true,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    ticks: {
                        callback: (value) => `Rp ${formatter.format(value)}`,
                    },
                },
            },
        },
    });
});
