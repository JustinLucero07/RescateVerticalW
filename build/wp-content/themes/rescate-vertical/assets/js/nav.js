(function () {
	'use strict';
	var toggle = document.getElementById('rv-nav-toggle');
	var menu = document.getElementById('rv-navlinks');

	if (!toggle || !menu) {
		return;
	}

	toggle.addEventListener('click', function () {
		var isOpen = menu.classList.toggle('is-open');
		toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
	});

	menu.addEventListener('click', function (event) {
		if (event.target.tagName === 'A' && menu.classList.contains('is-open')) {
			menu.classList.remove('is-open');
			toggle.setAttribute('aria-expanded', 'false');
		}
	});
})();
