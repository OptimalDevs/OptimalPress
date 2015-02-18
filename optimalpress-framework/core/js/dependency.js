/*
 * Dependencies.
 *
 * Iterate all dependencies and creates events to show or hide each control.
 *
 * @param Array 	opDependencies	The array of dependencies.
 */
function opInitDependency( opDependencies ) {
	
	for( var i = 0; i < opDependencies.length; i++ ) {

		var opDependency	= opDependencies[i];
		
		for( var j = 0; j < opDependency.dependencies.length; j++ ) {
			
			var dependency = opDependency.dependencies[j];
			
			//Dependencies for Shortcode Generator
			if( dependency.shortcode_group ) {
				
				var dependsOfField	= jQuery( '#' + opDependency.id + ' .op-sc-group-' + dependency.shortcode_group + ' .op-input.' + dependency.depends_of );
				var opField			= jQuery( '#' + opDependency.id + ' .op-sc-group-' + dependency.shortcode_group + ' .op-input.' + dependency.field ).parents('.op-field');
				opOnLoadDependency( dependsOfField, opField, dependency.values );
				OpCreateDependencyEvents( '#' + opDependency.id + ' .op-sc-group-' + dependency.shortcode_group + ' .op-input.' + dependency.depends_of, opField, dependency.values, false );
				
			}//Dependencies for Groups 
			else if( dependency.group ) { 
			
				var dependsOfFields	= jQuery( '#' + opDependency.id + ' .op_group-' + dependency.group + ' .' + dependency.group + '-' + dependency.depends_of );
				var txt = dependency.group + '-' + dependency.depends_of;
				var depValues	= dependency.values;
				var opFields	= jQuery( '#' + opDependency.id + ' .op_loop-' + dependency.group + ' .' + dependency.group + '-' + dependency.field ).parents('.op-field');
	
				OpCreateDependencyEvents( '#' + opDependency.id + ' .op_loop-' + dependency.group + ' .' + dependency.group + '-' + dependency.depends_of, opFields, dependency.values, '.' + dependency.group + '-' + dependency.field );

				opFields.each( function() {

					opOnLoadDependency( jQuery(this).parent('.op-controls').find('.'+txt), jQuery(this), depValues);
				
				});
				
			} //Normal dependencies
			else { 

				var dependsOfField	= jQuery( '#' + opDependency.id + ' input[name*="' + dependency.depends_of  + '"], #' + opDependency.id + ' select[name*="' + dependency.depends_of  + '"],' + ' #' + opDependency.id + ' textarea[name*="' + dependency.depends_of  + '"]' );
				var opField			= jQuery( '#' + opDependency.id + ' #field-' + dependency.field );
				opOnLoadDependency( dependsOfField, opField, dependency.values );
				
				OpCreateDependencyEvents(  'input[name*="' + dependency.depends_of  + '"], select[name*="' + dependency.depends_of  + '"], textarea[name*="' + dependency.depends_of  + '"]', opField, dependency.values, false );
			
			} 
		}
	}

}

/*
 * Dependencies OnLoad
 *
 * Performs dependencies on page load.
 *
 * @param jQueryObject 	dependsOfField		Dependent field.
 * @param jQueryObject	opField				Field to show or hide.
 * @param mixed 		dependencyValues	Value that must be checked to display or not the field evaluated.
 */
function opOnLoadDependency( dependsOfField, opField, dependencyValues ){

	var fieldValues 	= '';
	
	switch( dependsOfField.attr('type') ){
	
		case 'checkbox':
		case 'radio':
			
			fieldValues = dependsOfField.is(':checked');

			if( dependsOfField.length > 1 ) {	

				fieldValues = [];	
				
				dependsOfField.each( function(){
				
					if( jQuery(this).is(':checked') ){
					
						fieldValues.push( jQuery(this).val() );
						
					}
					
				});
				
			}

			break;
			
		default:
			
			fieldValues = dependsOfField.val();
			if( ! fieldValues ){
				fieldValues = '';
			}
			break;
	}
		
	//If the dependent field is an array.
	
	if ( jQuery.isArray( fieldValues ) ) {
		
		//If the value of the dependencies is an array
		if ( jQuery.isArray( dependencyValues ) ) {
			
			opField.hide();

			for( var k=0; k < dependencyValues.length; k++ ) {

				if( fieldValues.indexOf( dependencyValues[k] ) >= 0 ) {
					opField.show();
				}
				
			}
		//If the value of the dependencies isn't an array (single value)
		}else{
		
			if( ( fieldValues.indexOf( dependencyValues ) < 0 && dependencyValues != '{{not-empty}}' ) || ( fieldValues.length == 0 && dependencyValues == '{{not-empty}}' )  ) {
				opField.hide();
			}
			
		}
		
	}
	//If the dependent field is a single value.
	else{
		
		//If the value of the dependencies is an array
		if ( jQuery.isArray( dependencyValues ) ) {
		
			var sw = 0;
			
			if( dependencyValues.indexOf( fieldValues ) >=0 ) {
				sw = 1;
			}
			
			if ( sw == 0){
				opField.hide();
			}else{
				opField.show();
			}
			
			
		}
		//If the value of the dependencies isn't an array (single value)
		else{

			if( ( fieldValues != dependencyValues && dependencyValues != '{{not-empty}}' ) || ( fieldValues.length == 0 && dependencyValues == '{{not-empty}}' ) ) {
				opField.hide();
			}
			
		}
	}
	
}

/*
 * Dependencies onChange
 *
 * Performs dependencies when a change occurs in any of the fields that it depends.
 *
 * @param jQueryObject 	dependsOfField		Dependent field.
 * @param jQueryObject	field				Field to show or hide.
 * @param mixed 		dependencyValue		Value that must be checked to display or not the field evaluated.
 * @param bool 			group				Indicates whether you are evaluating a dependency of a group or if a singular field.
 */
function OpCreateDependencyEvents( dependsOfField, field, dependencyValue, group ){

	jQuery(document).on( 'change', dependsOfField, function(){

		if( group ){
			
			field = jQuery(this).parents('.op-group').find( group ).parents('.op-field');
		
		}
	
		var fieldValues = '';
		
		switch( jQuery(this).attr('type') ){
	
			case 'checkbox':
				fieldValues = jQuery(this).is(':checked');

				if( jQuery(this).parents('.input').find('.op-input').length > 1 ) {	

					fieldValues = [];	
					
					jQuery(this).parents('.input').find('.op-input').each( function(){
						if( jQuery(this).is(':checked') ){

							fieldValues.push(jQuery(this).val());
						}
					});
					
				}
			
				break;
				
			default:
				fieldValues = jQuery(this).val();
				if( ! fieldValues ){
					fieldValues = '';
				}
				
				break;
				
		}
				
		//If the dependent field is an array.

		if ( jQuery.isArray( fieldValues ) ) {
		
			//If the value of the dependencies is an array
			if ( jQuery.isArray( dependencyValue ) ) {
				
				var sw = 0;
				
				for( var k=0; k < dependencyValue.length; k++ ) {
	
					if( fieldValues.indexOf( dependencyValue[k] ) >= 0 ) {
						sw = 1;
					}
					
				}
				
				if ( sw == 0){
					field.hide('slow');
				}else{
					field.show('slow');
				}
				
			}
			
			//If the value of the dependencies isn't an array (single value)
			else{
			
				if( fieldValues.indexOf( dependencyValue ) >= 0 || ( fieldValues.length > 0 && dependencyValue == '{{not-empty}}' ) ) {
					field.show('slow');
				}else{
					field.hide('slow');
				}
			}
		
		}
		//If the dependent field is a single value.
		else{
			
			//If the value of the dependencies is an array
			if ( jQuery.isArray( dependencyValue ) ) {
				
				if( dependencyValue.indexOf( fieldValues ) >=0 || ( fieldValues.length == 0 && dependencyValue == '{{not-empty}}' ) ) {
					field.show('slow');
				}else{
					field.hide('slow');
				}
				
			}
			//If the value of the dependencies isn't an array (single value)
			else{
				
				if( fieldValues == dependencyValue || ( fieldValues.length > 0 && dependencyValue == '{{not-empty}}' ) ){
					field.show('slow');
				}else{
					field.hide('slow');
				}
				
			}
					
		}

	});
}
