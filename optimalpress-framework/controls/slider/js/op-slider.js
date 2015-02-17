"use strict";

jQuery( window ).load(function() {

	opLoadSliderScript();
	
	jQuery( 'body' ).on( 'op-addnew-group-element', function(){
		opLoadSliderScript();
	});
		
	jQuery( document ).on( 'keyup', '.op-input.slideinput', function () {

		jQuery(this).next().slider( 'value', parseInt(this.value));

	});
	
	jQuery( document ).on( 'change', '.op-input.slideinput', function () {
		
		var min = jQuery(this).next().data('min');
		var max = jQuery(this).next().data('max');
		var value = this.value;
		if( value < min ) {
			value = min;
		}
		if( value > max ) {
			value = max;
		}

		jQuery(this).next().slider("value", parseInt(value));
		jQuery(this).val( value );
		
	});
	
});

function opLoadSliderScript(){

	jQuery( '.op-group:not(.to-copy) .slideinput' ).each( function(){
		
		var opSlider = jQuery( this ).next();
		opSlider.slider({
			step: Number(opSlider.attr('data-step')),
			range: 'min',
			min: Number(opSlider.attr('data-min')),
			value: Number(opSlider.attr('data-value')),
			max: Number(opSlider.attr('data-max')),
			slide: function( event, ui ) {
				jQuery(this).prev().val( ui.value );
				//jQuery(this).prev().trigger('change');
			}
		});
		
		jQuery(this).val(opSlider.attr('data-value'));

	});
	
	jQuery( '.op-single.slideinput' ).each( function(){
		
		var opSlider = jQuery( this ).next();
		
		opSlider.slider({
			step: Number(opSlider.attr('data-step')),
			range: 'min',
			min: Number(opSlider.attr('data-min')),
			value: Number(opSlider.attr('data-value')),
			max: Number(opSlider.attr('data-max')),
			slide: function( event, ui ) {
				jQuery(this).prev().val( ui.value );
				//jQuery(this).prev().trigger('change');
			}
		});
		
		jQuery(this).val(opSlider.attr('data-value'));

	});
	
}