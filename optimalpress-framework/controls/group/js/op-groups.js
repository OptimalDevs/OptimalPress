"use strict";

jQuery( window ).load(function() {
	
	/**
	 * Sortable Groups
	 */
	jQuery('.op-loop.op-sortable').sortable({
			items: '>.op-group',
			handle: '.op-group-heading',
			axis: 'y',
			opacity: 0.5,
			tolerance: 'pointer',
			placeholder: 'op-sortable-placeholder',
			distance: 5,
			start: function (event, ui) {
              ui.placeholder.height(ui.item.height()-2);
            }
	});

	/**
	 * Add New Group
	 */
	jQuery( document ).on( 'click', '.op-loop .op-group-add', function( e ) {

		e.preventDefault();
		
		jQuery(this).parents( '.op-loop' ).find( '.op-controls' ).addClass( 'closed' );
		
		var toCopy	= jQuery(this).prev();
		var parent	= jQuery(this).parents( '.op-loop' );
		
		var copied = toCopy.clone().removeClass( 'op-hide last to-copy' ).insertBefore( toCopy );
		copied.find( '.op-controls' ).removeClass('closed');
		var lastkey	=  toCopy.data('lastkey');
		
		copied.html(function(i, oldHTML) {
			return oldHTML.replace(/\optimalpress-lastkey/g, lastkey );
		});
		
		toCopy.data( 'lastkey', ++lastkey );
		
		jQuery( this ).parents( '.op-loop' ).find( '.op-lastkey' ).val(lastkey);
		
		jQuery( this ).trigger( 'op-addnew-group-element' );
		
	} );
	
	/*
	* Delete an element of the group
	*/
	jQuery( document ).on( 'click', '.op-loop .op-group-remove', function( e ) {

		e.preventDefault();
		
		var r = confirm( optimalpressGroupsData.deleteGroupText );
			
		if ( r == true ){
			jQuery( this ).parents( '.op-group' ).remove();
		}
		
	});
	
	jQuery( '.op-group-wrapper .op-controls' ).addClass( 'closed' );
	
	/*
	* Open Close Groups 
	*/
	jQuery( document ).on( 'click', '.op-group-wrapper .op-group-title', function( e ) {
		
		e.preventDefault();

		var beforeClosed = jQuery(this).parents( '.op-group' ).find( '.op-controls' ).hasClass( 'closed' );

		jQuery( this ).parents( '.op-loop' ).find( '.op-controls' ).addClass( 'closed' );
		
		if ( beforeClosed ){
			jQuery( this ).parents( '.op-group' ).find( '.op-controls' ).removeClass( 'closed', 500 );
		}
		else{
			jQuery( this ).parents( '.op-group' ).find( '.op-controls' ).addClass( 'closed' );
		}
		
		
	});
	
	// Change Element List Title
	jQuery( '.op-group-wrapper.op-loop.standard' ).each( function(){
		
		if( jQuery( this ).data( 'dynamic-title' ) ) {
	
			opCreateChangeElementListTitleEvents( jQuery( this ).data( 'dynamic-title' ) );	
			
		}
		
	});
		
});

function opChangeElementListTitle( element ) {

	jQuery( element ).closest( '.op-group' ).find( '.op-group-title' ).html( '<i class="fa fa-arrows"></i> ' + jQuery( element ).val() );

}

function opCreateChangeElementListTitleEvents( inputName ) {

	jQuery( document ).on( 'keyup change', 'input[name*="[' + inputName + ']"]', function() {      

		opChangeElementListTitle( this );
	
	});
	
	jQuery( 'input[name*="[' + inputName + ']"]' ).each(function() {

		opChangeElementListTitle( this );
	
	});
	
}