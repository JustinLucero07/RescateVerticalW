(function () {
	'use strict';

	/* ---------- Menú móvil ---------- */
	function initNav() {
		var toggle = document.getElementById('rv-nav-toggle');
		var nav = document.getElementById('rv-nav');
		if (!toggle || !nav) {
			return;
		}

		toggle.addEventListener('click', function () {
			var open = nav.classList.toggle('is-open');
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
		});

		nav.addEventListener('click', function (event) {
			if (event.target.tagName === 'A' && nav.classList.contains('is-open')) {
				nav.classList.remove('is-open');
				toggle.setAttribute('aria-expanded', 'false');
			}
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && nav.classList.contains('is-open')) {
				nav.classList.remove('is-open');
				toggle.setAttribute('aria-expanded', 'false');
				toggle.focus();
			}
		});
	}

	/* ---------- Animaciones de entrada al hacer scroll ---------- */
	function initReveal() {
		var items = document.querySelectorAll('.rv-reveal');
		if (!items.length) {
			return;
		}

		var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

		// Sin IntersectionObserver (o con movimiento reducido) se muestra todo de una.
		if (reduced || !('IntersectionObserver' in window)) {
			items.forEach(function (el) {
				el.classList.add('is-visible');
			});
			return;
		}

		var observer = new IntersectionObserver(
			function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						entry.target.classList.add('is-visible');
						observer.unobserve(entry.target);
					}
				});
			},
			{ threshold: 0.12, rootMargin: '0px 0px -8% 0px' }
		);

		items.forEach(function (el) {
			observer.observe(el);
		});
	}

	function init() {
		initNav();
		initReveal();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
