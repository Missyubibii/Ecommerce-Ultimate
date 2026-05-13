@props(['revenueChart'])

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 h-full">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-bold text-gray-900">Biểu đồ doanh thu trong kỳ</h3>
        <div class="flex items-center gap-2 text-xs text-gray-500">
            <span class="w-3 h-3 rounded-full bg-indigo-500"></span>
            <span>Doanh thu (VNĐ)</span>
        </div>
    </div>
    <div class="relative h-72">
        <canvas id="revenueChartCanvas"></canvas>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChartCanvas').getContext('2d');
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(99, 102, 241, 0.2)');
        gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

        const revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: @js($revenueChart['labels']),
                datasets: [{
                    label: 'Doanh thu',
                    data: @js($revenueChart['values']),
                    borderColor: '#6366f1',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 0,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#6366f1',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: '#1f2937',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { 
                            font: { size: 11 }, 
                            color: '#9ca3af',
                            maxTicksLimit: 12
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f3f4f6' },
                        ticks: {
                            font: { size: 11 },
                            color: '#9ca3af',
                            callback: function(value) {
                                if (value >= 1000000) return (value / 1000000) + 'M';
                                if (value >= 1000) return (value / 1000) + 'K';
                                return value;
                            }
                        }
                    }
                }
            }
        });

        // Listen for real-time updates
        window.addEventListener('dashboard-updated', function(e) {
            const newData = e.detail.revenueChart;
            revenueChart.data.labels = newData.labels;
            revenueChart.data.datasets[0].data = newData.values;
            revenueChart.update();
        });
    });
</script>
