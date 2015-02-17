"use strict";

jQuery( window ).load(function() {
	
	jQuery( '.op-single.optimalpress-colorpicker' ).wpColorPicker();
	
	jQuery( '.op-group:not(.to-copy) .optimalpress-colorpicker' ).wpColorPicker();
	
	jQuery( 'body' ).on( 'op-addnew-group-element', function () {

		jQuery( '.op-group:not(.to-copy) .optimalpress-colorpicker' ).wpColorPicker();

	});
	
});