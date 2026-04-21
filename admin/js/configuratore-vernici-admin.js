(function() {
	'use strict';

	function drawQuoteChart(canvas, chartData) {
		if (!canvas || !chartData || !Array.isArray(chartData.labels) || !Array.isArray(chartData.counts)) {
			return;
		}

		var context = canvas.getContext('2d');

		if (!context) {
			return;
		}

		var ratio = window.devicePixelRatio || 1;
		var rect = canvas.getBoundingClientRect();
		var width = Math.max(rect.width, 320);
		var height = Math.max(rect.height, 220);
		var labels = chartData.labels;
		var counts = chartData.counts.map(function(count) {
			return Number(count) || 0;
		});
		var max = Math.max.apply(null, counts.concat([1]));
		var padding = {
			top: 22,
			right: 18,
			bottom: 44,
			left: 36
		};
		var plotWidth = width - padding.left - padding.right;
		var plotHeight = height - padding.top - padding.bottom;
		var gap = 8;
		var barWidth = Math.max(10, (plotWidth / counts.length) - gap);

		canvas.width = width * ratio;
		canvas.height = height * ratio;
		context.setTransform(ratio, 0, 0, ratio, 0, 0);
		context.clearRect(0, 0, width, height);

		context.fillStyle = '#ffffff';
		context.fillRect(0, 0, width, height);

		context.strokeStyle = '#dcdcde';
		context.lineWidth = 1;
		context.beginPath();
		context.moveTo(padding.left, padding.top);
		context.lineTo(padding.left, padding.top + plotHeight);
		context.lineTo(padding.left + plotWidth, padding.top + plotHeight);
		context.stroke();

		context.fillStyle = '#646970';
		context.font = '12px -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif';
		context.textAlign = 'right';
		context.textBaseline = 'middle';

		for (var step = 0; step <= 4; step++) {
			var value = Math.round((max / 4) * step);
			var y = padding.top + plotHeight - ((value / max) * plotHeight);

			context.strokeStyle = '#f0f0f1';
			context.beginPath();
			context.moveTo(padding.left, y);
			context.lineTo(padding.left + plotWidth, y);
			context.stroke();

			context.fillStyle = '#646970';
			context.fillText(String(value), padding.left - 8, y);
		}

		counts.forEach(function(count, index) {
			var x = padding.left + (index * (plotWidth / counts.length)) + (gap / 2);
			var barHeight = (count / max) * plotHeight;
			var y = padding.top + plotHeight - barHeight;

			context.fillStyle = '#2271b1';
			context.fillRect(x, y, barWidth, barHeight);

			context.fillStyle = '#1d2327';
			context.textAlign = 'center';
			context.textBaseline = 'bottom';
			context.fillText(String(count), x + (barWidth / 2), y - 4);

			context.save();
			context.translate(x + (barWidth / 2), padding.top + plotHeight + 18);
			context.rotate(-Math.PI / 5);
			context.fillStyle = '#646970';
			context.textAlign = 'right';
			context.textBaseline = 'middle';
			context.fillText(labels[index] || '', 0, 0);
			context.restore();
		});
	}

	document.addEventListener('DOMContentLoaded', function() {
		var canvas = document.querySelector('[data-configuratore-chart]');
		var data = window.configuratoreVerniciAdmin ? window.configuratoreVerniciAdmin.quoteChart : null;

		drawQuoteChart(canvas, data);
	});
})();
