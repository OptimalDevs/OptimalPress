"use strict";

jQuery( window ).load(function() {
	
	jQuery( '.op-single.optimalpress-chosen-select' ).chosen({disable_search_threshold: 10});
	
	jQuery( '.op-group:not(.to-copy) .optimalpress-chosen-select' ).chosen({disable_search_threshold: 10});
	
	jQuery( 'body' ).on( 'op-addnew-group-element', function () {

		jQuery( '.op-group:not(.to-copy) .optimalpress-chosen-select' ).chosen({disable_search_threshold: 10});

	});
	
});