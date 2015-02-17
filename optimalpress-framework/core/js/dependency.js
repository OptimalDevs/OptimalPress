/*
 * Dependencies
 *
 * Recorre todas las dependencias y crea los eventos necesarios para
 * mostrar u ocultar cada control
 *
 * @param Array 	opDependencies	The array of dependencies
 *
 */
function opInitDependency( opDependencies ) {
	
	for( var i = 0; i < opDependencies.length; i++ ) {

		var opDependency	= opDependencies[i];
		
		for( var j = 0; j < opDependency.dependencies.length; j++ ) {
			
			var dependency = opDependency.dependencies[j];
			
			//Dependencias para Shortcodes
			if( dependency.shortcode_group ) {
				
				var dependsOfField	= jQuery( '#' + opDependency.id + ' .op-sc-group-' + dependency.shortcode_group + ' .op-input.' + dependency.depends_of );
				var opField			= jQuery( '#' + opDependency.id + ' .op-sc-group-' + dependency.shortcode_group + ' .op-input.' + dependency.field ).parents('.op-field');
				opOnLoadDependency( dependsOfField, opField, dependency.values );
				OpCreateDependencyEvents( '#' + opDependency.id + ' .op-sc-group-' + dependency.shortcode_group + ' .op-input.' + dependency.depends_of, opField, dependency.values, false );
				
			}//Dependencias para grupos 
			else if( dependency.group ) { 
			
				var dependsOfFields	= jQuery( '#' + opDependency.id + ' .op_group-' + dependency.group + ' .' + dependency.group + '-' + dependency.depends_of );
				var txt = dependency.group + '-' + dependency.depends_of;
				var depValues	= dependency.values;
				var opFields	= jQuery( '#' + opDependency.id + ' .op_loop-' + dependency.group + ' .' + dependency.group + '-' + dependency.field ).parents('.op-field');
	
				OpCreateDependencyEvents( '#' + opDependency.id + ' .op_loop-' + dependency.group + ' .' + dependency.group + '-' + dependency.depends_of, opFields, dependency.values, '.' + dependency.group + '-' + dependency.field );

				opFields.each( function() {

					opOnLoadDependency( jQuery(this).parent('.op-controls').find('.'+txt), jQuery(this), depValues);
				
				});
				
				
			} //Dependencias normales
			else { 

				var dependsOfField	= jQuery( 'input[name*="' + dependency.depends_of  + '"], select[name*="' + dependency.depends_of  + '"], textarea[name*="' + dependency.depends_of  + '"]' );
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
 * Efectua las dependencias al cargar la página.
 *
 * @param jQueryObject 	dependsOfField		Campo del que depende
 * @param jQueryObject	opField				Campo da mostrar u ocultar
 * @param mixed 		dependencyValues	Valor por el que se tiene que comprobar para mostrar o no el campo evaluado
 *
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
	
	
	//Si el campo del que depende es un array
	
	if ( jQuery.isArray( fieldValues ) ) {
		
		//Si los valores de las dependencias son arrays
		if ( jQuery.isArray( dependencyValues ) ) {
			
			opField.hide();

			for( var k=0; k < dependencyValues.length; k++ ) {

				if( fieldValues.indexOf( dependencyValues[k] ) >= 0 ) {
					opField.show();
				}
				
			}
		//Si el valor de las dependencias no son arrays	
		}else{
		
			if( ( fieldValues.indexOf( dependencyValues ) < 0 && dependencyValues != '{{not-empty}}' ) || ( fieldValues.length == 0 && dependencyValues == '{{not-empty}}' )  ) {
				opField.hide();
			}
			
		}
		
	}
	//Si el campo del que depende es un valor single
	else{
		
		//Si los valores de las dependencias son arrays
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
		//Si el valor de las dependencias es un valor single
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
 * Efectua las dependencias cuando se produce un evento en alguno de los campos de los que se depende.
 *
 * @param jQueryObject 	dependsOfField		Campo del que depende
 * @param jQueryObject	field				Campo da mostrar u ocultar
 * @param mixed 		dependencyValue		Valor por el que se tiene que comprobar para mostrar o no el campo evaluado
 * @param bool 			group				Indica si se esta evaluando una dependencia de un grupo o si es de un campo singular.
 *
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
		
		
		//Si el campo del que depende es un array

		if ( jQuery.isArray( fieldValues ) ) {
		
			//Si las dependencias es un array
			if ( jQuery.isArray( dependencyValue ) ) {
				
				var sw = 0;
				
				for( var k=0; k < dependencyValue.length; k++ ) {
	
					if( fieldValues.indexOf( dependencyValue[k] ) >= 0 ) {
						sw = 1;
					}
					
				}
				
				if ( sw == 0){
					field.hide('slow');
					console.log("no-(array-array)");
				}else{
					field.show('slow');
					console.log("si-(array-array)");
				}
				
			}
			
			//Si las dependencias es un valor single
			else{
			
				if( fieldValues.indexOf( dependencyValue ) >= 0 || ( fieldValues.length > 0 && dependencyValue == '{{not-empty}}' ) ) {
					field.show('slow');
					console.log("si-(array-normal)");
				}else{
					field.hide('slow');
					console.log("no-(array-normal)");
					
				}
			}
		
		}
		//Si el campo del que depende es un valor single
		else{
			
			//Si los valores de las dependencias son arrays
			if ( jQuery.isArray( dependencyValue ) ) {
				
				if( dependencyValue.indexOf( fieldValues ) >=0 || ( fieldValues.length == 0 && dependencyValue == '{{not-empty}}' ) ) {
					field.show('slow');
					console.log("si-(normal-array)");
				}else{
					field.hide('slow');
					console.log("no-(normal-array)");
				}
				
			}
			//Si el valor de las dependencias no son arrays	
			else{
				
				if( fieldValues == dependencyValue || ( fieldValues.length > 0 && dependencyValue == '{{not-empty}}' ) ){
					field.show('slow');
					console.log("si-(normal-normal)");
				}else{
					field.hide('slow');
				
					console.log("no-(normal-normal)");
				}
				
			}
			
			
		
		}

		
	});
}
