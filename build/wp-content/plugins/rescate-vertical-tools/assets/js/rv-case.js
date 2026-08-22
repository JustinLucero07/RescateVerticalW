(function () {
	'use strict';

	/**
	 * Escapa HTML antes de cualquier transformación: así ninguna etiqueta
	 * del texto devuelto por la IA puede llegar viva al DOM.
	 */
	function escapeHtml(s) {
		return String(s)
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;');
	}

	/** Formato en línea: negrita, cursiva y código. Sobre texto ya escapado. */
	function inline(s) {
		return s
			.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>')
			.replace(/(^|[\s(¡¿])\*([^*\n]+)\*(?=[\s).,;:!?¡¿]|$)/g, '$1<em>$2</em>')
			.replace(/`([^`]+)`/g, '<code>$1</code>');
	}

	/**
	 * Convierte el markdown que devuelve Gemini (títulos, listas, negritas)
	 * en HTML. Deliberadamente limitado: solo el subconjunto que la IA usa.
	 */
	function renderMarkdown(raw) {
		var lines = escapeHtml(raw).split('\n');
		var html = '';
		// Pila de listas abiertas: cada entrada es { tag: 'ul'|'ol', indent: n }
		var stack = [];

		function closeTo(depth) {
			while (stack.length > depth) {
				html += '</' + stack.pop().tag + '>';
			}
		}

		function openItem(tag, indent, content) {
			// Cerrar niveles más profundos o del mismo nivel con otro tipo
			while (
				stack.length &&
				(stack[stack.length - 1].indent > indent ||
					(stack[stack.length - 1].indent === indent && stack[stack.length - 1].tag !== tag))
			) {
				html += '</' + stack.pop().tag + '>';
			}
			if (!stack.length || stack[stack.length - 1].indent < indent) {
				html += '<' + tag + '>';
				stack.push({ tag: tag, indent: indent });
			}
			html += '<li>' + inline(content) + '</li>';
		}

		lines.forEach(function (line) {
			// La sangría define el nivel de anidación (tab = 2 espacios)
			var indent = (line.match(/^[ \t]*/)[0] || '').replace(/\t/g, '  ').length;
			var t = line.trim();

			if (t === '') {
				closeTo(0);
				return;
			}

			// separador ---
			if (/^-{3,}$/.test(t)) {
				closeTo(0);
				html += '<hr>';
				return;
			}

			// títulos # .. ####
			var h = t.match(/^(#{1,4})\s+(.+)$/);
			if (h) {
				closeTo(0);
				var lvl = Math.min(h[1].length + 2, 5);
				html += '<h' + lvl + '>' + inline(h[2]) + '</h' + lvl + '>';
				return;
			}

			// viñeta o numeral sin contenido: descartar (deja "*" sueltos si no)
			if (/^([-*•]|\d+[.)])\s*$/.test(t)) {
				return;
			}

			var ul = t.match(/^[-*•]\s+(.+)$/);
			if (ul) {
				openItem('ul', indent, ul[1]);
				return;
			}

			var ol = t.match(/^\d+[.)]\s+(.+)$/);
			if (ol) {
				openItem('ol', indent, ol[1]);
				return;
			}

			closeTo(0);
			html += '<p>' + inline(t) + '</p>';
		});

		closeTo(0);
		return html;
	}

	document.addEventListener('DOMContentLoaded', function () {
		var form = document.getElementById('rv-case-form');
		if (!form || typeof rvCaseData === 'undefined') {
			return;
		}

		var textarea = document.getElementById('rv_caso_texto');
		var submitBtn = form.querySelector('.rv-case-submit');
		var result = document.getElementById('rv-case-result');

		/** Estados de aviso: texto plano, nunca HTML. */
		function showNotice(text, isError) {
			result.hidden = false;
			result.classList.toggle('is-error', !!isError);
			result.textContent = text;
		}

		/** Respuesta de la IA: markdown renderizado. */
		function showAnswer(markdown) {
			result.hidden = false;
			result.classList.remove('is-error');
			result.innerHTML = renderMarkdown(markdown);
		}

		form.addEventListener('submit', function (event) {
			event.preventDefault();

			var caso = textarea.value.trim();
			if (!caso) {
				showNotice(rvCaseData.i18n.empty, true);
				textarea.focus();
				return;
			}

			submitBtn.disabled = true;
			showNotice(rvCaseData.i18n.loading, false);

			var body = new URLSearchParams();
			body.set('action', rvCaseData.action);
			body.set('nonce', rvCaseData.nonce);
			body.set('rv_caso_texto', caso);

			fetch(rvCaseData.ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
				body: body.toString()
			})
				.then(function (response) {
					return response.json();
				})
				.then(function (json) {
					if (json && json.success && json.data && json.data.texto) {
						showAnswer(json.data.texto);
					} else {
						var message = json && json.data && json.data.message
							? json.data.message
							: rvCaseData.i18n.generic;
						showNotice(message, true);
					}
				})
				.catch(function () {
					showNotice(rvCaseData.i18n.generic, true);
				})
				.finally(function () {
					submitBtn.disabled = false;
				});
		});
	});
})();
