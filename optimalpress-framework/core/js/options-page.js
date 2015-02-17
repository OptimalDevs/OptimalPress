
/**
 * Options page javascript
 *
 * Este script se encarga de la parte de administración de las opciones del theme
 * Cambiar entre tabs y guardar las opciones por Ajax.
 */

jQuery( window ).load(function() {
	
	//LLama a la función que se encarga de las dependencias
	
	if( optimalpressData.dependency ){
		opInitDependency( optimalpressData.dependency );
	}		
	/*
	*	Menu set tabs when pages reload
	*
	*/	
	jQuery( '.op-panel' ).removeClass( 'op-current' );
	jQuery( '.op-menus li' ).removeClass( 'op-current' );
	
	if (  window.location.hash ) {
	
		jQuery( '#' +  window.location.hash.replace( '#', '' ) ).addClass( 'op-current' );
		jQuery( 'a[href="' +  window.location.hash + '"]' ).parent().addClass( 'op-current' );

	}else{
	
		jQuery( '.op-panel' ).first().addClass( 'op-current' );
		jQuery( '.op-menu-level-1 li' ).first().addClass( 'op-current' );
		
	}
	
	/*
	*	Menu change tabs
	*
	*/
	jQuery( '.op-menu-goto' ).on( 'click', function( e ) {

		e.preventDefault(); //si hago esto no cambia el anchor pero ya no se produce el salto
		
		jQuery( '.op-menus li' ).removeClass( 'op-current' );
		jQuery( '.op-panel' ).removeClass( 'op-current' );
		jQuery( '#' + jQuery( this ).attr( 'href' ).replace( '#', '' ) ).addClass( 'op-current' );
		jQuery( this ).parent().addClass( 'op-current' );
		//jQuery( 'html,body' ).delay(10).animate({scrollTop: 0}, 800);
		
	});
	
	/*
	*	Ajax: Save Options
	*
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
				location.reload();//Recargar despues de ajax??
				
			} )		
			.fail( function( jqXHR, textStatus, errorThrown ) {

				console.log( "Request failed: " + textStatus );
				console.log( "Error Thrown: " + errorThrown );
			
			});
		
	});
		
});
