"use strict";

jQuery( window ).load( function() {
								
	jQuery( document ).on( 'click', '.op-control-radioimage .op-image-item', function(){
		
		jQuery(this).parents( '.input' ).find( 'input' ).removeClass( 'checked' );
		jQuery(this).prev( 'input ').addClass( 'checked' );	
			
	});
	
	jQuery( '.op-control-radioimage .op-image-item' ).tipsy();
	
	jQuery( 'body' ).on( 'op-addnew-group-element', function () {

		jQuery( '.op-control-radioimage .op-image-item' ).tipsy();

	});
	
});