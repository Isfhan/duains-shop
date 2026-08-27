/**
 * Duains Fragrances — luxury glass header overrides
 * ==================================================
 * Loaded AFTER vendor/shop/themes/default/aimeos.js (and after the
 * deferred component scripts in <head> execute). Extends the Aimeos
 * JS namespaces with the official decorator pattern: the original
 * init() is kept and called first, then custom behaviour is attached.
 *
 * This is safe because every wrapped init uses a `this.once` guard —
 * re-invoking the original is a no-op after the core already ran.
 *
 * Vanilla JS internally (no jQuery/cash dependency); DOM-ready boot so
 * deferred component scripts (catalog-filter.js, basket-mini.js, ...)
 * have defined their namespaces first.
 */
(function(win, doc) {
	'use strict';

	var d = doc;
	var $ = function(sel, ctx) { return (ctx || d).querySelector(sel); };

	/* ------------------------------------------------------------------
	 * Custom header behaviour
	 * ------------------------------------------------------------------ */
	var DuainsHeader = {

		once: {},

		/**
		 * Wraps each custom routine in a once-guard so it can be invoked
		 * from a decorator AND from the direct boot() without duplicating
		 * event listeners.
		 */
		run: function(name, fn) {
			if (this.once[name]) { return; }
			this.once[name] = true;
			fn.call(this);
		},

		/**
		 * Scroll state — adds .is-scrolled to the fixed navbar and the
		 * announcement bar so the glass elevation / topbar hide transitions
		 * in aimeos.css can fire. Mirrors AimeosPage.onMenuScroll() (which
		 * adds .scroll to the navbar).
		 */
		applyScrollState: function() {
			this.run('scroll', function() {
				var navbar = $('.navbar');
				var announce = $('.du-announce');
				if (!navbar && !announce) { return; }
				var ticking = false;

				function update() {
					ticking = false;
					var y = win.scrollY || d.documentElement.scrollTop;
					if (navbar) { navbar.classList.toggle('is-scrolled', y > 8); }
					if (announce) { announce.classList.toggle('is-scrolled', y > 8); }
				}

				win.addEventListener('scroll', function() {
					if (!ticking) {
						ticking = true;
						win.requestAnimationFrame(update);
					}
				}, { passive: true });

				update();
			});
		},

		/**
		 * Search — Cmd/Ctrl+K focuses the header search input; the
		 * focus ring / pill expansion is pure CSS (:focus-within).
		 */
		applySearch: function() {
			this.run('search', function() {
				var input = $('.navbar .catalog-filter-search input.form-control.value');
				if (!input) { return; }

				d.addEventListener('keydown', function(e) {
					var k = String.fromCharCode(e.which || e.keyCode).toUpperCase();
					if ((e.metaKey || e.ctrlKey) && k === 'K') {
						e.preventDefault();
						input.focus();
					}
				});
			});
		},

		/**
		 * Mobile drawer — the navbar hamburger opens the category
		 * off-canvas drawer directly (no intermediate panel). Proxies the
		 * core .menu click so onShowCategories keeps full control.
		 */
		applyMobileDrawer: function() {
			this.run('mobileDrawer', function() {
				var toggler = $('.navbar-toggler');
				var menu = $('.catalog-filter-tree > a.menu');
				if (!toggler || !menu) { return; }

				/* Keep the core anchor from jumping to "#" in both flows */
				menu.addEventListener('click', function(e) { e.preventDefault(); });

				toggler.addEventListener('click', function(e) {
					e.preventDefault();
					menu.click();
				});
			});
		},

		/**
		 * Drawers (basket + category) — lock body scroll while either is
		 * open, close via ESC, and sync the toggler's aria-expanded (drives
		 * the hamburger -> X animation). Watches the .opened class set by
		 * the Aimeos core so open/close logic stays fully core-owned.
		 */
		applyDrawers: function() {
			this.run('drawers', function() {
				var basket = $('.basket-mini .zeynep');
				var tree = $('.catalog-filter-tree .zeynep');
				var toggler = $('.navbar-toggler');
				if (!basket && !tree || typeof MutationObserver === 'undefined') { return; }

				var sync = function() {
					var open = (basket && basket.classList.contains('opened'))
						|| (tree && tree.classList.contains('opened'));
					d.body.classList.toggle('du-drawer-open', open);
					if (toggler) {
						toggler.setAttribute('aria-expanded', open ? 'true' : 'false');
					}
				};

				[basket, tree].forEach(function(drawer) {
					if (!drawer) { return; }
					new MutationObserver(sync).observe(drawer, {
						attributes: true,
						attributeFilter: ['class']
					});
				});

				d.addEventListener('keydown', function(e) {
					var open = (basket && basket.classList.contains('opened'))
						|| (tree && tree.classList.contains('opened'));
					if (e.key === 'Escape' && open) {
						var overlay = $('.catalog-filter-tree .aimeos-overlay-offscreen')
							|| $('.basket-mini .aimeos-overlay-offscreen');
						if (overlay) { overlay.click(); } /* core closes all drawers */
					}
				});

				sync();
			});
		},

		/**
		 * Cart badge — pop animation when the quantity changes
		 * (core updates the .quantity text via basket-mini.js).
		 */
		applyBadgePop: function() {
			this.run('badgePop', function() {
				var badge = $('.basket-mini-main .quantity');
				if (!badge || typeof MutationObserver === 'undefined') { return; }

				new MutationObserver(function() {
					badge.classList.remove('pop');
					void badge.offsetWidth;
					badge.classList.add('pop');
				}).observe(badge, { childList: true, characterData: true, subtree: true });
			});
		},

		/**
		 * Runs every custom routine once.
		 */
		init: function() {
			this.applyScrollState();
			this.applySearch();
			this.applyMobileDrawer();
			this.applyDrawers();
			this.applyBadgePop();
		}
	};

	/* ------------------------------------------------------------------
	 * Decorator pattern — wrap existing Aimeos init methods without
	 * replacing the core logic. originalInit() is called first (a no-op
	 * after the core already ran, thanks to the `once` guards), then the
	 * matching custom behaviour is attached.
	 * ------------------------------------------------------------------ */
	function decorate(name, feature) {
		var obj = win[name];
		if (!obj || typeof obj.init !== 'function') { return; }

		var originalInit = obj.init.bind(obj);

		obj.init = function() {
			originalInit();
			feature.call(DuainsHeader);
		};
	}

	function boot() {
		/* Scroll state + hamburger->drawer ride on the core navbar handler */
		decorate('AimeosPage', function() {
			DuainsHeader.applyScrollState();
			DuainsHeader.applyMobileDrawer();
		});

		/* Search focus rides on the filter/search handler */
		decorate('AimeosCatalogFilter', DuainsHeader.applySearch);

		/* Drawer lock and badge pop ride on the basket handler */
		decorate('AimeosBasketMini', function() {
			DuainsHeader.applyDrawers();
			DuainsHeader.applyBadgePop();
		});

		/* Pattern completeness — nothing extra needed for locale switching */
		decorate('AimeosLocaleSelect', function() {});

		/* Direct boot: the wrapped inits already ran before this file
		 * executed, so their decorators would never fire. Running the
		 * routines here guarantees the behaviour on every page. */
		DuainsHeader.init();
	}

	if (d.readyState === 'loading') {
		d.addEventListener('DOMContentLoaded', boot);
	} else {
		boot();
	}
})(window, document);