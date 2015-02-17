"use strict";

jQuery( window ).load(function() {
								
	jQuery( document ).on( 'click', '.op-control-checkimage .op-image-item', function(){
	
		jQuery(this).prev('input').toggleClass('checked');
		
	});
	
	jQuery('.op-control-checkimage .op-image-item').tipsy();

});

