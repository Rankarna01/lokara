<!-- Chart.js Scripts Initialization -->
@push('scripts')
<script>
    let growthChartInstance = null;
    let donutChartInstance = null;

    document.addEventListener('DOMContentLoaded', function () {
        initCharts();
    });

    document.addEventListener('livewire:navigated', function () {
        initCharts();
    });

    document.addEventListener('dateFilterChanged', function () {
        setTimeout(initCharts, 100);
    });

    function initCharts() {
        const growthLabels = @json($growthLabels);
        const growthData = @json($growthData);
        const donutData = @json($donutChartData);

        // 1. Line Chart: Pertumbuhan Tenant
        const growthCanvas = document.getElementById('growthChart');
        if (growthCanvas) {
            if (growthChartInstance) {
                growthChartInstance.destroy();
                growthChartInstance = null;
            }

            const ctxGrowth = growthCanvas.getContext('2d');
            const gradientOrange = ctxGrowth.createLinearGradient(0, 0, 0, 240);
            gradientOrange.addColorStop(0, 'rgba(249, 115, 22, 0.20)');
            gradientOrange.addColorStop(1, 'rgba(249, 115, 22, 0.00)');

            const maxVal = Math.max(...growthData, 5);
            const yMax = Math.ceil(maxVal * 1.3);

            growthChartInstance = new Chart(ctxGrowth, {
                type: 'line',
                data: {
                    labels: growthLabels,
                    datasets: [{
                        label: 'Tenant Terdaftar',
                        data: growthData,
                        borderColor: '#F97316',
                        backgroundColor: gradientOrange,
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#F97316',
                        pointBorderColor: '#FFFFFF',
                        pointBorderWidth: 2,
                        pointRadius: 4.5,
                        pointHoverRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#111827',
                            titleFont: { family: 'Inter', size: 12 },
                            bodyFont: { family: 'Inter', size: 12 },
                            padding: 10,
                            cornerRadius: 8,
                            displayColors: false,
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { family: 'Inter', size: 11 },
                                color: '#94A3B8'
                            }
                        },
                        y: {
                            min: 0,
                            suggestedMax: yMax,
                            ticks: {
                                precision: 0,
                                font: { family: 'Inter', size: 11 },
                                color: '#94A3B8',
                                callback: function(value) {
                                    return value === 0 ? '0' : (value >= 1000 ? (value / 1000).toFixed(1) + 'k' : value);
                                }
                            },
                            grid: {
                                color: '#F1F5F9',
                                drawBorder: false
                            }
                        }
                    }
                }
            });
        }

        // 2. Donut Chart: Status Langganan
        const donutCanvas = document.getElementById('subscriptionDonutChart');
        if (donutCanvas) {
            if (donutChartInstance) {
                donutChartInstance.destroy();
                donutChartInstance = null;
            }

            const ctxDonut = donutCanvas.getContext('2d');
            donutChartInstance = new Chart(ctxDonut, {
                type: 'doughnut',
                data: {
                    labels: ['Active', 'Trial', 'Suspended', 'Expired'],
                    datasets: [{
                        data: donutData,
                        backgroundColor: [
                            '#F97316', // Active Orange
                            '#BAE6FD', // Trial Soft Sky Blue
                            '#64748B', // Suspended Slate
                            '#1E293B'  // Expired Dark Slate
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '76%',
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#111827',
                            padding: 8,
                            cornerRadius: 6,
                            bodyFont: { family: 'Inter', size: 11 }
                        }
                    }
                }
            });
        }
    }
</script>
@endpush

