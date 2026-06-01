@extends('layouts.app')

@section('body-class', 'dashboard-page')

@section('content')
    <div class="dashboard-shell" data-dashboard-endpoint="{{ route('admin.dashboard.data') }}">
        <script id="dashboardInitialData" type="application/json">{!! json_encode($dashboard, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>
        <div class="dashboard-hero px-4 px-xl-5 pt-4 pb-3">
            <div class="d-flex flex-column flex-xxl-row justify-content-between align-items-xxl-end gap-3">
                <div>
                    <div class="dashboard-eyebrow mb-2">WheelyGoodCars control room</div>
                    <h1 class="display-6 fw-bold mb-2">Aanbod dashboard</h1>
                    <p class="text-white-50 mb-0">Realtime overzicht van het aanbod, views en aanbieders. De data ververst automatisch elke 10 seconden.</p>
                </div>
                <div class="text-xxl-end">
                    <div class="small text-white-50 mb-1">Laatst bijgewerkt</div>
                    <div id="dashboardUpdatedAt" class="fw-semibold">-</div>
                    <div class="small text-white-50 mt-2">Volgende verversing</div>
                    <div class="progress dashboard-refresh-progress mt-1">
                        <div id="dashboardRefreshBar" class="progress-bar" role="progressbar" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="px-4 px-xl-5 pt-4 pb-4">
            <div class="row g-3 mb-3">
                <div class="col-6 col-xl-2">
                    <div class="dashboard-kpi-card">
                        <span class="kpi-label">Aantal auto's aangeboden</span>
                        <span id="metricTotalCars" class="kpi-value">0</span>
                    </div>
                </div>
                <div class="col-6 col-xl-2">
                    <div class="dashboard-kpi-card">
                        <span class="kpi-label">Aantal verkocht</span>
                        <span id="metricSoldCars" class="kpi-value">0</span>
                    </div>
                </div>
                <div class="col-6 col-xl-2">
                    <div class="dashboard-kpi-card">
                        <span class="kpi-label">Vandaag aangeboden</span>
                        <span id="metricTodayOffered" class="kpi-value">0</span>
                    </div>
                </div>
                <div class="col-6 col-xl-2">
                    <div class="dashboard-kpi-card">
                        <span class="kpi-label">Aantal aanbieders</span>
                        <span id="metricProviders" class="kpi-value">0</span>
                    </div>
                </div>
                <div class="col-6 col-xl-2">
                    <div class="dashboard-kpi-card">
                        <span class="kpi-label">Views vandaag</span>
                        <span id="metricTodayViews" class="kpi-value">0</span>
                    </div>
                </div>
                <div class="col-6 col-xl-2">
                    <div class="dashboard-kpi-card">
                        <span class="kpi-label">Gem. auto's per aanbieder</span>
                        <span id="metricAveragePerProvider" class="kpi-value">0</span>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12 col-xl-4">
                    <div class="dashboard-panel h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="h5 mb-0">Verdeling</h2>
                            <span id="metricSoldRatio" class="badge rounded-pill text-bg-warning">0%</span>
                        </div>
                        <div class="chart-stage chart-stage--donut">
                            <canvas id="statusChart"></canvas>
                        </div>
                        <div class="mt-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span>Verkocht aandeel</span>
                                <span id="soldProgressLabel">0%</span>
                            </div>
                            <div class="progress sold-progress">
                                <div id="soldProgressBar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-5">
                    <div class="dashboard-panel h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="h5 mb-0">Aanbod vs verkocht</h2>
                            <span class="text-white-50 small">Laatste 14 dagen</span>
                        </div>
                        <div class="chart-stage chart-stage--wide">
                            <canvas id="dailyChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-3">
                    <div class="dashboard-panel h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="h5 mb-0">Top aanbieders</h2>
                            <span class="text-white-50 small">Aantal auto's</span>
                        </div>
                        <div class="chart-stage chart-stage--wide">
                            <canvas id="providersChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-12 col-xl-8">
                    <div class="dashboard-panel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="h5 mb-0">Views trend</h2>
                            <span class="text-white-50 small">Laatste 14 dagen</span>
                        </div>
                        <div class="chart-stage chart-stage--short">
                            <canvas id="viewsChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-4">
                    <div class="dashboard-panel dashboard-panel--summary">
                        <h2 class="h5 mb-3">Tijdlijn</h2>
                        <ul class="dashboard-summary-list list-unstyled mb-0">
                            <li><span>Nieuwe auto’s vandaag</span><strong id="summaryTodayOffered">0</strong></li>
                            <li><span>Views vandaag</span><strong id="summaryTodayViews">0</strong></li>
                            <li><span>Aantal aanbieders</span><strong id="summaryProviders">0</strong></li>
                            <li><span>Gemiddeld per aanbieder</span><strong id="summaryAveragePerProvider">0</strong></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>
    <script>
        (() => {
            const initialPayloadElement = document.getElementById('dashboardInitialData');
            const initialPayload = initialPayloadElement ? JSON.parse(initialPayloadElement.textContent || '{}') : {};
            const endpoint = document.querySelector('[data-dashboard-endpoint]')?.dataset.dashboardEndpoint;
            const refreshMs = 10000;
            const refreshSteps = 100;
            let refreshRemaining = refreshSteps;
            let refreshTimer = null;

            const elements = {
                updatedAt: document.getElementById('dashboardUpdatedAt'),
                refreshBar: document.getElementById('dashboardRefreshBar'),
                metricTotalCars: document.getElementById('metricTotalCars'),
                metricSoldCars: document.getElementById('metricSoldCars'),
                metricTodayOffered: document.getElementById('metricTodayOffered'),
                metricProviders: document.getElementById('metricProviders'),
                metricTodayViews: document.getElementById('metricTodayViews'),
                metricAveragePerProvider: document.getElementById('metricAveragePerProvider'),
                metricSoldRatio: document.getElementById('metricSoldRatio'),
                soldProgressBar: document.getElementById('soldProgressBar'),
                soldProgressLabel: document.getElementById('soldProgressLabel'),
                summaryTodayOffered: document.getElementById('summaryTodayOffered'),
                summaryTodayViews: document.getElementById('summaryTodayViews'),
                summaryProviders: document.getElementById('summaryProviders'),
                summaryAveragePerProvider: document.getElementById('summaryAveragePerProvider'),
            };

            const formatNumber = new Intl.NumberFormat('nl-NL');

            const chartBaseOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: { color: '#d1d5db' },
                    },
                },
                scales: {
                    x: {
                        ticks: { color: '#9ca3af' },
                        grid: { color: 'rgba(255,255,255,0.06)' },
                    },
                    y: {
                        ticks: { color: '#9ca3af' },
                        grid: { color: 'rgba(255,255,255,0.06)' },
                    },
                },
            };

            const statusChart = new Chart(document.getElementById('statusChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Actief', 'Verkocht'],
                    datasets: [{
                        data: [0, 0],
                        backgroundColor: ['#ffb15b', '#4f46e5'],
                        borderWidth: 0,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    aspectRatio: 1.02,
                    cutout: '72%',
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: { color: '#d1d5db' },
                        },
                    },
                },
            });

            const dailyChart = new Chart(document.getElementById('dailyChart'), {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [
                        {
                            label: 'Aangeboden',
                            data: [],
                            borderColor: '#ff9a3d',
                            backgroundColor: 'rgba(255,154,61,0.12)',
                            tension: 0.35,
                            fill: true,
                        },
                        {
                            label: 'Verkocht',
                            data: [],
                            borderColor: '#7c3aed',
                            backgroundColor: 'rgba(124,58,237,0.12)',
                            tension: 0.35,
                            fill: true,
                        },
                    ],
                },
                options: chartBaseOptions,
            });

            const viewsChart = new Chart(document.getElementById('viewsChart'), {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Views',
                        data: [],
                        backgroundColor: 'rgba(34, 197, 94, 0.85)',
                        borderRadius: 8,
                    }],
                },
                options: chartBaseOptions,
            });

            const providersChart = new Chart(document.getElementById('providersChart'), {
                type: 'bar',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Auto’s',
                        data: [],
                        backgroundColor: 'rgba(251, 191, 36, 0.85)',
                        borderRadius: 8,
                        maxBarThickness: 18,
                        borderRadius: 8,
                    }],
                },
                options: {
                    ...chartBaseOptions,
                    indexAxis: 'y',
                    plugins: {
                        legend: {
                            display: false,
                        },
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            ticks: {
                                color: '#9ca3af',
                                precision: 0,
                            },
                            grid: { color: 'rgba(255,255,255,0.06)' },
                        },
                        y: {
                            ticks: {
                                color: '#9ca3af',
                                autoSkip: false,
                            },
                            grid: { display: false },
                        },
                    },
                },
            });

            function updateMetricText(element, value) {
                if (element) {
                    element.textContent = value;
                }
            }

            function renderData(payload) {
                const metrics = payload.metrics;
                const charts = payload.charts;

                updateMetricText(elements.metricTotalCars, formatNumber.format(metrics.total_cars));
                updateMetricText(elements.metricSoldCars, formatNumber.format(metrics.sold_cars));
                updateMetricText(elements.metricTodayOffered, formatNumber.format(metrics.today_offered));
                updateMetricText(elements.metricProviders, formatNumber.format(metrics.providers));
                updateMetricText(elements.metricTodayViews, formatNumber.format(metrics.today_views));
                updateMetricText(elements.metricAveragePerProvider, metrics.average_cars_per_provider.toFixed(1));
                updateMetricText(elements.metricSoldRatio, `${metrics.sold_ratio.toFixed(1)}%`);
                updateMetricText(elements.soldProgressLabel, `${metrics.sold_ratio.toFixed(1)}% verkocht`);
                updateMetricText(elements.summaryTodayOffered, formatNumber.format(metrics.today_offered));
                updateMetricText(elements.summaryTodayViews, formatNumber.format(metrics.today_views));
                updateMetricText(elements.summaryProviders, formatNumber.format(metrics.providers));
                updateMetricText(elements.summaryAveragePerProvider, metrics.average_cars_per_provider.toFixed(1));
                updateMetricText(elements.updatedAt, new Date(payload.generated_at).toLocaleString('nl-NL'));

                const soldRatioWidth = Math.max(0, Math.min(100, metrics.sold_ratio));
                elements.soldProgressBar.style.width = `${soldRatioWidth}%`;

                statusChart.data.labels = charts.status.labels;
                statusChart.data.datasets[0].data = charts.status.values;
                statusChart.update();

                dailyChart.data.labels = charts.daily.labels;
                dailyChart.data.datasets[0].data = charts.daily.offers;
                dailyChart.data.datasets[1].data = charts.daily.sold;
                dailyChart.update();

                viewsChart.data.labels = charts.daily.labels;
                viewsChart.data.datasets[0].data = charts.daily.views;
                viewsChart.update();

                providersChart.data.labels = charts.providers.labels;
                providersChart.data.datasets[0].data = charts.providers.values;
                providersChart.options.scales.x.suggestedMax = Math.max(...charts.providers.values, 1) + 1;
                providersChart.update();
            }

            async function refreshDashboard() {
                if (!endpoint) {
                    return;
                }

                const response = await fetch(endpoint, {
                    headers: { 'Accept': 'application/json' },
                    cache: 'no-store',
                });

                if (!response.ok) {
                    return;
                }

                const payload = await response.json();
                renderData(payload);
                refreshRemaining = refreshSteps;
            }

            function tickRefreshBar() {
                refreshRemaining -= 1;
                const width = Math.max(0, (refreshRemaining / refreshSteps) * 100);
                elements.refreshBar.style.width = `${width}%`;

                if (refreshRemaining <= 0) {
                    refreshRemaining = refreshSteps;
                    refreshDashboard();
                }
            }

            renderData(initialPayload);
            refreshDashboard();
            refreshTimer = setInterval(tickRefreshBar, refreshMs / refreshSteps);
        })();
    </script>
@endsection