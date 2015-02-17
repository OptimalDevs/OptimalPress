/* global ajaxurl, wpLinkL10n, setUserSetting, */
"use strict";

var opwpLink;

( function( $ ) {
	var inputs = {}, rivers = {}, editor, River, Query;

	opwpLink = {
		timeToTriggerRiver: 150,
		minRiverAJAXDuration: 200,
		riverBottomThreshold: 5,
		keySensitivity: 100,
		lastSearch: '',

		init: function() {
			inputs.wrap = $('#op-wp-link-wrap');
			inputs.dialog = $( '#op-wp-link' );
			inputs.backdrop = $( '#op-wp-link-backdrop' );
			inputs.submit = $( '#op-wp-link-submit' );
			inputs.close = $( '#op-wp-link-close' );
			// URL
			inputs.url = $( '#op-url-field' );
			inputs.nonce = $( '#_ajax_linking_nonce' );
			// Secondary options
			inputs.title = $( '#op-link-title-field' );
			// Advanced Options
			inputs.openInNewTab = $( '#op-link-target-checkbox' );
			inputs.search = $( '#op-search-field' );
			// Build Rivers
			rivers.search = new River( $( '#op-search-results' ) );
			rivers.recent = new River( $( '#op-most-recent-results' ) );
			rivers.elements = inputs.dialog.find( '.query-results' );

			// Bind event handlers
			inputs.dialog.keydown( opwpLink.keydown );
			inputs.dialog.keyup( opwpLink.keyup );
			inputs.submit.click( function( event ) {
				event.preventDefault();
			});
			inputs.close.add( inputs.backdrop ).add( '#op-wp-link-cancel a' ).click( function( event ) {	
				event.preventDefault();
				opwpLink.close();
			});
			
			$( '#op-wp-link-search-toggle' ).click( opwpLink.toggleInternalLinking );

			rivers.elements.on( 'river-select', opwpLink.updateFields );

			inputs.search.keyup( opwpLink.searchInternalLinks );
		},

		open: function( editorId ) {
			
			inputs.wrap.show();
			inputs.backdrop.show();

			opwpLink.refresh();
		},

		refresh: function() {
			// Refresh rivers (clear links, check visibility)
			rivers.search.refresh();
			rivers.recent.refresh();

			opwpLink.setDefaultValues();

			// Focus the URL field and highlight its contents.
			//     If this is moved above the selection changes,
			//     IE will show a flashing cursor over the dialog.
			inputs.url.focus()[0].select();
			// Load the most recent results if this is the first time opening the panel.
			if ( ! rivers.recent.ul.children().length )
				rivers.recent.ajax();
		},

		close: function() {
			
			inputs.backdrop.hide();
			inputs.wrap.hide();
		},

		getAttrs: function() {
			return {
				href: inputs.url.val(),
				title: inputs.title.val(),
				target: inputs.openInNewTab.prop( 'checked' ) ? '_blank' : ''
			};
		},

		updateFields: function( e, li, originalEvent ) {
			inputs.url.val( li.children( '.item-permalink' ).val() );
			inputs.title.val( li.hasClass( 'no-title' ) ? '' : li.children( '.item-title' ).text() );
			if ( originalEvent && originalEvent.type == 'click' )
				inputs.url.focus();
		},

		setDefaultValues: function() {
			// Set URL and description to defaults.
			// Leave the new tab setting as-is.
			inputs.url.val( 'http://' );
			inputs.title.val( '' );

			// Update save prompt.
			inputs.submit.val( wpLinkL10n.save );
		},

		searchInternalLinks: function() {
			var t = $( this ), waiting,
				search = t.val();

			if ( search.length > 2 ) {
				rivers.recent.hide();
				rivers.search.show();

				// Don't search if the keypress didn't change the title.
				if ( opwpLink.lastSearch == search )
					return;

				opwpLink.lastSearch = search;
				waiting = $( '#river-waiting' ).show();

				rivers.search.change( search );
				rivers.search.ajax( function() {
					waiting.hide();
				});
			} else {
				rivers.search.hide();
				rivers.recent.show();
			}
		},

		next: function() {
			rivers.search.next();
			rivers.recent.next();
		},

		prev: function() {
			rivers.search.prev();
			rivers.recent.prev();
		},

		keydown: function( event ) {
			var fn, id,
				key = $.ui.keyCode;

			if ( key.ESCAPE === event.keyCode ) {
				opwpLink.close();
				event.stopImmediatePropagation();
			} else if ( key.TAB === event.keyCode ) {
				id = event.target.id;

				if ( id === 'op-wp-link-submit' && ! event.shiftKey ) {
					inputs.close.focus();
					event.preventDefault();
				} else if ( id === 'op-wp-link-close' && event.shiftKey ) {
					inputs.submit.focus();
					event.preventDefault();
				}
			}

			if ( event.keyCode !== key.UP && event.keyCode !== key.DOWN ) {
				return;
			}

			fn = event.keyCode === key.UP ? 'prev' : 'next';
			clearInterval( opwpLink.keyInterval );
			opwpLink[ fn ]();
			opwpLink.keyInterval = setInterval( opwpLink[ fn ], opwpLink.keySensitivity );
			event.preventDefault();
		},

		keyup: function( event ) {
			var key = $.ui.keyCode;

			if ( event.which === key.UP || event.which === key.DOWN ) {
				clearInterval( opwpLink.keyInterval );
				event.preventDefault();
			}
		},

		delayedCallback: function( func, delay ) {
			var timeoutTriggered, funcTriggered, funcArgs, funcContext;

			if ( ! delay )
				return func;

			setTimeout( function() {
				if ( funcTriggered )
					return func.apply( funcContext, funcArgs );
				// Otherwise, wait.
				timeoutTriggered = true;
			}, delay );

			return function() {
				if ( timeoutTriggered )
					return func.apply( this, arguments );
				// Otherwise, wait.
				funcArgs = arguments;
				funcContext = this;
				funcTriggered = true;
			};
		},

		toggleInternalLinking: function() {
			var visible = inputs.wrap.hasClass( 'search-panel-visible' );

			inputs.wrap.toggleClass( 'search-panel-visible', ! visible );
			setUserSetting( 'opwpLink', visible ? '0' : '1' );
			inputs[ ! visible ? 'search' : 'url' ].focus();
		}
	};

	River = function( element, search ) {
		var self = this;
		this.element = element;
		this.ul = element.children( 'ul' );
		this.contentHeight = element.children( '#link-selector-height' );
		this.waiting = $( '#river-waiting' );

		this.change( search );
		this.refresh();

		$( '#op-wp-link .query-results, #op-wp-link #op-link-selector' ).scroll( function() {
			self.maybeLoad();
		});
		element.on( 'click', 'li', function( event ) {
			self.select( $( this ), event );
		});
	};

	$.extend( River.prototype, {
		refresh: function() {
			this.deselect();
			this.visible = this.element.is( ':visible' );
		},
		show: function() {
			if ( ! this.visible ) {
				this.deselect();
				this.element.show();
				this.visible = true;
			}
		},
		hide: function() {
			this.element.hide();
			this.visible = false;
		},
		// Selects a list item and triggers the river-select event.
		select: function( li, event ) {
			var liHeight, elHeight, liTop, elTop;

			if ( li.hasClass( 'unselectable' ) || li == this.selected )
				return;

			this.deselect();
			this.selected = li.addClass( 'selected' );
			// Make sure the element is visible
			liHeight = li.outerHeight();
			elHeight = this.element.height();
			liTop = li.position().top;
			elTop = this.element.scrollTop();

			if ( liTop < 0 ) // Make first visible element
				this.element.scrollTop( elTop + liTop );
			else if ( liTop + liHeight > elHeight ) // Make last visible element
				this.element.scrollTop( elTop + liTop - elHeight + liHeight );

			// Trigger the river-select event
			this.element.trigger( 'river-select', [ li, event, this ] );
		},
		deselect: function() {
			if ( this.selected )
				this.selected.removeClass( 'selected' );
			this.selected = false;
		},
		prev: function() {
			if ( ! this.visible )
				return;

			var to;
			if ( this.selected ) {
				to = this.selected.prev( 'li' );
				if ( to.length )
					this.select( to );
			}
		},
		next: function() {
			if ( ! this.visible )
				return;

			var to = this.selected ? this.selected.next( 'li' ) : $( 'li:not(.unselectable):first', this.element );
			if ( to.length )
				this.select( to );
		},
		ajax: function( callback ) {
			var self = this,
				delay = this.query.page == 1 ? 0 : opwpLink.minRiverAJAXDuration,
				response = opwpLink.delayedCallback( function( results, params ) {
					self.process( results, params );
					if ( callback )
						callback( results, params );
				}, delay );

			this.query.ajax( response );
		},
		change: function( search ) {
			if ( this.query && this._search == search )
				return;

			this._search = search;
			this.query = new Query( search );
			this.element.scrollTop( 0 );
		},
		process: function( results, params ) {
			var list = '', alt = true, classes = '',
				firstPage = params.page == 1;

			if ( ! results ) {
				if ( firstPage ) {
					list += '<li class="unselectable"><span class="item-title"><em>' +
						wpLinkL10n.noMatchesFound + '</em></span></li>';
				}
			} else {
				$.each( results, function() {
					classes = alt ? 'alternate' : '';
					classes += this.title ? '' : ' no-title';
					list += classes ? '<li class="' + classes + '">' : '<li>';
					list += '<input type="hidden" class="item-permalink" value="' + this.permalink + '" />';
					list += '<span class="item-title">';
					list += this.title ? this.title : wpLinkL10n.noTitle;
					list += '</span><span class="item-info">' + this.info + '</span></li>';
					alt = ! alt;
				});
			}

			this.ul[ firstPage ? 'html' : 'append' ]( list );
		},
		maybeLoad: function() {
			var self = this,
				el = this.element,
				bottom = el.scrollTop() + el.height();

			if ( ! this.query.ready() || bottom < this.contentHeight.height() - opwpLink.riverBottomThreshold )
				return;

			setTimeout(function() {
				var newTop = el.scrollTop(),
					newBottom = newTop + el.height();

				if ( ! self.query.ready() || newBottom < self.contentHeight.height() - opwpLink.riverBottomThreshold )
					return;

				self.waiting.show();
				el.scrollTop( newTop + self.waiting.outerHeight() );

				self.ajax( function() {
					self.waiting.hide();
				});
			}, opwpLink.timeToTriggerRiver );
		}
	});

	Query = function( search ) {
		this.page = 1;
		this.allLoaded = false;
		this.querying = false;
		this.search = search;
	};

	$.extend( Query.prototype, {
		ready: function() {
			return ! ( this.querying || this.allLoaded );
		},
		ajax: function( callback ) {
			var self = this,
				query = {
					action : 'wp-link-ajax',
					page : this.page,
					'_ajax_linking_nonce' : inputs.nonce.val()
				};

			if ( this.search )
				query.search = this.search;

			this.querying = true;

			$.post( ajaxurl, query, function( r ) {
				self.page++;
				self.querying = false;
				self.allLoaded = ! r;
				callback( r, query );
			}, 'json' );
		}
	});

	$( document ).ready( opwpLink.init );
	
})( jQuery );

/**
 * Link Selector for Module Title
 * ----------------------------------------------------------------------------
 */

jQuery( window ).load(function() {
	
	var selectedModuleObject;

	jQuery('body').on( 'click', '.op_open_popup_link_editor_button', function(event) {
		
		event.preventDefault();

		opwpLink.open();

		jQuery( '.ui-button-icon-primary.ui-icon.ui-icon-closethick' ).remove();

		selectedModuleObject = jQuery( this );
		
		jQuery( '#op-url-field' ).val( ( selectedModuleObject.siblings( '.op-url-input' ).val() != '' ) ? selectedModuleObject.siblings( '.op-url-input' ).val() : 'http://' );
		jQuery( '#op-link-title-field' ).val( ( selectedModuleObject.siblings( '.op-title-input' ).val() != '' ) ? selectedModuleObject.siblings( '.op-title-input' ).val() : '' );
		jQuery( '#op-link-target-checkbox' ).prop( 'checked', ( selectedModuleObject.siblings( '.op-target-input' ).val() != '' ) ?  true : false );

	});
	
	jQuery('body').on( 'click', '.op_open_popup_link_editor_button_shortcode', function(event) {
		
		event.preventDefault();

		opwpLink.open();

		jQuery( '.ui-button-icon-primary.ui-icon.ui-icon-closethick' ).remove();

		selectedModuleObject = jQuery( this );
		
	});

	jQuery( '#op-wp-link-submit' ).on( 'click', function(event) {

		var linkAtts = opwpLink.getAttrs();

		if ( linkAtts.href != '' && linkAtts.href != 'http://' ) {

			selectedModuleObject.siblings( '.op-url-input' ).val( linkAtts.href );
			selectedModuleObject.siblings( '.op-url-input' ).trigger('change');
			selectedModuleObject.siblings( '.op-title-input' ).val( linkAtts.title );
			selectedModuleObject.siblings( '.op-target-input' ).val( linkAtts.target );
			selectedModuleObject.siblings( '.op_title_link_container' ).css( 'display', 'table-cell' );
			selectedModuleObject.siblings( '.op_title_link_container' ).find( '.op_module_link_span' ).html( linkAtts.href );
		
			//For Shortcodes Nota: si vas a hacer dependecias en shortcodes tienes que hacer el trigger
			selectedModuleObject.parents( '.op-sc-fields' ).find( 'input[name="_link_url"]' ).val( linkAtts.href );
			selectedModuleObject.parents( '.op-sc-fields' ).find( 'input[name="_link_title"]' ).val( linkAtts.title );
			selectedModuleObject.parents( '.op-sc-fields' ).find( 'input[name="_link_target"]' ).val( linkAtts.target );
			selectedModuleObject.html( op_wplink_data.edit_link_text );
		
		}
		
		opwpLink.close();

	});

	jQuery('body').on( 'click', '.op-remove-link-button', function(event) {

		event.preventDefault();
		var selected = jQuery( this );

		selected.parent().siblings( '.op-url-input' ).val('');
		selected.parent().siblings( '.op-url-input' ).trigger('change');
		selected.parent().siblings( '.op-title-input' ).val('');
		selected.parent().siblings( '.op-target-input' ).val('');
		
		selected.parent().hide();
		
		selected.parent().siblings( '.op_open_popup_link_editor_button' ).html( op_wplink_data.add_link_text  );
		
		//For Shortcode
		selectedModuleObject.parents('.op-sc-fields').find('input[name="_link_url"]').val( '' );
		selectedModuleObject.parents('.op-sc-fields').find('input[name="_link_title"]').val( '' );
		selectedModuleObject.parents('.op-sc-fields').find('input[name="_link_target"]').val( '' );
		

	});

});