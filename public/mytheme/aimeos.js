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
		 * Basket drawer — lock body scroll while open, close via ESC.
		 * Watches the .opened class set by the Aimeos core so the core's
		 * open/close logic keeps full control.
		 */
		applyBasketDrawer: function() {
			this.run('basketDrawer', function() {
				var drawer = $('.basket-mini .zeynep');
				if (!drawer || typeof MutationObserver === 'undefined') { return; }

				var sync = function() {
					d.body.classList.toggle('du-drawer-open', drawer.classList.contains('opened'));
				};

				new MutationObserver(sync).observe(drawer, {
					attributes: true,
					attributeFilter: ['class']
				});

				d.addEventListener('keydown', function(e) {
					if (e.key === 'Escape' && drawer.classList.contains('opened')) {
						var overlay = $('.basket-mini .aimeos-overlay-offscreen');
						if (overlay) { overlay.click(); }
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
			this.applyBasketDrawer();
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
		/* Scroll state rides on the core navbar handler */
		decorate('AimeosPage', DuainsHeader.applyScrollState);

		/* Search focus rides on the filter/search handler */
		decorate('AimeosCatalogFilter', DuainsHeader.applySearch);

		/* Drawer + badge polish ride on the mini basket handler */
		decorate('AimeosBasketMini', function() {
			DuainsHeader.applyBasketDrawer();
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