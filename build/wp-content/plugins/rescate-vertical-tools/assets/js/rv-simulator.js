(function () {
	'use strict';

	// Paleta clara, alineada al tema: fondo suave + borde y etiqueta de color por nivel.
	var LEVELS = {
		seguro: {
			bg: '#EDF7F0',
			border: '#1F7A4D',
			tagBg: '#1F7A4D',
			tagText: '#FFFFFF',
			textColor: '#1D3A2A'
		},
		moderado: {
			bg: '#FFF6E9',
			border: '#C97A1E',
			tagBg: '#C97A1E',
			tagText: '#FFFFFF',
			textColor: '#4A3A1F'
		},
		peligroso: {
			bg: '#FDEEEA',
			border: '#C0341A',
			tagBg: '#C0341A',
			tagText: '#FFFFFF',
			textColor: '#4A231A'
		}
	};

	function initSimulator(container) {
		var fall = container.querySelector('.rv-sim-fall');
		var rope = container.querySelector('.rv-sim-rope');
		var angle = container.querySelector('.rv-sim-angle');
		var fallOut = container.querySelector('.rv-sim-fall-out');
		var ropeOut = container.querySelector('.rv-sim-rope-out');
		var angleOut = container.querySelector('.rv-sim-angle-out');
		var fcNum = container.querySelector('.rv-sim-fc-num');
		var fcTag = container.querySelector('.rv-sim-fc-tag');
		var feedback = container.querySelector('.rv-sim-feedback');
		var panel = container.querySelector('.rv-sim-result');

		if (!fall || !rope || !angle) {
			return;
		}

		var safeMax = parseFloat(container.dataset.safeMax) || 0.3;
		var moderateMax = parseFloat(container.dataset.moderateMax) || 1;
		var angleWarn = parseFloat(container.dataset.angleWarn) || 60;
		var angleCritical = parseFloat(container.dataset.angleCritical) || 120;
		var msgSafe = container.dataset.msgSafe || '';
		var msgModerate = container.dataset.msgModerate || '';
		var msgDangerous = container.dataset.msgDangerous || '';
		var msgAngleWarn = container.dataset.msgAngleWarn || '';
		var msgAngleCritical = container.dataset.msgAngleCritical || '';

		function update() {
			var f = parseFloat(fall.value);
			var r = parseFloat(rope.value);
			var a = parseFloat(angle.value);

			fallOut.textContent = f.toFixed(1);
			ropeOut.textContent = r.toFixed(1);
			angleOut.textContent = a.toFixed(0);

			var fc = Math.min(f / r, 2);
			fcNum.textContent = fc.toFixed(2);

			var level, msg;
			if (fc <= safeMax) {
				level = 'seguro';
				msg = msgSafe;
			} else if (fc <= moderateMax) {
				level = 'moderado';
				msg = msgModerate;
			} else {
				level = 'peligroso';
				msg = msgDangerous;
			}

			if (a > angleWarn) {
				msg += a >= angleCritical ? ' ' + msgAngleCritical : ' ' + msgAngleWarn;
			}

			var theme = LEVELS[level];
			fcTag.textContent = level;
			fcTag.style.background = theme.tagBg;
			fcTag.style.color = theme.tagText;
			feedback.textContent = msg;
			feedback.style.color = theme.textColor;
			panel.style.background = theme.bg;
			panel.style.borderLeftColor = theme.border;
		}

		[fall, rope, angle].forEach(function (el) {
			el.addEventListener('input', update);
		});
		update();
	}

	function init() {
		var sims = document.querySelectorAll('.rv-sim');
		Array.prototype.forEach.call(sims, initSimulator);
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
