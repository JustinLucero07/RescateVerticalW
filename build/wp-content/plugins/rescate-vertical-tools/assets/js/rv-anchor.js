(function () {
	'use strict';

	var LEVELS = {
		eficiente: { bg: '#EDF7F0', border: '#1F7A4D', tagBg: '#1F7A4D', text: '#1D3A2A' },
		atencion:  { bg: '#FFF6E9', border: '#C97A1E', tagBg: '#C97A1E', text: '#4A3A1F' },
		critico:   { bg: '#FDEEEA', border: '#C0341A', tagBg: '#C0341A', text: '#4A231A' }
	};

	var LABEL = { eficiente: 'eficiente', atencion: 'atención', critico: 'crítico' };

	// Geometría del esquema
	var CX = 130, CY = 160, LEG = 116;

	function initAnchor(box) {
		var load = box.querySelector('.rv-anc-load');
		var angle = box.querySelector('.rv-anc-angle');
		if (!load || !angle) {
			return;
		}

		var loadOut = box.querySelector('.rv-anc-load-out');
		var angleOut = box.querySelector('.rv-anc-angle-out');
		var forceEl = box.querySelector('.rv-anc-force');
		var pctEl = box.querySelector('.rv-anc-pct');
		var marginEl = box.querySelector('.rv-anc-margin');
		var forceBig = box.querySelector('.rv-anc-force-big');
		var tag = box.querySelector('.rv-anc-tag');
		var feedback = box.querySelector('.rv-anc-feedback');
		var panel = box.querySelector('.rv-anc-result');

		var legL = box.querySelector('.rv-anc-leg-l');
		var legR = box.querySelector('.rv-anc-leg-r');
		var capL = box.querySelector('.rv-anc-cap-l');
		var capR = box.querySelector('.rv-anc-cap-r');
		var arc = box.querySelector('.rv-anc-arc');
		var arcLabel = box.querySelector('.rv-anc-arc-label');

		var warn = parseFloat(box.dataset.angleWarn) || 60;
		var critical = parseFloat(box.dataset.angleCritical) || 120;
		var rating = parseFloat(box.dataset.anchorRating) || 22;
		var msgSafe = box.dataset.msgSafe || '';
		var msgWarn = box.dataset.msgWarn || '';
		var msgCritical = box.dataset.msgCritical || '';

		function nf(n, d) {
			return n.toFixed(d).replace('.', ',');
		}

		function drawSchematic(deg) {
			var half = (deg / 2) * Math.PI / 180;
			var dx = LEG * Math.sin(half);
			var dy = LEG * Math.cos(half);
			var lx = CX - dx, ly = CY - dy;
			var rx = CX + dx, ry = CY - dy;

			legL.setAttribute('x2', lx.toFixed(1));
			legL.setAttribute('y2', ly.toFixed(1));
			legR.setAttribute('x2', rx.toFixed(1));
			legR.setAttribute('y2', ry.toFixed(1));

			// La "estructura" queda perpendicular a cada rama
			capL.setAttribute('transform', 'translate(' + (lx - 86).toFixed(1) + ',' + (ly - 52).toFixed(1) + ') rotate(' + (-half * 180 / Math.PI).toFixed(1) + ',86,52)');
			capR.setAttribute('transform', 'translate(' + (rx - 174).toFixed(1) + ',' + (ry - 52).toFixed(1) + ') rotate(' + (half * 180 / Math.PI).toFixed(1) + ',174,52)');

			// Arco del ángulo sobre el punto maestro
			var r = 46;
			var ax = CX - r * Math.sin(half), ay = CY - r * Math.cos(half);
			var bx = CX + r * Math.sin(half), by = CY - r * Math.cos(half);
			arc.setAttribute('d', 'M' + ax.toFixed(1) + ' ' + ay.toFixed(1) + ' A ' + r + ' ' + r + ' 0 0 0 ' + bx.toFixed(1) + ' ' + by.toFixed(1));
			arcLabel.textContent = deg.toFixed(0) + '°';
			arcLabel.setAttribute('y', (CY - r - 10).toFixed(1));
		}

		function update() {
			var L = parseFloat(load.value);
			var deg = parseFloat(angle.value);

			loadOut.textContent = nf(L, 1);
			angleOut.textContent = deg.toFixed(0);

			// F = L / (2 * cos(theta/2))
			var half = (deg / 2) * Math.PI / 180;
			var denom = 2 * Math.cos(half);
			var F = denom > 0.0001 ? L / denom : L * 999;
			var pct = (F / L) * 100;
			var margin = F > 0 ? rating / F : 0;

			forceEl.textContent = nf(F, 2) + ' kN';
			pctEl.textContent = Math.round(pct) + ' %';
			marginEl.textContent = nf(margin, 1) + '×';
			forceBig.textContent = nf(F, 2) + ' kN';

			var level;
			var msg;
			if (deg > critical || F > rating) {
				level = 'critico';
				msg = msgCritical;
			} else if (deg > warn) {
				level = 'atencion';
				msg = msgWarn;
			} else {
				level = 'eficiente';
				msg = msgSafe;
			}

			// Aviso extra cuando la fuerza supera lo que resiste el anclaje
			if (F > rating) {
				msg += ' La fuerza por anclaje (' + nf(F, 2) + ' kN) supera los ' + nf(rating, 0) + ' kN que resiste el punto.';
			}

			var th = LEVELS[level];
			tag.textContent = LABEL[level];
			tag.style.background = th.tagBg;
			tag.style.color = '#FFFFFF';
			feedback.textContent = msg;
			feedback.style.color = th.text;
			panel.style.background = th.bg;
			panel.style.borderLeftColor = th.border;
			marginEl.style.color = margin < 5 ? '#C0341A' : '#101820';

			drawSchematic(deg);
		}

		[load, angle].forEach(function (el) {
			el.addEventListener('input', update);
		});
		update();
	}

	function init() {
		Array.prototype.forEach.call(document.querySelectorAll('.rv-anc'), initAnchor);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
