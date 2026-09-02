/*
 * Duains: editable HTML/CSS code modal for the Aimeos CMS GrapesJS editor
 * (admin/jqadm/js/custom-grapesjs.js).
 *
 * The </> button runs the custom "duains:open-code" command: a modal with
 * editable HTML and CSS tabs (CodeMirror) whose "Update layout" button
 * applies the code via setComponents()/setStyle() and syncs the Aimeos
 * form.
 *
 * Injected into the admin JS bundle ("index-js") via manifest.jsb2, so it
 * is served same-origin and complies with the backend CSP.
 *
 * The whole admin bundle is evaluated before DOMContentLoaded, but the
 * concatenation order relative to ai-cms-grapesjs custom.js is not
 * guaranteed, so we patch immediately when possible and fall back to a
 * DOMContentLoaded listener (registered before Aimeos.CMSContent.init()
 * mounts the Vue app) otherwise.
 */
( function() {
	'use strict';

	var patched = false;

	// Editable HTML + CSS modal replacing the read-only export view
	function openCodeModal( editor ) {
		var modal = editor.Modal;
		var opts = { theme: 'hopscotch', readOnly: 0, height: '450px' };
		var htmlVw = editor.CodeManager.createViewer( Object.assign( {
			type: 'CodeMirror', codeName: 'htmlmixed'
		}, opts ) );
		var cssVw = editor.CodeManager.createViewer( Object.assign( {
			type: 'CodeMirror', codeName: 'css'
		}, opts ) );

		htmlVw.getEditor().setValue( editor.getHtml() );
		cssVw.getEditor().setValue( editor.getCss( { avoidProtected: true } ) );

		var box = document.createElement( 'div' );
		box.style.width = '100%';

		var tabs = document.createElement( 'div' );
		tabs.style.marginBottom = '0.5rem';

		var htmlTab = codeBtn( 'HTML' );
		var cssTab = codeBtn( 'CSS' );
		tabs.appendChild( htmlTab );
		tabs.appendChild( cssTab );

		var htmlBox = htmlVw.getEditor().getWrapperElement();
		var cssBox = cssVw.getEditor().getWrapperElement();
		box.appendChild( tabs );
		box.appendChild( htmlBox );
		box.appendChild( cssBox );

		function codeBtn( label ) {
			var b = document.createElement( 'button' );
			b.type = 'button';
			b.innerHTML = label;
			b.style.cssText = 'margin-right:0.5rem;padding:0.25rem 0.75rem;cursor:pointer';
			return b;
		}

		function showCode( html ) {
			htmlBox.style.display = html ? 'block' : 'none';
			cssBox.style.display = html ? 'none' : 'block';
			( html ? htmlVw : cssVw ).refresh();
		}

		htmlTab.onclick = function() {
			showCode( true );
		};
		cssTab.onclick = function() {
			showCode( false );
		};

		var btns = document.createElement( 'div' );
		btns.style.marginTop = '0.5rem';
		btns.style.textAlign = 'right';

		var upd = codeBtn( 'Update layout' );
		upd.style.cssText += ';background:#3ca8dd;color:#fff;border:none';
		upd.onclick = function() {
			editor.setComponents( htmlVw.getEditor().getValue().trim() );
			editor.setStyle( cssVw.getEditor().getValue() );
			modal.close();
			sync();
		};

		var cls = codeBtn( 'Close' );
		cls.onclick = function() {
			modal.close();
		};

		btns.appendChild( cls );
		btns.appendChild( upd );
		box.appendChild( btns );

		modal.setTitle( 'Edit HTML and CSS code' );
		modal.setContent( box );
		modal.open();
		showCode( true );
		modal.getModel().once( 'change:open', function() {
			editor.stopCommand( 'duains:open-code' );
		} );
	}

	function patch( setup ) {
		if( patched || !setup || !setup.config || !setup.initialize ) {
			return;
		}
		patched = true;

		// setup.initialize() resets the panels *after* the plugins have run
		// (Aimeos.Panels.getPanels().reset(setup.panels)), so the toolbar
		// buttons must be part of setup.panels itself
		setup.panels.push( {
			id: 'impexp',
			buttons: [ {
				id: 'duains-open-code',
				className: 'fa fa-code',
				attributes: { title: 'Edit HTML/CSS code' },
				command: function( e ) {
					// Registered again here (harmless overwrite) instead of
					// relying on the registration below: every CMS content
					// item (one per language) mounts its own editor instance
					// simultaneously off the same shared setup.panels array,
					// so this button isn't guaranteed to be clicked on the
					// exact instance whose own initialize() ran first
					e.Commands.add( 'duains:open-code', { run: openCodeModal, stop: function() {} } );
					e.runCommand( 'duains:open-code' );
				}
			}, {
				id: 'canvas-clear',
				className: 'fa fa-trash',
				attributes: { title: 'Clear all elements from the page' },
				command: function( e ) {
					if( confirm( 'Remove all elements from the page?' ) ) {
						e.runCommand( 'core:canvas-clear' );
						sync();
					}
				}
			} ]
		} );

		var initialize = setup.initialize;
		setup.initialize = function( editor, setup, media ) {
			initialize.call( this, editor, setup, media );

			// The PHP-provided config (canvas styles) is shallow merged over
			// setup.config by the Vue component, so extra editor CSS has to
			// be injected after initialization. Appended directly into the
			// canvas iframe's document (not via the Css/style-composer API)
			// so it stays a display-only aid and never leaks into getCss()/
			// the saved page content.
			var canvasDoc = editor.Canvas.getDocument();
			if( canvasDoc ) {
				var canvasStyle = canvasDoc.createElement( 'style' );
				canvasStyle.textContent = '.container-fluid { max-width: 1320px; margin: 0 auto; }';
				canvasDoc.head.appendChild( canvasStyle );
			}

			// Direct canvas edits (drag/drop, inline text editing) aren't
			// covered by the .btn mousedown binding that bumps the Vue
			// "update" counter, so queue a form sync on canvas changes
			var t = null;

			function queueSync() {
				clearTimeout( t );
				t = setTimeout( sync, 0 );
			}

			editor.on( 'component:create', queueSync );
			editor.on( 'component:remove', queueSync );

			// Eager registration for the common case; the button itself also
			// registers defensively before running (see setup.panels above)
			editor.Commands.add( 'duains:open-code', { run: openCodeModal, stop: function() {} } );
		};
	}

	function sync() {
		if( window.Aimeos && Aimeos.apps && Aimeos.apps['cms-content'] ) {
			Aimeos.apps['cms-content'].change();
		}
	}

	if( window.Aimeos && Aimeos.CMSContent ) {
		patch( Aimeos.CMSContent.GrapesJS );
	} else {
		document.addEventListener( 'DOMContentLoaded', function() {
			patch( window.Aimeos && Aimeos.CMSContent && Aimeos.CMSContent.GrapesJS );
		} );
	}
} )();
