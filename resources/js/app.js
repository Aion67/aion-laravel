import './bootstrap';

import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;

window.Chart = Chart;

const mountedCharts = new WeakSet();

function mountReportCharts() {
	document.querySelectorAll('[data-report-chart]').forEach((canvas) => {
		if (mountedCharts.has(canvas)) {
			return;
		}

		const configSource = canvas.dataset.reportChart;

		if (!configSource) {
			return;
		}

		const config = JSON.parse(configSource);

		new Chart(canvas, config);
		mountedCharts.add(canvas);
	});
}

function downloadReportChart(chartId) {
	const canvas = document.getElementById(chartId);

	if (!(canvas instanceof HTMLCanvasElement)) {
		return;
	}

	const link = document.createElement('a');
	link.href = canvas.toDataURL('image/png', 1.0);
	link.download = `${chartId}.png`;
	link.click();
}

document.addEventListener('DOMContentLoaded', mountReportCharts);

document.addEventListener('click', (event) => {
	const button = event.target.closest('[data-report-chart-download]');

	if (!button) {
		return;
	}

	const chartId = button.dataset.reportChartDownload;

	if (!chartId) {
		return;
	}

	downloadReportChart(chartId);
});

Alpine.start();
