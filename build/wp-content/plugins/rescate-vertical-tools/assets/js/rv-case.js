(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {
		var form = document.getElementById('rv-case-form');
		if (!form || typeof rvCaseData === 'undefined') {
			return;
		}

		var textarea = document.getElementById('rv_caso_texto');
		var submitBtn = form.querySelector('.rv-case-submit');
		var result = document.getElementById('rv-case-result');

		function showResult(text, isError) {
			result.hidden = false;
			result.textContent = text;
			result.classList.toggle('is-error', !!isError);
		}

		form.addEventListener('submit', function (event) {
			event.preventDefault();

			var caso = textarea.value.trim();
			if (!caso) {
				showResult(rvCaseData.i18n.empty, true);
				return;
			}

			submitBtn.disabled = true;
			showResult(rvCaseData.i18n.loading, false);

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
						showResult(json.data.texto, false);
					} else {
						var message = json && json.data && json.data.message ? json.data.message : rvCaseData.i18n.generic;
						showResult(message, true);
					}
				})
				.catch(function () {
					showResult(rvCaseData.i18n.generic, true);
				})
				.finally(function () {
					submitBtn.disabled = false;
				});
		});
	});
})();
