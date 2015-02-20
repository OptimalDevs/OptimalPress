/**
 * Shortcodes Generator Javascript.
 *
 * This script manage the backend shortcode generator:
 * Manage the modalbox.
 * Switch between tabs.
 * Opening and closing headers.
 * Insert Shortcode in WordPress editor
 */
jQuery( document ).ready( function($) {

	opInitDependency( op_sg );	
	
	//Set Overlay background for modal
	$( '<div class="op-modal-overlay" />' ).insertAfter( '#wpfooter' );	
	
	//Close all headings	
	$( '.op-modal .op-element' ).addClass( 'closed' );
	
	/*
	 * Menu change tabs.
	 */	
	$( '.op-modal .op-menu a' ).on( 'click', function( e ) {
	
		$( this ).parents( '.op-menu' ).siblings( '.op-main' ).find( '.op-sub-menu-list' ).removeClass( 'op-current' );
		$( this ).parents( '.op-menu' ).siblings( '.op-main' ).find( '.op-sub-menu-' + $( this ).attr( 'href' ).replace( '#', '' ) ).addClass( 'op-current' );
		
	});
	
	/*
	 * Menu change heading: open and close.
	 */	
	$( '.op-modal .op-element-heading' ).on( 'click', function(e) {
		
		var beforeClosed = $( this ).parents( '.op-element' ).hasClass( 'closed' );
		
		$( this ).parents( '.op-modal' ).find( '.op-element' ).addClass( 'closed' );
		
		if ( beforeClosed ){
			$( this ).parents( '.op-element' ).addClass( 'closed' );
		}
		else{
			$( this ).parents( '.op-element' ).removeClass( 'closed' );
		}
		
	});
	
	/*
	 * Insert Shortcodes in Editor.
	 */	
	$( '.op-modal .op-insert' ).bind( 'click.op_sc_insert', function(e){
	
		e.preventDefault();
		var $modal  = $( this ).parents( '.op-modal' ),
			$parent = $( this ).parents( '.op-element' ),
			$form   = $( this ).parents( '.op-element-form' ),
			$fields = $form.find( '.op-field:not(".not-sc")' ),
			values  = {},
			code    = $parent.find( '.op-code' ).first().html(),
			atts    = '';

		$fields.each(function(i){
		
			var $input = $( this ).find( ':not(div).op-input' ),
				name   = $input.attr( 'name' ),
				type   = $( this ).attr( 'data-op-type' );
				
			var fieldValues 	= '';
		
			switch( $input.attr( 'type' ) ){
			
				case 'checkbox':
				case 'radio':
					
					fieldValues = $input.is( ':checked' );

					if( $input.length > 1 ) {	

						fieldValues = [];	
						
						$input.each( function(){
						
							if( jQuery( this ).is( ':checked' ) ){
							
								fieldValues.push( jQuery( this ).val() );
								
							}
							
						});
						
					}

					break;
					
				default:
					
					fieldValues = $input.val();
		
					fieldValues = ! fieldValues ? '' : fieldValues;

					break;
			}
	
			values[name] = fieldValues
	
		});

		for( var name in values ) {
		
			if( values.hasOwnProperty(name) ) {
			
				atts += ( " " + name.replace( '[]', '' ).replace( '[url]', '' ) + '="' + values[name] + '"' );
			}
			
		}

		// print shortcode to editor					
		code = code.replace( ']', atts + ']' );
		code = decodeEntities( code );
		$modal.trigger( 'op_insert_shortcode', code );

		// reset form and close dialog
		$( '.op-element' ).removeClass( 'active' );
		$modal.css( { visibility: 'hidden' } );
		$( '.op-modal-overlay' ).hide();
		$( this ).closest( 'form' ).find( 'input[type=text], textarea' ).val( '' );
		
	});
	
	if( ! jQuery.fn.insertAtCaret ) {
		jQuery.fn.insertAtCaret = function(text) {
			return this.each(function() {
				if( document.selection && this.tagName == 'TEXTAREA' ) {
					//IE textarea support
					this.focus();
					sel = document.selection.createRange();
					sel.text = text;
					this.focus();
				} else if( this.selectionStart || this.selectionStart == '0' ) {
					//MOZILLA/NETSCAPE support
					startPos = this.selectionStart;
					endPos = this.selectionEnd;
					scrollTop = this.scrollTop;
					this.value = this.value.substring(0, startPos) + text + this.value.substring(endPos, this.value.length);
					this.focus();
					this.selectionStart = startPos + text.length;
					this.selectionEnd = startPos + text.length;
					this.scrollTop = scrollTop;
				} else {
					// IE input[type=text] and other browsers
					this.value += text;
					this.focus();
					this.value = this.value;
				}
			});
		};
	}

	var decodeEntities = (function() {
	
		// this prevents any overhead from creating the object each time
		var element = document.createElement( 'div' );

		function decodeHTMLEntities (str) {
			if( str && typeof str === 'string' ) {
				// strip script/html tags
				str = str.replace(/<script[^>]*>([\S\s]*?)<\/script>/gmi, '');
				str = str.replace(/<\/?\w(?:[^"'>]|"[^"]*"|'[^']*')*>/gmi, '');
				element.innerHTML = str;
				str = element.textContent;
				element.ftextContent = '';
			}

			return str;
		}

		return decodeHTMLEntities;
		
	})();
	
	/*
	 * Close Modal by clicking outside.
	 */
	$( '.op-modal-overlay' ).click(function(){
		
		var $modal  = $( '.op-modal' );

		$modal.css( { visibility: 'hidden' } );
		$( '.op-modal-overlay' ).hide();
		$modal.find( 'input[type=text], textarea' ).val( '' );
	
	});
	
	/*
	 * Close Modal by clicking "x" Button or cancel button.
	 */
	$( '.op-close-modal, .op-cancel' ).click(function(e){
		
		e.preventDefault();
		
		var $modal  = $(this).parents( '.op-modal' );
		$modal.css( { visibility: 'hidden' } );
		$( '.op-modal-overlay' ).hide();
		$(this).parents( '.op-scroll-container' ).find( 'input[type=text], textarea' ).val('');

	});
	
});




