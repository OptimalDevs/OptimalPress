
/**
 * Options page javascript
 *
 * This script manage the backend theme options.
 * Switch between tabs and save settings by Ajax.
 */
jQuery( window ).load( function() {
	
	//Call function that handles dependencies.
	if( optimalpressData.dependency ){
		
		opInitDependency( optimalpressData.dependency );
		
	}
	
	/*
	 * Menu set tabs when pages reload.
	 */	
	jQuery( '.op-panel' ).removeClass( 'op-current' );
	jQuery( '.op-menus li' ).removeClass( 'op-current' );
	
	if (  window.location.hash ) {
	
		jQuery( '#op-' +  window.location.hash.replace( '#', '' ) ).addClass( 'op-current' );
		jQuery( 'a[href="' +  window.location.hash + '"]' ).parent().addClass( 'op-current' );

	}else{
	
		jQuery( '.op-panel' ).first().addClass( 'op-current' );
		jQuery( '.op-menu-level-1 li' ).first().addClass( 'op-current' );
		
	}
	
	/*
	 * Menu change tabs.
	 */
	jQuery( '.op-menu-goto' ).on( 'click', function( e ) {

		jQuery( '.op-menus li' ).removeClass( 'op-current' );
		jQuery( '.op-panel' ).removeClass( 'op-current' );
		jQuery( '#op-' + jQuery( this ).attr( 'href' ).replace( '#', '' ) ).addClass( 'op-current' );
		jQuery( this ).parent().addClass( 'op-current' );
		
	});
	
	/*
	 * Ajax: Save Options.
	 */
	jQuery( '.op-save' ).on( 'click', function( e ) {
	
		e.preventDefault();
		
		jQuery.ajax({
			
				url:		optimalpressData.ajaxurl,
				data:		'nonce=' + optimalpressData.nonce + '&action=optimalpress_save_options_page_hook&' + jQuery( '#op-option-form' ).serialize(),
				type:		'POST',
				dataType:	'json',

			})
			.done ( function( data ) {

				console.log( data.message );
				location.reload();//Reload after Ajax?
				
			} )		
			.fail( function( jqXHR, textStatus, errorThrown ) {

				console.log( 'Request failed: ' + textStatus );
				console.log( 'Error Thrown: ' + errorThrown );
			
			});
		
	});
		
});

