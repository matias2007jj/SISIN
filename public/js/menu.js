document.addEventListener('DOMContentLoaded', function () {
    const canvas = document.getElementById('graficoEstadoBienes');
    if (!canvas || typeof Chart === 'undefined') return;

    const activos = Number(canvas.dataset.activos || 0);
    const inactivos = Number(canvas.dataset.inactivos || 0);
    const total = activos + inactivos;

    const datos = total > 0 ? [activos, inactivos] : [1];
    const etiquetas = total > 0 ? ['Bienes Activos', 'Bienes Inactivos'] : ['Sin bienes registrados'];
    const colores = total > 0 ? ['#10b981', '#ef4444'] : ['rgba(156, 163, 175, 0.2)'];

    new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: etiquetas,
            datasets: [{
                data: datos,
                backgroundColor: colores,
                borderColor: '#11181c',
                borderWidth: 3,
                hoverOffset: total > 0 ? 6 : 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '68%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        color: '#cbd5e1',
                        padding: 18,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: {
                            family: 'Manrope, sans-serif',
                            size: 13,
                            weight: '600'
                        }
                    }
                },
                tooltip: {
                    enabled: total > 0,
                    backgroundColor: '#0b1114',
                    titleColor: '#f3f4f6',
                    bodyColor: '#e5e7eb',
                    borderColor: 'rgba(255, 255, 255, 0.12)',
                    borderWidth: 1,
                    padding: 10,
                    callbacks: {
                        label: function (ctx) {
                            const valor = ctx.parsed || 0;
                            const porcentaje = total > 0 ? ((valor / total) * 100).toFixed(1) : 0;
                            return ` ${ctx.label}: ${valor} (${porcentaje}%)`;
                        }
                    }
                }
            }
        }
    });
});