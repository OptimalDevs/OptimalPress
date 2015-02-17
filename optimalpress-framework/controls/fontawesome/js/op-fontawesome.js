/**
 * On Document Ready
 * ----------------------------------------------------------------------------
 */
jQuery( window ).load(function() {
	
	var opFontawesomeActiveControl;
	
	jQuery( document ).on( 'click', '.op-show-fontawesome-modalbox', function(e) {
		
		e.preventDefault();
		console.log("abirr modal");
		opFontawesomeActiveControl = this;
		
		jQuery( '#op-fontawesome-overlay' ).show().fadeTo( 200, 1 );
		jQuery( '#op-fontawesome-searcher' ).val("");
		jQuery( '#op-fontawesome-searcher' ).trigger('input');
				
	
		
	});
	
	jQuery( document ).on( 'click', '.op-fontawesome-list .fa', function(e) {
		
		jQuery( opFontawesomeActiveControl ).siblings( '.op-fontawesome-preview' ).removeClass( jQuery( opFontawesomeActiveControl ).siblings( '.op-fontawesome-selected-icon' ).val() );
		jQuery( opFontawesomeActiveControl ).siblings( '.op-fontawesome-selected-icon' ).val( jQuery( this ).data( 'class-name' ) );
		jQuery( opFontawesomeActiveControl ).siblings( '.op-fontawesome-preview' ).addClass( jQuery( this ).data( 'class-name' ) );
		jQuery( opFontawesomeActiveControl ).siblings( '.op-fontawesome-selected-icon' ).trigger('change');
		
		jQuery( '#op-fontawesome-overlay' ).fadeTo( 200, 0, function() { 
			jQuery( '#op-fontawesome-overlay' ).hide();
		} );
		
	});
	
	jQuery( document ).on( 'click', '.op-remove-fontawesome', function(e) {

		e.preventDefault();

		jQuery( this ).siblings( '.op-fontawesome-preview' ).removeClass( jQuery( this ).siblings( '.op-fontawesome-selected-icon' ).val() );
		jQuery( this ).siblings( '.op-fontawesome-selected-icon' ).val( '' );
		jQuery( this ).siblings( '.op-fontawesome-selected-icon' ).trigger('change');
		
	});

	jQuery('#op-fontawesome-overlay').mousedown(function (e) {

	    if ( ! jQuery(".op-fontawesome-container").is(e.target) && jQuery(".op-fontawesome-container").has(e.target).length === 0 ) {
			
			jQuery( '#op-fontawesome-overlay' ).fadeTo( 200, 0, function() {

				jQuery( '#op-fontawesome-overlay' ).hide();

			});

	    }
	});

	jQuery( '#op-fontawesome-searcher' ).bind( 'input keyup', function(e) {
		
		var typed = this.value;

		jQuery(this).siblings().children('i').each(function () {

		   if ( jQuery(this).data('class-name').substring(3).indexOf( typed ) == -1 ) {

		   		jQuery(this).hide();

		   }
		   else{

		   		jQuery(this).show();

		   }

		});
		
	});
	
	jQuery( document ).keydown( function( e ) {
 
		 if ( e.keyCode == 27 ) {
		 
			jQuery( '#op-fontawesome-overlay' ).fadeTo( 200, 0, function() { 
				jQuery( '#op-fontawesome-overlay' ).hide();
			} );				
			 
		 }
		 
	 });
	
});