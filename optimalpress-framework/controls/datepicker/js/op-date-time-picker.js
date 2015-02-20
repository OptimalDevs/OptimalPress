"use strict";

jQuery( window ).load(function() {

	opLoadDatePickerScript();
	
	jQuery( 'body' ).on( 'op-addnew-group-element', function(){
		opLoadDatePickerScript();
	});
	
	
});

function opLoadDatePickerScript(){
	jQuery( '.optimalpress-datepicker' ).each( function(){
	
		jQuery( this ).datetimepicker({
			format : jQuery( this ).data( 'op-format' ),
			timepicker : jQuery( this ).data( 'op-timepicker' ),
			
		});
	
	});
	
}