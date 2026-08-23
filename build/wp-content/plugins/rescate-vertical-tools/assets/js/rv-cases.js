(function () {
	'use strict';

	function escapeHtml(s) {
		return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
	}

	/** Markdown mínimo: títulos, viñetas y negrita. Sobre texto ya escapado. */
	function md(raw) {
		var lines = escapeHtml(raw).split('\n');
		var html = '';
		var inList = false;

		function closeList() {
			if (inList) { html += '</ul>'; inList = false; }
		}

		lines.forEach(function (line) {
			var t = line.trim();
			if (t === '') { closeList(); return; }

			var h = t.match(/^#{1,4}\s+(.+)$/);
			if (h) { closeList(); html += '<h4>' + inline(h[1]) + '</h4>'; return; }

			var li = t.match(/^[-*•]\s+(.+)$/);
			if (li) {
				if (!inList) { html += '<ul>'; inList = true; }
				html += '<li>' + inline(li[1]) + '</li>';
				return;
			}

			closeList();
			html += '<p>' + inline(t) + '</p>';
		});
		closeList();
		return html;
	}

	function inline(s) {
		return s.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
	}

	document.addEventListener('DOMContentLoaded', function () {
		var root = document.getElementById('rv-caso');
		if (!root || typeof rvCasesData === 'undefined') {
			return;
		}

		var titulo = document.getElementById('rv-caso-titulo');
		var enunciado = document.getElementById('rv-caso-enunciado');
		var form = document.getElementById('rv-caso-form');
		var correccion = document.getElementById('rv-caso-correccion');
		var btnNuevo = document.getElementById('rv-caso-nuevo');
		var fcRow = document.getElementById('rv-caso-fc-row');
		var fcInput = document.getElementById('rv-caso-fc');
		var riesgos = document.getElementById('rv-caso-riesgos');
		var medico = document.getElementById('rv-caso-medico');
		var accion = document.getElementById('rv-caso-accion');
		var enviar = form.querySelector('.rv-caso-enviar');

		var casoActual = 0;

		function post(action, extra) {
			var body = new URLSearchParams();
			body.set('action', action);
			body.set('nonce', rvCasesData.nonce);
			Object.keys(extra || {}).forEach(function (k) {
				body.set(k, extra[k]);
			});
			return fetch(rvCasesData.ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: body.toString()
			}).then(function (r) { return r.json(); });
		}

		function cargarCaso() {
			titulo.textContent = '';
			enunciado.innerHTML = '<p>' + escapeHtml(rvCasesData.i18n.loading) + '</p>';
			form.hidden = true;
			correccion.hidden = true;
			correccion.innerHTML = '';
			btnNuevo.disabled = true;

			post(rvCasesData.actionGet, { exclude: casoActual })
				.then(function (json) {
					if (!json || !json.success) {
						var m = json && json.data && json.data.message ? json.data.message : rvCasesData.i18n.noCases;
						enunciado.innerHTML = '<p>' + escapeHtml(m) + '</p>';
						return;
					}
					casoActual = json.data.id;
					titulo.textContent = json.data.titulo;
					enunciado.innerHTML = json.data.enunciado;

					// Solo se pregunta el factor de caída si el caso trae los datos
					fcRow.hidden = !json.data.preguntaFc;

					fcInput.value = '';
					riesgos.value = '';
					medico.value = '';
					accion.value = '';
					form.hidden = false;
				})
				.catch(function () {
					enunciado.innerHTML = '<p>' + escapeHtml(rvCasesData.i18n.generic) + '</p>';
				})
				.finally(function () {
					btnNuevo.disabled = false;
				});
		}

		function pintarCorreccion(d) {
			var html = '';

			if (d.fc) {
				var ok = d.fc.correcto;
				html += '<div class="rv-caso-fc ' + (ok ? 'is-ok' : 'is-ko') + '">'
					+ '<span class="rv-caso-fc-tag">' + (ok ? 'Correcto' : 'A revisar') + '</span>'
					+ '<div class="rv-caso-fc-body">'
					+ '<strong>Factor de caída: ' + escapeHtml(d.fc.real) + '</strong>'
					+ (d.fc.tuyo !== '' ? ' <span class="rv-caso-fc-tuyo">(tu respuesta: ' + escapeHtml(d.fc.tuyo) + ')</span>' : ' <span class="rv-caso-fc-tuyo">(no respondiste)</span>')
					+ '<div class="rv-caso-fc-calc">' + escapeHtml(d.fc.calculo) + '</div>'
					+ '</div></div>';
			}

			if (d.ia) {
				html += '<div class="rv-caso-bloque rv-caso-bloque--ia">'
					+ '<h4 class="rv-caso-bloque-t">Sobre tus respuestas</h4>'
					+ md(d.ia) + '</div>';
			}

			if (d.modelo) {
				html += '<div class="rv-caso-bloque">'
					+ '<h4 class="rv-caso-bloque-t">Lo que debería haberse hecho</h4>'
					+ md(d.modelo) + '</div>';
			}

			correccion.innerHTML = html;
			correccion.hidden = false;
			correccion.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
		}

		form.addEventListener('submit', function (e) {
			e.preventDefault();

			var algo = fcInput.value.trim() || riesgos.value.trim() || medico.value.trim() || accion.value.trim();
			if (!algo) {
				correccion.hidden = false;
				correccion.innerHTML = '<p class="rv-caso-aviso">' + escapeHtml(rvCasesData.i18n.needAnswer) + '</p>';
				return;
			}

			enviar.disabled = true;
			correccion.hidden = false;
			correccion.innerHTML = '<p class="rv-caso-aviso">' + escapeHtml(rvCasesData.i18n.checking) + '</p>';

			post(rvCasesData.actionCheck, {
				caso: casoActual,
				fc: fcInput.value,
				riesgos: riesgos.value,
				medico: medico.value,
				accion: accion.value
			})
				.then(function (json) {
					if (json && json.success) {
						pintarCorreccion(json.data);
					} else {
						var m = json && json.data && json.data.message ? json.data.message : rvCasesData.i18n.generic;
						correccion.innerHTML = '<p class="rv-caso-aviso">' + escapeHtml(m) + '</p>';
					}
				})
				.catch(function () {
					correccion.innerHTML = '<p class="rv-caso-aviso">' + escapeHtml(rvCasesData.i18n.generic) + '</p>';
				})
				.finally(function () {
					enviar.disabled = false;
				});
		});

		btnNuevo.addEventListener('click', cargarCaso);
		cargarCaso();
	});
})();
